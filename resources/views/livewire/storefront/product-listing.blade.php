<div class="min-h-screen bg-white">

    {{-- Hero --}}
    <section class="relative overflow-hidden border-b border-slate-200 bg-gradient-to-r from-slate-50 to-slate-100">
        <div class="mx-auto grid max-w-[1440px] items-center gap-8 px-4 py-12 sm:px-8 md:grid-cols-2 md:py-16">
            <div>
                <nav class="flex items-center gap-2 text-sm text-slate-500">
                    <a href="{{ route('home') }}" class="cursor-pointer hover:text-slate-900">Home</a>
                    <span aria-hidden="true">/</span>
                    @if (request()->routeIs('categories.show'))
                        <a href="{{ route('shop') }}" class="cursor-pointer hover:text-slate-900">Shop</a>
                        <span aria-hidden="true">/</span>
                        <span class="font-medium text-slate-900">{{ $categories->firstWhere('slug', request()->route('slug'))?->name ?? 'Category' }}</span>
                    @else
                        <span class="font-medium text-slate-900">Shop</span>
                    @endif
                </nav>

                <h1 class="mt-4 text-4xl font-extrabold tracking-tight text-slate-950 sm:text-5xl">Shop Our Products</h1>
                <p class="mt-4 max-w-lg text-base leading-7 text-slate-600">
                    Discover top-quality products, great prices and a seamless shopping experience, all in one place.
                </p>
            </div>

            <div class="hidden md:block">
                <img src="https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=1200&q=85"
                     alt="Featured products"
                     class="h-56 w-full rounded-2xl object-cover shadow-sm lg:h-64">
            </div>
        </div>
    </section>

    <section class="mx-auto grid max-w-[1440px] grid-cols-1 items-start gap-8 px-4 py-8 sm:px-8 lg:grid-cols-[260px_1fr]">

        {{-- Filter sidebar --}}
        <aside class="rounded-xl border border-slate-200 bg-white p-5 lg:sticky lg:top-28">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-slate-950">Filter Products</h2>
                <button type="button" wire:click="clearFilters" class="cursor-pointer text-xs font-medium text-slate-500 hover:text-red-600">Clear all</button>
            </div>

            {{-- Search --}}
            <div class="mt-6">
                <label for="listing-search" class="mb-2 block text-sm font-semibold text-slate-900">Search</label>
                <input id="listing-search" type="search" wire:model.live.debounce.400ms="search" placeholder="Product or brand"
                       class="w-full rounded-lg border-slate-300 px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-slate-900 focus:ring-slate-900">
            </div>

            {{-- Category (real filter) --}}
            <div class="mt-6 border-t border-slate-200 pt-5">
                <label for="listing-category" class="mb-2 block text-sm font-semibold text-slate-900">Category</label>
                <select id="listing-category" wire:model.live="category"
                        class="w-full rounded-lg border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-slate-900 focus:ring-slate-900">
                    <option value="">All categories</option>
                    @foreach ($categories as $item)
                        <option value="{{ $item->slug }}">{{ $item->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Price --}}
            <div class="mt-6 border-t border-slate-200 pt-5">
                <p class="text-sm font-semibold text-slate-900">Price range</p>
                <div class="mt-3 grid grid-cols-2 gap-3">
                    <div>
                        <label for="min-price" class="mb-1 block text-xs text-slate-500">Min (₹)</label>
                        <input id="min-price" type="number" min="0" step="0.01" wire:model.live.debounce.400ms="minPrice" placeholder="0"
                               class="w-full min-w-0 rounded-lg border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:ring-slate-900">
                    </div>
                    <div>
                        <label for="max-price" class="mb-1 block text-xs text-slate-500">Max (₹)</label>
                        <input id="max-price" type="number" min="0" step="0.01" wire:model.live.debounce.400ms="maxPrice" placeholder="Any"
                               class="w-full min-w-0 rounded-lg border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:ring-slate-900">
                    </div>
                </div>
            </div>

            {{-- Brand (visual, filter logic abhi component mein nahi) --}}
            <div class="mt-6 border-t border-slate-200 pt-5">
                <p class="text-sm font-semibold text-slate-900">Brand</p>
                <div class="mt-3 space-y-2.5 text-sm text-slate-700">
                    @foreach (['Nike', 'Adidas', 'Apple', 'Samsung'] as $brand)
                        <label class="flex cursor-pointer items-center gap-2.5">
                            <input type="checkbox" class="rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                            {{ $brand }}
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Size (visual) --}}
            <div class="mt-6 border-t border-slate-200 pt-5">
                <p class="text-sm font-semibold text-slate-900">Size</p>
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach (['XS', 'S', 'M', 'L', 'XL', 'XXL'] as $size)
                        <label class="cursor-pointer">
                            <input type="checkbox" class="peer sr-only">
                            <span class="inline-flex h-9 min-w-9 items-center justify-center rounded-md border border-slate-300 px-2.5 text-xs font-medium text-slate-700 transition peer-checked:border-slate-900 peer-checked:bg-slate-900 peer-checked:text-white hover:border-slate-500">
                                {{ $size }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
        </aside>

        {{-- Products --}}
        <div id="product-results" class="min-w-0">

            {{-- Toolbar --}}
            <div class="flex flex-col gap-4 border-b border-slate-200 pb-5 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <label for="listing-sort" class="text-sm text-slate-600">Sort by:</label>
                    <select id="listing-sort" wire:model.live="sort"
                            class="rounded-lg border-slate-300 py-2 pl-3 pr-8 text-sm font-medium text-slate-900 shadow-sm focus:border-slate-900 focus:ring-slate-900">
                        <option value="latest">Newest</option>
                        <option value="price_low">Price: low to high</option>
                        <option value="price_high">Price: high to low</option>
                    </select>
                </div>

                <p class="text-sm text-slate-600">
                    Showing <span class="font-semibold text-slate-900">{{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }}</span>
                    of <span class="font-semibold text-slate-900">{{ $products->total() }}</span> products
                </p>
            </div>

            @if ($products->isEmpty())
                <div class="mt-8 rounded-xl border border-dashed border-slate-300 px-6 py-16 text-center">
                    <p class="text-sm font-semibold text-slate-900">No products match your filters</p>
                    <p class="mx-auto mt-1 max-w-md text-sm text-slate-500">Try a different search term or clear the filters.</p>
                    <button type="button" wire:click="clearFilters"
                            class="cursor-pointer mt-6 inline-flex rounded-lg bg-red-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-red-700">
                        Clear filters
                    </button>
                </div>
            @else
                <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($products as $product)
                        @php
                            $availableVariants = $product->variants
                                ->where('is_active', true)
                                ->where('stock_quantity', '>', 0);

                            $lowestVariant = $availableVariants->sortBy('price')->first();
                            $lowestPrice = $lowestVariant?->price;
                            $comparePrice = $lowestVariant?->compare_at_price;

                            $discount = $comparePrice && $lowestPrice !== null && $comparePrice > $lowestPrice
                                ? round((($comparePrice - $lowestPrice) / $comparePrice) * 100)
                                : null;

                            $productImage = $product->primaryImage?->image_path ?? $lowestVariant?->image_path;
                            $productUrl = route('products.show', ['slug' => $product->slug]);
                        @endphp

                        <article wire:key="listing-product-{{ $product->id }}"
                                 class="group flex flex-col overflow-hidden rounded-xl border border-slate-200 bg-white transition hover:shadow-lg">

                            <div class="relative">
                                <a href="{{ $productUrl }}" class="cursor-pointer block aspect-[4/3] overflow-hidden bg-slate-50">
                                    @if ($productImage)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($productImage) }}"
                                             alt="{{ $product->name }}" loading="lazy"
                                             class="h-full w-full object-contain p-4 transition duration-500 group-hover:scale-105">
                                    @else
                                        <div class="flex h-full items-center justify-center text-sm text-slate-400">No image</div>
                                    @endif
                                </a>

                                @if ($discount)
                                    <span class="absolute left-3 top-3 rounded-md bg-red-600 px-2 py-0.5 text-xs font-semibold text-white">-{{ $discount }}%</span>
                                @endif

                                <span class="absolute right-3 top-3 flex h-8 w-8 items-center justify-center rounded-full bg-white text-slate-500 shadow-sm" aria-hidden="true">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.8-7.5 1.1-1.1a5.5 5.5 0 0 0-.1-7.8Z"/></svg>
                                </span>
                            </div>

                            <div class="flex flex-1 flex-col p-4">
                                @if ($product->category)
                                    <p class="text-xs text-slate-500">{{ $product->category->name }}</p>
                                @endif

                                <a href="{{ $productUrl }}" class="cursor-pointer mt-1 line-clamp-2 text-sm font-semibold leading-5 text-slate-900 hover:text-red-600">
                                    {{ $product->name }}
                                </a>

                                @if ($product->brand)
                                    <p class="mt-1 text-xs text-slate-500">{{ $product->brand }}</p>
                                @endif

                                <div class="mt-3 flex items-baseline gap-2">
                                    @if ($lowestPrice !== null)
                                        <span class="text-base font-bold text-red-600">₹{{ number_format((float) $lowestPrice, 2) }}</span>
                                        @if ($comparePrice && $comparePrice > $lowestPrice)
                                            <span class="text-xs text-slate-400 line-through">₹{{ number_format((float) $comparePrice, 2) }}</span>
                                        @endif
                                    @else
                                        <span class="text-sm font-medium text-slate-700">View details</span>
                                    @endif
                                </div>

                                <div class="mt-auto pt-4">
                                    @if ($lowestVariant)
                                        <a href="{{ $productUrl }}"
                                           class="cursor-pointer flex w-full items-center justify-center gap-2 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3h2l2.4 12.2a2 2 0 0 0 2 1.6h8.8a2 2 0 0 0 2-1.6L22 8H6"/><circle cx="10" cy="21" r="1"/><circle cx="18" cy="21" r="1"/></svg>
                                            Add to Cart
                                        </a>
                                    @else
                                        <span class="block w-full rounded-lg bg-slate-100 px-4 py-2.5 text-center text-sm font-medium text-slate-500">Out of stock</span>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-10">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </section>
</div>