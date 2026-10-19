<?php

namespace App\Livewire\Storefront;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ProductDetails extends Component
{
    public Product $product;
    public ?int $selectedVariantId = null;
    public int $quantity = 1;

    public function mount(string $slug): void
    {
        $this->product = Product::with(['variants', 'images', 'category', 'primaryImage'])
            ->where('slug', $slug)
            ->firstOrFail();

        $this->selectedVariantId = $this->variants->first()?->id;
    }

    /**
     * Sirf active aur stock wale variants.
     */
    #[Computed]
    public function variants()
    {
        return $this->product->variants
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->values();
    }

    #[Computed]
    public function selectedVariant(): ?ProductVariant
    {
        return $this->variants->firstWhere('id', $this->selectedVariantId)
            ?? $this->variants->first();
    }

    public function selectSize(string $size): void
    {
        $this->pickVariantBy('size', $size);
    }

    public function selectColor(string $color): void
    {
        $this->pickVariantBy('color', $color);
    }

    private function pickVariantBy(string $field, string $value): void
    {
        $target = strtolower(trim($value));
        $current = $this->selectedVariant;

        // Dusra attribute current selection se match karna chahiye
        $other = $field === 'color' ? 'size' : 'color';
        $currentOther = strtolower((string) $current?->{$other});

        $candidates = $this->variants->filter(
            fn ($v) => strtolower((string) $v->{$field}) === $target
        );

        if ($candidates->isEmpty()) {
            return;
        }

        $match = $candidates->first(
            fn ($v) => strtolower((string) $v->{$other}) === $currentOther
        ) ?? $candidates->first();

        if ($match->id === $this->selectedVariantId) {
            return;
        }

        $this->selectedVariantId = $match->id;

        $this->quantity = min(
            $this->quantity,
            max(1, $match->stock_quantity)
        );

        unset($this->selectedVariant);

        $imagePath = $match->image_path
            ?: $this->product->primaryImage?->image_path
            ?: $this->product->images->first()?->image_path;

        $imageUrl = $imagePath
            ? \Illuminate\Support\Facades\Storage::url($imagePath)
            : null;

        $this->dispatch('variant-image-updated', url: $imageUrl);
    }

    // private function chooseVariant(ProductVariant $variant): void
    // {
    //     $this->selectedVariantId = $variant->id;
    //     $this->quantity = max(1, min($this->quantity, $variant->stock_quantity));
    //     $this->resetErrorBag('cart');
    // }

    public function increaseQuantity(): void
    {
        $max = $this->selectedVariant?->stock_quantity ?? 1;
        $this->quantity = min($this->quantity + 1, $max);
    }

    public function decreaseQuantity(): void
    {
        $this->quantity = max(1, $this->quantity - 1);
    }

    
    public function addToCart(CartService $cartService): void
    {
        $variant = $this->selectedVariant;

        // dd([
        //     'product_id' => $this->product->id,
        //     'selected_variant_id' => $this->selectedVariantId,
        //     'variants_for_current_product' => $this->variants
        //         ->map(fn ($variant) => [
        //             'id' => $variant->id,
        //             'product_id' => $variant->product_id,
        //             'sku' => $variant->sku,
        //             'is_active' => $variant->is_active,
        //         ])
        //         ->toArray(),
        //     'selected_variant' => $variant?->toArray(),
        // ]);

        if (! $variant) {
            $this->addError('cart', 'This product is currently unavailable.');

            return;
        }

        try {
            $cartService->add(
                $this->product,
                $variant->id,
                $this->quantity
            );

            $cartQty = collect(session('factory_cart', []))
                ->sum('quantity');

            $this->dispatch('cart-updated', count: $cartQty);

            $this->dispatch(
                'cart-message',
                message: $this->product->name . ' added to your cart.'
            );
        } catch (ValidationException $exception) {
            $this->addError(
                'cart',
                $exception->errors()['cart'][0] ?? 'Unable to add to cart.'
            );

            return;
        }

        $this->dispatch(
            'cart-message',
            message: $this->product->name . ' added to your cart.'
        );
    }


    public function render()
    {
        $related = Product::query()
            ->with(['variants', 'primaryImage'])
            ->where('category_id', $this->product->category_id)
            ->where('id', '!=', $this->product->id)
            ->latest()
            ->take(4)
            ->get();

        return view('livewire.storefront.product-details', [
            'related' => $related,
        ])->layout('components.layouts.storefront', [
            'title' => $this->product->name . ' | Factory',
        ]);
    }
}
