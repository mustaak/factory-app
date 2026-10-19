<?php

namespace App\Livewire\Storefront;

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;

class HomePage extends Component
{
    public string $search = '';

    public function searchProducts()
    {
        return $this->redirectRoute('shop', [
            'search' => trim($this->search),
        ]);
    }

    public function render()
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->limit(8)
            ->get();

        $products = Product::published()
            ->with(['category', 'primaryImage', 'variants'])
            ->whereHas('variants', function ($query) {
                $query->where('is_active', true)
                    ->where('stock_quantity', '>', 0);
            })
            ->latest()
            ->limit(6)
            ->get();

        return view('livewire.storefront.home-page', [
            'categories' => $categories,
            'products' => $products,
        ])->layout('components.layouts.storefront');
    }
}
