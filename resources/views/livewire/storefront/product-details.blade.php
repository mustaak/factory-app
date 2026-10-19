@php
    $sel = $this->selectedVariant;

    $sizeOrder = ['xs' => 1, 's' => 2, 'm' => 3, 'l' => 4, 'xl' => 5, 'xxl' => 6, 'xxxl' => 7];

    $colors = $this->variants->pluck('color')->filter()->unique()->values();
    $sizes = $this->variants->pluck('size')->filter()->unique()
        ->sortBy(fn ($s) => $sizeOrder[strtolower($s)] ?? 99)->values();

    $sizesForColor = $this->variants->where('color', $sel?->color)->pluck('size')->filter()->all();

    $discount = $sel && $sel->compare_at_price > $sel->price
        ? round((($sel->compare_at_price - $sel->price) / $sel->compare_at_price) * 100)
        : 0;

    $mainImage = $product->primaryImage ?? $product->images->first();
    $mainUrl = $mainImage?->image_path ? \Illuminate\Support\Facades\Storage::url($mainImage->image_path) : null;
@endphp

<div class="min-h-screen bg-slate-50">
    <div class="mx-auto max-w-[1440px] px-4 py-6 sm:px-8">

        {{-- Breadcrumb --}}
        <nav class="mb-6 flex flex-wrap items-center gap-2 text-sm text-slate-500">
            <a href="{{ route('home') }}" class="cursor-pointer hover:text-slate-900">Home</a>
            <span aria-hidden="true">/</span>
            <a href="{{ route('shop') }}" class="cursor-pointer hover:text-slate-900">Shop</a>
            @if ($product->category)
                <span aria-hidden="true">/</span>
                <a href="{{ route('categories.show', $product->category->slug) }}" class="cursor-pointer hover:text-slate-900">{{ $product->category->name }}</a>
            @endif
            <span aria-hidden="true">/</span>
            <span class="max-w-60 truncate font-medium text-slate-900">{{ $product->name }}</span>
        </nav>

        {{-- Top section: gallery | purchase | trust --}}
        <div class="grid gap-8 lg:grid-cols-12">

            {{-- Gallery --}}
            <section class="lg:col-span-5" x-data="{ src: @js($mainUrl) }">
                <div class="flex flex-col-reverse gap-4 lg:flex-row">

                    {{-- Thumbnails --}}
                    <div class="flex gap-3 overflow-x-auto lg:w-20 lg:shrink-0 lg:flex-col lg:overflow-visible">
                        @foreach ($product->images as $image)
                            @php $thumb = \Illuminate\Support\Facades\Storage::url($image->image_path); @endphp
                            <button type="button" wire:key="thumb-{{ $image->id }}"
                                    @click="src = @js($thumb)"
                                    :class="src === @js($thumb) ? 'border-slate-900' : 'border-slate-200 hover:border-slate-400'"
                                    class="cursor-pointer h-20 w-20 shrink-0 overflow-hidden rounded-lg border bg-white p-1.5 transition">
                                <img src="{{ $thumb }}" alt="{{ $product->name }}" loading="lazy" class="h-full w-full object-contain">
                            </button>
                        @endforeach
                    </div>

                    {{-- Main image --}}
                    <div class="relative flex-1">
                        <div class="relative flex aspect-square items-center justify-center overflow-hidden rounded-xl border border-slate-200 bg-white p-6">
                            <template x-if="src">
                                <img :src="src" alt="{{ $product->name }}" class="h-full w-full object-contain transition duration-300">
                            </template>
                            <template x-if="!src">
                                <div class="flex flex-col items-center gap-2 text-slate-400">
                                    <svg class="h-14 w-14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                                    <span class="text-sm">Image unavailable</span>
                                </div>
                            </template>

                            @if ($discount > 0)
                                <span class="absolute left-4 top-4 rounded-md bg-red-600 px-2.5 py-1 text-xs font-bold text-white">-{{ $discount }}%</span>
                            @endif

                            <button type="button" aria-label="Add to wishlist"
                                    class="cursor-pointer absolute right-4 top-4 flex h-9 w-9 items-center justify-center rounded-full bg-white text-slate-500 shadow-sm transition hover:text-red-600">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.8-7.5 1.1-1.1a5.5 5.5 0 0 0-.1-7.8Z"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Purchase --}}
            <section class="min-w-0 lg:col-span-4">
                @if ($product->brand)
                    <p class="text-sm font-semibold text-slate-600">{{ $product->brand }}</p>
                @endif

                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-950 sm:text-3xl">{{ $product->name }}</h1>

                @if ($sel)
                    <div class="mt-5 flex flex-wrap items-baseline gap-3">
                        <span class="text-3xl font-extrabold text-red-600">₹{{ number_format((float) $sel->price, 2) }}</span>
                        @if ($discount > 0)
                            <span class="text-base text-slate-400 line-through">₹{{ number_format((float) $sel->compare_at_price, 2) }}</span>
                            <span class="rounded-md bg-red-600 px-2 py-0.5 text-xs font-bold text-white">{{ $discount }}% OFF</span>
                        @endif
                    </div>
                    <p class="mt-1 text-xs text-slate-500">Inclusive of all taxes</p>
                @else
                    <p class="mt-5 font-semibold text-red-600">Currently unavailable</p>
                    <p class="mt-1 text-sm text-slate-500">This product has no available variants at the moment.</p>
                @endif

                <div class="transition-opacity duration-200" wire:loading.class="opacity-60" wire:target="selectColor,selectSize">

                    {{-- Color --}}
                    @if ($colors->isNotEmpty())
                        <div class="mt-7">
                            <p class="text-sm font-semibold text-slate-900">
                                Color
                                <span class="ml-1 font-normal text-slate-500">{{ $sel?->color ? ucfirst($sel->color) : '' }}</span>
                            </p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach ($colors as $color)
                                    @php $isSelected = $sel?->color === $color; @endphp
                                    <button type="button" wire:key="color-{{ $color }}"
                                            wire:click='selectColor(@js($color))'
                                            aria-pressed="{{ $isSelected ? 'true' : 'false' }}"
                                            class="cursor-pointer rounded-full border px-4 py-1.5 text-sm transition-all duration-150 active:scale-95
                                                {{ $isSelected
                                                    ? 'border-slate-900 bg-slate-900 font-semibold text-white'
                                                    : 'border-slate-300 text-slate-700 hover:border-slate-600 hover:text-slate-950' }}">
                                        {{ ucfirst($color) }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Size --}}
                    @if ($sizes->isNotEmpty())
                        <div class="mt-6">
                            <p class="text-sm font-semibold text-slate-900">Size</p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach ($sizes as $size)
                                    @php
                                        $isSelected = $sel?->size === $size;
                                        $isAvailable = in_array($size, $sizesForColor, true);
                                    @endphp
                                    <button type="button" wire:key="size-{{ $size }}"
                                            wire:click='selectSize(@js($size))'
                                            @disabled(! $isAvailable)
                                            aria-pressed="{{ $isSelected ? 'true' : 'false' }}"
                                            class="min-w-14 rounded-lg border px-4 py-2 text-sm font-medium uppercase transition-all duration-150 active:scale-95
                                                {{ $isSelected
                                                    ? 'cursor-pointer border-slate-900 bg-slate-900 text-white'
                                                    : ($isAvailable
                                                        ? 'cursor-pointer border-slate-300 text-slate-700 hover:border-slate-600 hover:text-slate-950'
                                                        : 'cursor-not-allowed border-slate-200 text-slate-300 line-through') }}">
                                        {{ strtoupper($size) }}
                                    </button>
                                @endforeach
                            </div>

                            @if ($sel?->color && count($sizesForColor) < $sizes->count())
                                <p class="mt-2 text-xs text-slate-500">Some sizes are not available in {{ ucfirst($sel->color) }}.</p>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Quantity + cart --}}
                @if ($sel && $sel->stock_quantity > 0)
                    <div class="mt-7 flex items-center gap-4">
                        <p class="text-sm font-semibold text-slate-900">Quantity</p>
                        <div class="inline-flex items-center rounded-lg border border-slate-300">
                            <button type="button" wire:click="decreaseQuantity" aria-label="Decrease quantity"
                                    class="cursor-pointer flex h-10 w-10 items-center justify-center text-slate-700 transition hover:bg-slate-50 active:scale-95">−</button>
                            <span class="flex h-10 w-12 items-center justify-center border-x border-slate-300 text-sm font-semibold">{{ $quantity }}</span>
                            <button type="button" wire:click="increaseQuantity" aria-label="Increase quantity"
                                    class="cursor-pointer flex h-10 w-10 items-center justify-center text-slate-700 transition hover:bg-slate-50 active:scale-95">+</button>
                        </div>
                        <span class="text-xs text-slate-500">{{ $sel->stock_quantity }} available</span>
                    </div>

                    <button type="button" wire:click="addToCart" wire:loading.attr="disabled" wire:target="addToCart"
                            class="cursor-pointer mt-6 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-red-600 px-6 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700 active:scale-[0.99] disabled:cursor-not-allowed disabled:opacity-60">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3h2l2.4 12.2a2 2 0 0 0 2 1.6h8.8a2 2 0 0 0 2-1.6L22 8H6"/><circle cx="10" cy="21" r="1"/><circle cx="18" cy="21" r="1"/></svg>
                        <span wire:loading.remove wire:target="addToCart">Add to cart</span>
                        <span wire:loading wire:target="addToCart">Adding...</span>
                    </button>

                    @error('cart')
                        <p class="mt-3 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                @endif

                <div class="mt-6 flex gap-6 border-t border-slate-200 pt-5 text-sm text-slate-600">
                    <span class="inline-flex items-center gap-2"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.8-7.5 1.1-1.1a5.5 5.5 0 0 0-.1-7.8Z"/></svg>Wishlist</span>
                    <span class="inline-flex items-center gap-2"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="m8.6 13.5 6.8 4M15.4 6.5l-6.8 4"/></svg>Share</span>
                </div>
            </section>

            {{-- Trust card --}}
            <aside class="h-fit rounded-xl border border-slate-200 bg-white p-6 lg:col-span-3">
                <ul class="divide-y divide-slate-100 text-sm">
                    <li class="flex gap-4 py-4 first:pt-0">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-700"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M1 3h15v13H1zM16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg></span>
                        <div><p class="font-semibold text-slate-900">Delivery</p><p class="mt-0.5 text-xs text-slate-500">Shipping calculated at checkout</p></div>
                    </li>
                    <li class="flex gap-4 py-4">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-700"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/></svg></span>
                        <div><p class="font-semibold text-slate-900">Easy returns</p><p class="mt-0.5 text-xs text-slate-500">Check the return policy before ordering</p></div>
                    </li>
                    <li class="flex gap-4 py-4">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-700"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></span>
                        <div><p class="font-semibold text-slate-900">Secure payment</p><p class="mt-0.5 text-xs text-slate-500">Your details stay safe</p></div>
                    </li>
                    <li class="flex gap-4 py-4 last:pb-0">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-700"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 14v-2a9 9 0 0 1 18 0v2"/><path d="M3 14h3v5H3zM18 14h3v5h-3z"/></svg></span>
                        <div><p class="font-semibold text-slate-900">Support</p><p class="mt-0.5 text-xs text-slate-500">We are here to help</p></div>
                    </li>
                </ul>
            </aside>
        </div>

        {{-- Description + related --}}
        <div class="mt-10 grid gap-6 lg:grid-cols-12">
            <section class="rounded-xl border border-slate-200 bg-white p-6 sm:p-8 lg:col-span-8">
                <h2 class="text-lg font-bold text-slate-950">Product description</h2>
                @if ($product->description)
                    <div class="mt-4 whitespace-pre-line text-sm leading-7 text-slate-600">{{ $product->description }}</div>
                @else
                    <p class="mt-4 text-sm text-slate-500">No description available.</p>
                @endif
            </section>

            <aside class="rounded-xl border border-slate-200 bg-white p-6 lg:col-span-4">
                <div class="flex items-center justify-between">
                    <h2 class="font-semibold text-slate-950">Related products</h2>
                    <a href="{{ route('shop') }}" class="cursor-pointer text-sm font-medium text-slate-600 hover:text-slate-950">View all</a>
                </div>

                @if ($related->isNotEmpty())
                    <div class="mt-5 space-y-2">
                        @foreach ($related as $item)
                            @php
                                $rv = $item->variants->where('is_active', true)->where('stock_quantity', '>', 0)->sortBy('price')->first();
                                $img = $item->primaryImage?->image_path ?? $rv?->image_path;
                            @endphp
                            <a href="{{ route('products.show', ['slug' => $item->slug]) }}" wire:key="related-{{ $item->id }}"
                               class="cursor-pointer flex items-center gap-4 rounded-lg p-2 transition hover:bg-slate-50">
                                <div class="h-16 w-16 shrink-0 overflow-hidden rounded-lg border border-slate-200 bg-white p-1">
                                    @if ($img)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($img) }}" alt="{{ $item->name }}" loading="lazy" class="h-full w-full object-contain">
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="line-clamp-2 text-sm font-medium text-slate-900">{{ $item->name }}</p>
                                    @if ($rv)
                                        <p class="mt-1 text-sm font-semibold text-slate-950">₹{{ number_format((float) $rv->price, 2) }}</p>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="mt-5 text-sm text-slate-500">No related products right now.</p>
                @endif
            </aside>
        </div>

        <div class="mt-8">
            <a href="{{ route('shop') }}" class="cursor-pointer inline-flex items-center gap-2 text-sm font-medium text-slate-700 hover:text-slate-950">
                ← Continue shopping
            </a>
        </div>
    </div>
</div>