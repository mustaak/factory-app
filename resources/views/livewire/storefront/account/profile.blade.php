@php
    $statusStyle = [
        'pending' => ['bg' => 'bg-amber-50 text-amber-700 ring-amber-600/20', 'dot' => 'bg-amber-500'],
        'processing' => ['bg' => 'bg-blue-50 text-blue-700 ring-blue-600/20', 'dot' => 'bg-blue-500'],
        'shipped' => ['bg' => 'bg-indigo-50 text-indigo-700 ring-indigo-600/20', 'dot' => 'bg-indigo-500'],
        'delivered' => ['bg' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20', 'dot' => 'bg-emerald-500'],
        'cancelled' => ['bg' => 'bg-rose-50 text-rose-700 ring-rose-600/20', 'dot' => 'bg-rose-500'],
    ];

    // Sidebar items. 'type' => 'section' ka matlab Livewire section switch, 'link' ka matlab page par jao.
    $menu = [
        'overview' => [
            'type' => 'section',
            'label' => 'Overview',
            'icon' => 'M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-6h-6v6H4a1 1 0 0 1-1-1v-9.5Z',
        ],
        'profile' => [
            'type' => 'section',
            'label' => 'Personal information',
            'icon' => 'M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2 M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z',
        ],
        'addresses' => [
            'type' => 'link',
            'url' => 'account.addresses',
            'label' => 'Addresses',
            'icon' => 'M12 21s-7-6.2-7-11.5A7 7 0 0 1 19 9.5C19 14.8 12 21 12 21Z M12 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z',
        ],
        'security' => [
            'type' => 'link',
            'url' => 'account.password.set',
            'label' => 'Sign-in & security',
            'icon' => 'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z',
        ],
    ];
@endphp

<div class="min-h-screen bg-slate-50">

    {{-- Header --}}
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-4 sm:px-6">
            <a href="{{ route('home') }}" class="cursor-pointer text-lg font-bold tracking-tight text-slate-900">
                Factory<span class="text-red-600">.</span>
            </a>

            <div class="flex items-center gap-2">
                <a href="{{ route('shop') }}"
                   class="cursor-pointer inline-flex items-center gap-2 rounded-lg bg-red-600 px-6 py-3.5 text-sm font-semibold text-white shadow-lg shadow-red-600/20 transition hover:bg-red-700">
                    Continue shopping
                </a>
            </div>
        </div>
    </header>

    <div class="mx-auto grid max-w-6xl gap-8 px-4 py-8 sm:px-6 lg:grid-cols-[240px_1fr]">

        {{-- Sidebar --}}
        <aside class="lg:sticky lg:top-8 lg:h-fit">
            <div class="mb-6 flex items-center gap-3 px-2">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-slate-900 text-sm font-semibold text-white">
                    {{ strtoupper(substr($name ?: 'U', 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="truncate text-sm font-semibold text-slate-900">{{ $name }}</p>
                    <p class="truncate text-xs text-slate-500">{{ $email }}</p>
                </div>
            </div>

            @php
                $itemBase = 'cursor-pointer flex w-full items-center gap-3 border-l-2 px-3 py-2.5 text-left text-sm font-medium transition';
                $itemActive = 'border-red-600 bg-white text-slate-900 shadow-sm';
                $itemInactive = 'border-transparent text-slate-600 hover:bg-white hover:text-slate-900';
            @endphp

            <nav class="space-y-0.5">
                @foreach ($menu as $key => $item)
                    @if ($item['type'] === 'link')
                        <a href="{{ route($item['url']) }}"
                           wire:key="menu-{{ $key }}"
                           class="{{ $itemBase }} {{ $itemInactive }}">
                            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                <path d="{{ $item['icon'] }}"/>
                            </svg>
                            {{ $item['label'] }}
                        </a>
                    @else
                        <button type="button"
                                wire:click="setSection('{{ $key }}')"
                                wire:key="menu-{{ $key }}"
                                class="{{ $itemBase }} {{ $section === $key ? $itemActive : $itemInactive }}">
                            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                <path d="{{ $item['icon'] }}"/>
                            </svg>
                            {{ $item['label'] }}
                        </button>
                    @endif
                @endforeach
            </nav>

            <div class="mt-6 border-t border-slate-200 pt-4">
                <button type="button" wire:click="logout" wire:confirm="Sign out karna chahte hain?"
                        class="cursor-pointer flex w-full items-center gap-3 px-3 py-2.5 text-left text-sm font-medium text-slate-600 transition hover:text-red-600">
                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4 M16 17l5-5-5-5 M21 12H9"/>
                    </svg>
                    Sign out
                </button>
            </div>
        </aside>

        {{-- Content --}}
        <main class="min-w-0 space-y-6">

            @if (session('profile-status'))
                <div role="status" class="flex items-center gap-2 rounded-lg bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 ring-1 ring-emerald-600/20">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m5 12 4 4L19 6"/></svg>
                    {{ session('profile-status') }}
                </div>
            @endif

            {{-- Overview --}}
            @if ($section === 'overview')
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900">Hello, {{ $name }}</h1>
                    <p class="mt-1 text-sm text-slate-500">Manage your orders and account details.</p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <a href="{{ route('account.orders') }}"
                       class="cursor-pointer group rounded-xl border border-slate-200 bg-white p-5 text-left transition hover:border-slate-300 hover:shadow-sm">
                        <div class="flex items-center justify-between">
                            <p class="font-semibold text-slate-900">Your orders</p>
                            <span class="text-slate-400 transition group-hover:translate-x-0.5 group-hover:text-slate-700">→</span>
                        </div>
                        <p class="mt-1 text-sm text-slate-500">Track deliveries and view past purchases.</p>
                    </a>

                    <button type="button" wire:click="setSection('profile')"
                            class="cursor-pointer group rounded-xl border border-slate-200 bg-white p-5 text-left transition hover:border-slate-300 hover:shadow-sm">
                        <div class="flex items-center justify-between">
                            <p class="font-semibold text-slate-900">Personal information</p>
                            <span class="text-slate-400 transition group-hover:translate-x-0.5 group-hover:text-slate-700">→</span>
                        </div>
                        <p class="mt-1 text-sm text-slate-500">Update your name and email address.</p>
                    </button>
                </div>

                <section class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                    <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                        <h2 class="font-semibold text-slate-900">Recent orders</h2>
                        <button type="button" wire:click="setSection('orders')"
                                class="cursor-pointer text-sm font-medium text-slate-600 hover:text-slate-900">
                            View all
                        </button>
                    </div>

                    @forelse ($recentOrders as $order)
                        <a href="{{ route('account.orders.show', $order->order_number) }}"
                           wire:key="recent-{{ $order->id }}"
                           class="cursor-pointer flex items-center justify-between gap-4 border-b border-slate-100 px-5 py-4 transition last:border-b-0 hover:bg-slate-50">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-slate-900">{{ $order->order_number }}</p>
                                <p class="mt-0.5 text-xs text-slate-500">
                                    {{ $order->created_at->format('d M Y') }} · {{ $order->items_count }} {{ $order->items_count === 1 ? 'item' : 'items' }}
                                </p>
                            </div>
                            <div class="flex shrink-0 items-center gap-4">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium capitalize ring-1 ring-inset {{ $statusStyle[$order->status]['bg'] ?? 'bg-slate-50 text-slate-700 ring-slate-600/20' }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $statusStyle[$order->status]['dot'] ?? 'bg-slate-400' }}"></span>
                                    {{ $order->status }}
                                </span>
                                <span class="w-24 text-right text-sm font-semibold text-slate-900">₹{{ number_format($order->total, 2) }}</span>
                            </div>
                        </a>
                    @empty
                        <div class="px-5 py-12 text-center">
                            <p class="text-sm font-medium text-slate-900">No orders yet</p>
                            <p class="mt-1 text-sm text-slate-500">Your orders will appear here once you place one.</p>
                            <a href="{{ route('shop') }}" class="cursor-pointer mt-5 inline-flex rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                                Start shopping
                            </a>
                        </div>
                    @endforelse
                </section>
            @endif

            {{-- Personal information --}}
            @if ($section === 'profile')
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900">Personal information</h1>
                    <p class="mt-1 text-sm text-slate-500">Update the name and email linked to your account.</p>
                </div>

                <section class="rounded-xl border border-slate-200 bg-white p-6">
                    <form wire:submit="updateProfile" class="max-w-xl space-y-5">
                        <div>
                            <label for="name" class="mb-1.5 block text-sm font-medium text-slate-700">Full name</label>
                            <input id="name" type="text" wire:model="name" autocomplete="name" required
                                   class="w-full rounded-lg border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 shadow-sm focus:border-slate-900 focus:ring-slate-900">
                            @error('name') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="email" class="mb-1.5 block text-sm font-medium text-slate-700">Email address</label>
                            <input id="email" type="email" wire:model="email" autocomplete="email" required
                                   class="w-full rounded-lg border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 shadow-sm focus:border-slate-900 focus:ring-slate-900">
                            @error('email') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <button type="submit" wire:loading.attr="disabled" wire:target="updateProfile"
                                    class="cursor-pointer rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-60">
                                <span wire:loading.remove wire:target="updateProfile">Save changes</span>
                                <span wire:loading wire:target="updateProfile">Saving...</span>
                            </button>
                        </div>
                    </form>
                </section>
            @endif
        </main>
    </div>
</div>