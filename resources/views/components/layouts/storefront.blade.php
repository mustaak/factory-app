<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Factory | Marketplace' }}</title>
    <meta name="description" content="Discover products from trusted vendors.">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="min-h-screen bg-white text-slate-900 antialiased">

    {{-- Toast --}}
    <div x-data="{ show: false, message: '', timeout: null }"
         x-on:cart-message.window="
            message = $event.detail.message;
            show = true;
            clearTimeout(timeout);
            timeout = setTimeout(() => show = false, 3000);
         "
         x-show="show"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         role="status" aria-live="polite"
         class="fixed bottom-6 right-6 z-[9999] flex w-[calc(100%-3rem)] max-w-sm items-start gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-lg">
        <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 4 4L19 6"/></svg>
        </span>
        <div class="min-w-0 flex-1">
            <p class="text-sm font-semibold text-slate-900">Added to cart</p>
            <p class="mt-0.5 truncate text-sm text-slate-500" x-text="message"></p>
            <a href="{{ route('cart') }}" class="mt-2 inline-block text-sm font-medium text-slate-900 hover:underline">View cart</a>
        </div>
        <button type="button" @click="show = false" aria-label="Close"
                class="cursor-pointer rounded-md p-1 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 6l12 12M18 6 6 18"/></svg>
        </button>
    </div>

    {{-- Header --}}
    <header class="sticky top-0 z-30 border-b border-slate-200 bg-white">
        <div class="mx-auto flex h-[72px] max-w-[1440px] items-center gap-8 px-4 sm:px-8">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="shrink-0 text-2xl font-extrabold tracking-tight text-slate-950">
                Factory<span class="text-red-600">.</span>
            </a>

            {{-- Main nav --}}
            <nav class="hidden items-center gap-7 lg:flex">
                @php
                    $navLinks = [
                        ['label' => 'Home', 'route' => 'home', 'active' => request()->routeIs('home')],
                        ['label' => 'Shop', 'route' => 'shop', 'active' => request()->routeIs('shop') || request()->routeIs('products.show')],
                        ['label' => 'Categories', 'route' => 'home', 'anchor' => '#categories', 'active' => false],
                    ];
                @endphp

                @foreach ($navLinks as $link)
                    <a href="{{ route($link['route']) }}{{ $link['anchor'] ?? '' }}"
                       class="cursor-pointer relative py-7 text-sm font-medium transition
                              {{ $link['active'] ? 'text-red-600' : 'text-slate-700 hover:text-slate-950' }}">
                        {{ $link['label'] }}
                        @if ($link['active'])
                            <span class="absolute inset-x-0 bottom-0 h-0.5 bg-red-600"></span>
                        @endif
                    </a>
                @endforeach
            </nav>

            {{-- Search --}}
            <form action="{{ route('shop') }}" method="GET" class="hidden min-w-0 flex-1 md:block">
                <label for="site-search" class="sr-only">Search products</label>
                <div class="relative">
                    <svg class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
                    <input id="site-search" type="search" name="search" value="{{ request('search') }}"
                           placeholder="Search products, brands and more..."
                           class="w-full rounded-full border-slate-200 bg-slate-50 py-2.5 pl-11 pr-4 text-sm text-slate-900 placeholder:text-slate-400 focus:border-slate-900 focus:bg-white focus:ring-slate-900">
                </div>
            </form>

            {{-- Right actions --}}
            <div class="ml-auto flex items-center gap-5">
                @auth
                    <a href="{{ route('account.profile') }}"
                       class="cursor-pointer hidden items-center gap-2.5 text-sm font-medium text-slate-800 transition hover:text-slate-950 sm:flex">
                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-950 text-sm font-semibold text-white">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </span>
                        <span class="max-w-28 truncate">{{ auth()->user()->name }}</span>
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="cursor-pointer hidden text-sm font-medium text-slate-800 transition hover:text-slate-950 sm:block">
                        Sign in
                    </a>
                @endauth

                <a href="{{ route('shop') }}" aria-label="Wishlist"
                   class="cursor-pointer hidden text-slate-700 transition hover:text-red-600 sm:block">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.8-7.5 1.1-1.1a5.5 5.5 0 0 0-.1-7.8Z"/></svg>
                </a>

                <a href="{{ route('cart') }}" aria-label="Cart"
                   class="cursor-pointer relative inline-flex h-10 w-10 items-center justify-center text-slate-700 transition hover:text-red-600">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3h2l2.4 12.2a2 2 0 0 0 2 1.6h8.8a2 2 0 0 0 2-1.6L22 8H6"/><circle cx="10" cy="21" r="1"/><circle cx="18" cy="21" r="1"/></svg>
                    @php $cartQty = collect(session('factory_cart', []))->sum('quantity'); @endphp
                    @if ($cartQty > 0)
                        <span class="absolute -right-0.5 -top-0.5 flex h-5 min-w-5 items-center justify-center rounded-full bg-red-600 px-1 text-[11px] font-bold text-white">{{ $cartQty }}</span>
                    @endif
                </a>
            </div>
        </div>

        {{-- Mobile search --}}
        <div class="border-t border-slate-100 px-4 py-3 md:hidden">
            <form action="{{ route('shop') }}" method="GET">
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Search products..."
                       aria-label="Search products"
                       class="w-full rounded-full border-slate-200 bg-slate-50 px-4 py-2.5 text-sm placeholder:text-slate-400 focus:border-slate-900 focus:ring-slate-900">
            </form>
        </div>

        {{-- Mobile nav --}}
        <nav class="flex gap-6 overflow-x-auto border-t border-slate-100 px-4 py-3 text-sm font-medium lg:hidden">
            <a href="{{ route('home') }}" class="shrink-0 {{ request()->routeIs('home') ? 'text-red-600' : 'text-slate-700' }}">Home</a>
            <a href="{{ route('shop') }}" class="shrink-0 {{ request()->routeIs('shop') ? 'text-red-600' : 'text-slate-700' }}">Shop</a>
            <a href="{{ route('home') }}#categories" class="shrink-0 text-slate-700">Categories</a>
        </nav>
    </header>

    {{-- Page content --}}
    <main class="min-h-[60vh]">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="mt-20 border-t border-slate-200 bg-white">
        <div class="mx-auto grid max-w-[1440px] gap-10 px-4 py-14 sm:px-8 md:grid-cols-4">

            <div class="md:col-span-2">
                <a href="{{ route('home') }}" class="text-2xl font-extrabold tracking-tight text-slate-950">
                    Factory<span class="text-red-600">.</span>
                </a>
                <p class="mt-3 max-w-sm text-sm leading-6 text-slate-500">
                    A marketplace for discovering quality products from trusted vendors.
                </p>
            </div>

            <div>
                <h2 class="text-sm font-semibold text-slate-900">Shop</h2>
                <ul class="mt-4 space-y-3 text-sm text-slate-600">
                    <li><a href="{{ route('shop') }}" class="hover:text-slate-950">All products</a></li>
                    <li><a href="{{ route('home') }}#categories" class="hover:text-slate-950">Categories</a></li>
                    <li><a href="{{ route('cart') }}" class="hover:text-slate-950">Cart</a></li>
                </ul>
            </div>

            <div>
                <h2 class="text-sm font-semibold text-slate-900">Support</h2>
                <ul class="mt-4 space-y-3 text-sm text-slate-600">
                    <li><a href="#" class="hover:text-slate-950">Help center</a></li>
                    <li><a href="#" class="hover:text-slate-950">Shipping and returns</a></li>
                    <li><a href="#" class="hover:text-slate-950">Privacy policy</a></li>
                    <li><a href="#" class="hover:text-slate-950">Terms and conditions</a></li>
                </ul>
            </div>
        </div>

        <div class="border-t border-slate-200">
            <div class="mx-auto flex max-w-[1440px] flex-col gap-2 px-4 py-6 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between sm:px-8">
                <p>&copy; {{ date('Y') }} Factory Marketplace. All rights reserved.</p>
                <p>Payments: Cash on delivery</p>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>

</html>