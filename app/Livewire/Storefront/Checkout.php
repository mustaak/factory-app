<?php

namespace App\Livewire\Storefront;

use App\Models\Address;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Checkout extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $address_line1 = '';
    public string $address_line2 = '';
    public string $city = '';
    public string $state = '';
    public string $postal_code = '';
    public string $country = 'IN';
    public string $notes = '';
    public string $paymentMethod = 'cod';

    public ?int $selectedAddressId = null;
    public bool $useNewAddress = false;

    public function mount(CartService $cartService): void
    {
        if (empty($cartService->items())) {
            $this->redirectRoute('cart');
            return;
        }

        if (Auth::check()) {
            $user = Auth::user();
            $this->name = $user->name;
            $this->email = $user->email;

            $default = Address::where('user_id', $user->id)
                ->orderByDesc('is_default')
                ->first();

            $this->selectedAddressId = $default?->id;
            $this->useNewAddress = ! $default;
        } else {
            $this->useNewAddress = true;
        }
    }

    public function selectAddress(int $id): void
    {
        $address = Address::where('user_id', Auth::id())->findOrFail($id);

        $this->selectedAddressId = $address->id;
        $this->useNewAddress = false;
        $this->resetValidation();
    }

    public function useNewAddressForm(): void
    {
        $this->selectedAddressId = null;
        $this->useNewAddress = true;
    }

    protected function rules(): array
    {
        $base = [
            'notes' => ['nullable', 'string', 'max:500'],
            'paymentMethod' => ['required', 'in:cod'],
        ];

        if (! Auth::check()) {
            $base += [
                'name' => ['required', 'string', 'max:120'],
                'email' => ['required', 'email', 'max:255'],
            ];
        }

        if ($this->usingSavedAddress()) {
            return $base;
        }

        return $base + [
            'phone' => ['required', 'digits:10'],
            'address_line1' => ['required', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'digits:6'],
            'country' => ['required', 'size:2'],
        ];
    }

    public function placeOrder(CartService $cartService)
    {
        $this->validate();

        $items = collect($cartService->items());

        if ($items->isEmpty()) {
            return $this->redirectRoute('cart');
        }

        // Guest ke liye pehle check: email kisi account ka to nahi
        if (Auth::guest() && User::where('email', $this->email)->exists()) {
            $this->addError('email', 'An account with this email already exists. Please sign in to continue.');

            return;
        }

        $address = $this->resolveAddress();

        try {
            $order = DB::transaction(function () use ($items, $address) {
                $variants = ProductVariant::query()
                    ->with('product')
                    ->whereIn('id', $items->pluck('variant_id'))
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                $subtotal = 0;
                $pending = [];

                foreach ($items as $item) {
                    $variant = $variants->get((int) $item['variant_id']);

                    if (! $variant || ! $variant->is_active || ! $variant->product || $variant->product->trashed()) {
                        throw ValidationException::withMessages([
                            'cart' => 'Some items are no longer available. Please review your cart.',
                        ]);
                    }

                    $quantity = (int) $item['quantity'];

                    if ($quantity < 1 || $quantity > $variant->stock_quantity) {
                        throw ValidationException::withMessages([
                            'cart' => "Not enough stock for {$variant->product->name} (available: {$variant->stock_quantity}).",
                        ]);
                    }

                    $price = (float) $variant->price;
                    $lineTotal = $price * $quantity;
                    $subtotal += $lineTotal;

                    $pending[] = [
                        'variant' => $variant,
                        'data' => [
                            'product_variant_id' => $variant->id,
                            'product_name' => $variant->product->name,
                            'size' => $variant->size,
                            'color' => $variant->color,
                            'sku' => $variant->sku,
                            'price' => $price,
                            'quantity' => $quantity,
                            'line_total' => $lineTotal,
                        ],
                    ];
                }

                $order = Order::create([
                    'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                    'user_id' => Auth::id(),
                    'name' => $this->name,
                    'email' => $this->email,
                    'phone' => $address['phone'],
                    'address_line1' => $address['address_line1'],
                    'address_line2' => $address['address_line2'],
                    'city' => $address['city'],
                    'state' => $address['state'],
                    'postal_code' => $address['postal_code'],
                    'country' => $address['country'],
                    'notes' => $this->notes ?: null,
                    'payment_method' => $this->paymentMethod,
                    'payment_status' => 'pending',
                    'status' => 'pending',
                    'subtotal' => $subtotal,
                    'total' => $subtotal,
                ]);

                foreach ($pending as $line) {
                    $order->items()->create($line['data']);
                    $line['variant']->decrement('stock_quantity', $line['data']['quantity']);
                }

                return $order;
            });
            
            if (Auth::check()) {
                $this->saveNewAddressForUser(Auth::user());
            }
        } catch (ValidationException $exception) {
            $this->addError('cart', $exception->errors()['cart'][0] ?? 'Could not place your order.');

            return;
        }

        if (Auth::guest()) {
            $redirectUrl = $this->handleGuestAccount($order);
            $cartService->clear();

            return $this->redirect($redirectUrl, navigate: false);
        }

        $cartService->clear();

        return $this->redirectRoute('checkout.success', ['order' => $order->order_number]);
    }

    public function render(CartService $cartService)
    {
        $items = collect($cartService->items());

        $variants = ProductVariant::query()
            ->with('product.primaryImage')
            ->whereIn('id', $items->pluck('variant_id'))
            ->get()
            ->keyBy('id');

        $cartItems = $items->map(function (array $item, string $key) use ($variants) {
            $variant = $variants->get((int) $key);

            if (! $variant || ! $variant->is_active || ! $variant->product || $variant->product->trashed()) {
                return null;
            }

            $item['name'] = $variant->product->name;
            $item['price'] = (float) $variant->price;
            $item['size'] = $variant->size;
            $item['color'] = $variant->color;
            $item['line_total'] = $item['price'] * $item['quantity'];

            return $item;
        })->filter()->values();

        $savedAddresses = Auth::check()
            ? Address::where('user_id', Auth::id())->orderByDesc('is_default')->get()
            : collect();

        return view('livewire.storefront.checkout', [
            'cartItems' => $cartItems,
            'subtotal' => $cartItems->sum('line_total'),
            'cartCount' => $cartItems->sum('quantity'),
            'savedAddresses' => $savedAddresses,
        ])->layout('components.layouts.storefront');
    }

    /**
     * Guest ke liye account banata hai, order ko usse link karta hai aur login karata hai.
     * User ko password set karne ke liye bheja jata hai.
     */
    private function handleGuestAccount(Order $order): string
    {
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Str::random(40), // random, user baad mein set karega
            'needs_password' => true,
        ]);

        $this->saveNewAddressForUser($user);

        $order->update(['user_id' => $user->id]);

        Auth::login($user);
        session()->regenerate();
        session(['last_order_number' => $order->order_number]);

        return route('account.password.set');
    }

    private function usingSavedAddress(): bool
    {
        return Auth::check() && ! $this->useNewAddress && $this->selectedAddressId !== null;
    }

    /**
     * Address ka snapshot. Saved address order mein copy hota hai,
     * taaki baad mein address edit karne se purane order par asar na pade.
     */
    private function resolveAddress(): array
    {
        if ($this->usingSavedAddress()) {
            $address = Address::where('user_id', Auth::id())->findOrFail($this->selectedAddressId);

            return [
                'phone' => $address->phone,
                'address_line1' => $address->address_line1,
                'address_line2' => $address->address_line2,
                'city' => $address->city,
                'state' => $address->state,
                'postal_code' => $address->postal_code,
                'country' => $address->country,
            ];
        }

        return [
            'phone' => $this->phone,
            'address_line1' => $this->address_line1,
            'address_line2' => $this->address_line2 ?: null,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'country' => strtoupper($this->country),
        ];
    }

    private function saveNewAddressForUser(User $user): void
    {
        if ($this->usingSavedAddress() || ! $this->address_line1) {
            return;
        }

        // Same address dobara save na ho, isliye check karte hain
        $exists = Address::where('user_id', $user->id)
            ->where('address_line1', $this->address_line1)
            ->where('postal_code', $this->postal_code)
            ->where('phone', $this->phone)
            ->exists();

        if ($exists) {
            return;
        }

        $hasAny = Address::where('user_id', $user->id)->exists();

        Address::create([
            'user_id' => $user->id,
            'label' => null,
            'name' => $this->name,
            'phone' => $this->phone,
            'address_line1' => $this->address_line1,
            'address_line2' => $this->address_line2 ?: null,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'country' => strtoupper($this->country),
            'is_default' => ! $hasAny,
        ]);
    }
}