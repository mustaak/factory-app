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

    public function selectColor(string $color): void
    {
        $candidates = $this->variants->where('color', $color);

        if ($candidates->isEmpty()) {
            return;
        }

        // Pehle wahi size, na mile to us color ka koi bhi available variant
        $match = $candidates->firstWhere('size', $this->selectedVariant?->size) ?? $candidates->first();

        $this->chooseVariant($match);
    }

    public function selectSize(string $size): void
    {
        $candidates = $this->variants->where('size', $size);

        if ($candidates->isEmpty()) {
            return;
        }

        // Pehle wahi color, na mile to us size ka koi bhi available variant
        $match = $candidates->firstWhere('color', $this->selectedVariant?->color) ?? $candidates->first();

        $this->chooseVariant($match);
    }

    private function chooseVariant(ProductVariant $variant): void
    {
        $this->selectedVariantId = $variant->id;
        $this->quantity = max(1, min($this->quantity, $variant->stock_quantity));
        $this->resetErrorBag('cart');
    }

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

        if (! $variant) {
            $this->addError('cart', 'This product is currently unavailable.');

            return;
        }

        try {
            $cartService->add($variant->id, $this->quantity);
        } catch (ValidationException $exception) {
            $this->addError('cart', $exception->errors()['cart'][0] ?? 'Unable to add to cart.');

            return;
        }

        $this->dispatch('cart-message', message: $this->product->name . ' added to your cart.');
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