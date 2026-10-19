<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class CartService
{
    private const CART_KEY = 'factory_cart';

    public function items(): array
    {
        return Session::get(self::CART_KEY, []);
    }

    public function count(): int
    {
        return collect($this->items())->sum('quantity');
    }

    public function subtotal(): float
    {
        return (float) collect($this->items())->sum(
            fn (array $item) => $item['price'] * $item['quantity']
        );
    }

    public function add(Product $product, int $variantId, int $quantity = 1): void
    {
        if ($quantity < 1) {
            throw ValidationException::withMessages([
                'quantity' => 'Quantity must be at least 1.',
            ]);
        }

        $variant = ProductVariant::query()
            ->where('product_id', $product->id)
            ->whereKey($variantId)
            ->where('is_active', 1)
            ->first();
        $productId = $product->id;

        if (! $variant || $variant->stock_quantity < 1) {
            throw ValidationException::withMessages([
                'cart' => 'This product variant is unavailable.',
            ]);
        }

        $cart = $this->items();
        $key = (string) $variant->id;
        $newQuantity = ($cart[$key]['quantity'] ?? 0) + $quantity;

        if ($newQuantity > $variant->stock_quantity) {
            throw ValidationException::withMessages([
                'cart' => 'Requested quantity exceeds available stock.',
            ]);
        }

        $cart[$key] = [
            'product_id' => $product->id,
            'variant_id' => $variant->id,
            'name' => $product->name,
            'sku' => $variant->sku,
            'size' => $variant->size,
            'color' => $variant->color,
            'price' => (float) $variant->price,
            'quantity' => $newQuantity,
            'image_path' => $variant->image_path
            ?: $product->primaryImage?->image_path,
        ];

        Session::put(self::CART_KEY, $cart);
    }

    public function update(int $variantId, int $quantity): void
    {
        $cart = $this->items();
        $key = (string) $variantId;

        if (! isset($cart[$key])) {
            return;
        }

        if ($quantity < 1) {
            $this->remove($variantId);
            return;
        }

        $variant = ProductVariant::query()
            ->whereKey($variantId)
            ->where('product_id', $cart[$key]['product_id'])
            ->where('is_active', true)
            ->first();

        if (! $variant || $quantity > $variant->stock_quantity) {
            throw ValidationException::withMessages([
                'cart' => 'Requested quantity is not available.',
            ]);
        }

        $cart[$key]['quantity'] = $quantity;

        // Refresh price from the database.
        $cart[$key]['price'] = (float) $variant->price;

        Session::put(self::CART_KEY, $cart);
    }

    public function remove(int $variantId): void
    {
        $cart = $this->items();

        unset($cart[(string) $variantId]);

        Session::put(self::CART_KEY, $cart);
    }

    public function clear(): void
    {
        Session::forget(self::CART_KEY);
    }
}
