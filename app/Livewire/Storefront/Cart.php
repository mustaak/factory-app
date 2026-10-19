<?php

namespace App\Livewire\Storefront;

use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Cart extends Component
{
    public function updateQuantity(
        int $variantId,
        int $quantity,
        CartService $cartService
    ): void {
        try {
            $cartService->update($variantId, $quantity);
        } catch (ValidationException $exception) {
            $this->addError(
                'cart',
                $exception->errors()['cart'][0] ?? 'Unable to update quantity.'
            );
        }
    }

    public function removeItem(int $variantId, CartService $cartService): void
    {
        $cartService->remove($variantId);

        $this->resetErrorBag('cart');
    }

    public function clearCart(CartService $cartService): void
    {
        $cartService->clear();

        $this->resetErrorBag('cart');
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

            if (! $variant || ! $variant->is_active) {
                return null;
            }

            $product = $variant->product;

            if (! $product || $product->trashed()) {
                return null;
            }

            // Current database price is authoritative.
            $item['name'] = $product->name;
            $item['price'] = (float) $variant->price;
            $item['image_path'] = $variant->image_path
                ?: $product->primaryImage?->image_path;
            $item['size'] = $variant->size;
            $item['color'] = $variant->color;
            $item['sku'] = $variant->sku;
            $item['stock_quantity'] = (int) $variant->stock_quantity;
            $item['line_total'] = $item['price'] * $item['quantity'];

            return $item;
        })->filter()->values();

        return view('livewire.storefront.cart', [
            'cartItems' => $cartItems,
            'subtotal' => $cartItems->sum('line_total'),
            'cartCount' => $cartItems->sum('quantity'),
        ])->layout('components.layouts.storefront');
    }
}