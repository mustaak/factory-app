@php
    $categoryFallbacks = [
        'photo-1556742049-0cfed4f6a45d',
        'photo-1586023492125-27b2c045efd7',
        'photo-1498049794561-7780e7231661',
        'photo-1523275335684-37898b6baf30',
        'photo-1542291026-7eec264c27ff',
    ];

    $benefits = [
        ['Shipping', 'Calculated at checkout', 'M1 3h15v13H1zM16 8h4l3 3v5h-7V8zM5.5 21a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5zM18.5 21a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z'],
        ['Trusted vendors', 'Verified sellers only', 'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z'],
        ['Secure checkout', 'Your details stay safe', 'M5 11h14v10H5zM8 11V7a4 4 0 0 1 8 0v4'],
        ['Support', 'We are here to help', 'M3 14v-2a9 9 0 0 1 18 0v2M3 14h3v5H3zM18 14h3v5h-3z'],
    ];
@endphp

<div class="bg-white">

    {{-- Hero --}}
    <section class="bg-gradient-to-r from-slate-50 via-slate-50 to-white">
        <div class="mx-auto grid max-w-[1440px] items-center gap-10 px-4 py-14 sm:px-8 md:grid-cols-2 md:py-20 lg:py-24">

            <div class="max-w-xl">
                <p class="flex items-center gap-3 text-xs font-bold uppercase tracking-[0.2em] text-red-600">
                    <span class="h-px w-10 bg-red-600"></span>
                    New arrivals
                </p>

                <h1 class="mt-5 text-4xl font-extrabold leading-[1.05] tracking-tight text-slate-950 sm:text-5xl lg:text-6xl">
                    Quality products,<br>
                    from trusted vendors<span class="text-red-600">.</span>
                </h1>

                <p class="mt-6 max-w-md text-base leading-7 text-slate-600">
                    Discover top-rated products, great prices and a seamless shopping experience, all in one place.
                </p>

                <div class="mt-9 flex flex-wrap items-center gap-6">
                    <a href="{{ route('shop') }}"
                       class="cursor-pointer inline-flex items-center gap-3 rounded-lg bg-slate-950 px-7 py-4 text-sm font-semibold text-white shadow-lg shadow-slate-900/10 transition hover:bg-slate-800">
                        Shop all products
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </a>
                    <a href="#categories"
                       class="cursor-pointer inline-flex items-center gap-2 text-sm font-semibold text-slate-900 transition hover:text-red-600">
                        Browse categories
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </a>
                </div>
            </div>

            <div>
                <img src="https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=1400&q=85"
                     alt="Featured products"
                     class="h-[320px] w-full rounded-2xl object-cover shadow-sm sm:h-[420px] md:h-[460px]">
            </div>
        </div>
    </section>

    {{-- Benefits strip (overlaps hero) --}}
    <section class="mx-auto -mt-10 max-w-[1440px] px-4 sm:px-8">
        <div class="relative z-10 grid grid-cols-1 divide-y divide-slate-200 rounded-2xl border border-slate-200 bg-white shadow-sm sm:grid-cols-2 sm:divide-y-0 lg:grid-cols-4 lg:divide-x">
            @foreach ($benefits as [$title, $text, $path])
                <div class="flex items-center gap-4 px-6 py-6">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-red-50 text-red-600">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $path }}"/></svg>
                    </span>
                    <div>
                        <p class="text-sm font-semibold text-slate-900">{{ $title }}</p>
                        <p class="mt-0.5 text-xs text-slate-500">{{ $text }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Categories --}}
    <section id="categories" class="mx-auto max-w-[1440px] px-4 py-16 sm:px-8 sm:py-20">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h2 class="text-3xl font-bold tracking-tight text-slate-950">Shop by category</h2>
                <p class="mt-2 text-sm text-slate-500">Find what you need, faster. Explore our top categories.</p>
            </div>
            <a href="{{ route('shop') }}" class="cursor-pointer inline-flex items-center gap-2 text-sm font-semibold text-slate-900 hover:text-red-600">
                View all categories
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </a>
        </div>

        @if ($categories->isNotEmpty())
            <div class="mt-10 grid grid-cols-2 gap-5 lg:grid-cols-5">
                @foreach ($categories->take(5) as $index => $category)
                    <a href="{{ route('categories.show', ['slug' => $category->slug]) }}"
                       wire:key="home-cat-{{ $category->id }}"
                       class="cursor-pointer group flex flex-col overflow-hidden rounded-xl border border-slate-200 bg-white transition hover:-translate-y-1 hover:shadow-lg">

                        <div class="relative aspect-[4/3] overflow-hidden bg-slate-100">
                            <img src="{{ $category->image_path
                                    ? \Illuminate\Support\Facades\Storage::url($category->image_path)
                                    : 'https://images.unsplash.com/' . $categoryFallbacks[$index % count($categoryFallbacks)] . '?auto=format&fit=crop&w=600&q=80' }}"
                                 alt="{{ $category->name }}"
                                 loading="lazy"
                                 class="h-full w-full object-cover transition duration-500 group-hover:scale-105">

                            @if ($index === 0)
                                <span class="absolute left-3 top-3 rounded-md bg-red-600 px-2 py-1 text-[11px] font-semibold text-white">Popular</span>
                            @endif
                        </div>

                        <div class="flex items-center justify-between gap-3 px-4 py-4">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-slate-900">{{ $category->name }}</p>
                                <p class="mt-0.5 truncate text-xs text-slate-500">Explore collection</p>
                            </div>
                            <svg class="h-4 w-4 shrink-0 text-slate-400 transition group-hover:translate-x-0.5 group-hover:text-slate-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="mt-10 rounded-xl border border-dashed border-slate-300 px-6 py-12 text-center">
                <p class="text-sm font-semibold text-slate-900">Categories coming soon</p>
                <p class="mt-1 text-sm text-slate-500">Active categories will appear here.</p>
            </div>
        @endif
    </section>

    {{-- Featured products --}}
    <section class="border-t border-slate-200 bg-slate-50">
        <div class="mx-auto max-w-[1440px] px-4 py-16 sm:px-8 sm:py-20">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight text-slate-950">Featured products</h2>
                    <p class="mt-2 text-sm text-slate-500">Available now, updated as stock changes.</p>
                </div>
                <a href="{{ route('shop') }}" class="cursor-pointer inline-flex items-center gap-2 text-sm font-semibold text-slate-900 hover:text-red-600">
                    Shop all
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </a>
            </div>

            @if ($products->isNotEmpty())
                <div class="mt-10 grid grid-cols-2 gap-x-5 gap-y-10 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                    @foreach ($products as $product)
                        @php
                            $variant = $product->variants
                                ->where('is_active', true)
                                ->where('stock_quantity', '>', 0)
                                ->sortBy('price')
                                ->first();

                            $image = $product->primaryImage?->image_path ?? $variant?->image_path;
                            $price = $variant?->price;
                            $compare = $variant?->compare_at_price;
                            $discount = $compare && $price !== null && $compare > $price
                                ? round((($compare - $price) / $compare) * 100)
                                : null;
                        @endphp

                        <a href="{{ route('products.show', ['slug' => $product->slug]) }}"
                           wire:key="home-product-{{ $product->id }}"
                           class="cursor-pointer group flex flex-col">

                            <div class="relative aspect-square overflow-hidden rounded-xl bg-white ring-1 ring-slate-200">
                                @if ($image)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($image) }}"
                                         alt="{{ $product->name }}" loading="lazy"
                                         class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                                @else
                                    <div class="flex h-full items-center justify-center text-sm text-slate-400">No image</div>
                                @endif

                                @if ($discount)
                                    <span class="absolute left-3 top-3 rounded-md bg-red-600 px-2 py-0.5 text-xs font-semibold text-white">{{ $discount }}% off</span>
                                @endif
                            </div>

                            <div class="mt-4 flex flex-1 flex-col">
                                @if ($product->category)
                                    <p class="text-xs text-slate-500">{{ $product->category->name }}</p>
                                @endif
                                <h3 class="mt-1 line-clamp-2 text-sm font-medium leading-5 text-slate-900 group-hover:underline">{{ $product->name }}</h3>

                                <div class="mt-auto flex items-baseline gap-2 pt-3">
                                    @if ($price !== null)
                                        <span class="text-sm font-semibold text-slate-950">${{ number_format((float) $price, 2) }}</span>
                                    @endif
                                    @if ($discount)
                                        <span class="text-xs text-slate-400 line-through">${{ number_format((float) $compare, 2) }}</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="mt-10 rounded-xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center">
                    <p class="text-sm font-semibold text-slate-900">Products coming soon</p>
                    <p class="mt-1 text-sm text-slate-500">Approved products with stock will appear here.</p>
                    <a href="{{ route('shop') }}"
                       class="cursor-pointer mt-6 inline-flex rounded-lg bg-slate-950 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                        Go to shop
                    </a>
                </div>
            @endif
        </div>
    </section>

    {{-- Closing CTA --}}
    <section class="mx-auto max-w-[1440px] px-4 py-16 sm:px-8 sm:py-20">
        <div class="flex flex-col items-start justify-between gap-6 rounded-2xl bg-slate-950 px-8 py-12 sm:flex-row sm:items-center sm:px-12">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-white sm:text-3xl">Find something you need today.</h2>
                <p class="mt-2 max-w-md text-sm leading-6 text-slate-400">
                    Search the full catalogue, filter by category and check stock before you order.
                </p>
            </div>
            <a href="{{ route('shop') }}"
               class="cursor-pointer inline-flex shrink-0 items-center gap-2 rounded-lg bg-white px-6 py-3 text-sm font-medium text-slate-950 transition hover:bg-slate-100">
                Browse the shop <span aria-hidden="true">→</span>
            </a>
        </div>
    </section>

</div>