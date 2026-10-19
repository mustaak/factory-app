<?php

namespace App\Livewire\Storefront;

use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ProductListing extends Component
{
    use WithPagination;

    #[Url(as: 'search')]
    public string $search = '';

    #[Url(as: 'category')]
    public string $category = '';

    #[Url]
    public string $sort = 'latest';

    #[Url(as: 'min_price')]
    public string $minPrice = '';

    #[Url(as: 'max_price')]
    public string $maxPrice = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCategory(): void
    {
        $this->resetPage();
    }

    public function updatedSort(): void
    {
        $this->resetPage();
    }

    public function updatedMinPrice(): void
    {
        $this->resetPage();
    }

    public function updatedMaxPrice(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset([
            'search',
            'category',
            'sort',
            'minPrice',
            'maxPrice',
        ]);

        $this->resetPage();
    }

    public function render()
    {
        $products = Product::published()
            ->with([
                'primaryImage',
                'category',
                'variants',
            ])
            ->whereHas('variants', function ($query) {
                $query->where('is_active', true)
                    ->where('stock_quantity', '>', 0);
            })
            ->when(trim($this->search) !== '', function ($query) {
                $search = trim($this->search);

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('brand', 'like', "%{$search}%")
                        ->orWhere('short_description', 'like', "%{$search}%");
                });
            })
            ->when($this->category !== '', function ($query) {
                $query->whereHas('category', function ($q) {
                    $q->where('slug', $this->category)
                        ->where('is_active', true);
                });
            })
            ->when(
                is_numeric($this->minPrice) && $this->minPrice !== '',
                function ($query) {
                    $query->whereHas('variants', function ($q) {
                        $q->where('is_active', true)
                            ->where('stock_quantity', '>', 0)
                            ->where('price', '>=', (float) $this->minPrice);
                    });
                }
            )
            ->when(
                is_numeric($this->maxPrice) && $this->maxPrice !== '',
                function ($query) {
                    $query->whereHas('variants', function ($q) {
                        $q->where('is_active', true)
                            ->where('stock_quantity', '>', 0)
                            ->where('price', '<=', (float) $this->maxPrice);
                    });
                }
            );

        if ($this->sort === 'price_low') {
            $products->withMin([
                'variants as lowest_price' => function ($query) {
                    $query->where('is_active', true)
                        ->where('stock_quantity', '>', 0);
                },
            ], 'price')->orderBy('lowest_price');
        } elseif ($this->sort === 'price_high') {
            $products->withMin([
                'variants as lowest_price' => function ($query) {
                    $query->where('is_active', true)
                        ->where('stock_quantity', '>', 0);
                },
            ], 'price')->orderByDesc('lowest_price');
        } else {
            $products->latest();
        }

        return view('livewire.storefront.product-listing', [
            'products' => $products->paginate(12),
            'categories' => Category::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
        ])->layout('components.layouts.storefront');
    }
}
