@php
    $statusStyle = [
        'pending' => ['bg' => 'bg-amber-50 text-amber-700 ring-amber-600/20', 'dot' => 'bg-amber-500'],
        'processing' => ['bg' => 'bg-blue-50 text-blue-700 ring-blue-600/20', 'dot' => 'bg-blue-500'],
        'shipped' => ['bg' => 'bg-indigo-50 text-indigo-700 ring-indigo-600/20', 'dot' => 'bg-indigo-500'],
        'delivered' => ['bg' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20', 'dot' => 'bg-emerald-500'],
        'cancelled' => ['bg' => 'bg-rose-50 text-rose-700 ring-rose-600/20', 'dot' => 'bg-rose-500'],
    ];

    $paymentStyle = [
        'pending' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
        'paid' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        'failed' => 'bg-rose-50 text-rose-700 ring-rose-600/20',
    ];

    $steps = [
        'pending' => 'Order placed',
        'processing' => 'Processing',
        'shipped' => 'Shipped',
        'delivered' => 'Delivered',
    ];
    $currentIndex = array_search($order->status, array_keys($steps), true);
    $canCancel = in_array($order->status, ['pending', 'processing'], true);
@endphp

<div class="min-h-screen bg-slate-50">

    {{-- Header --}}
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-4 sm:px-6">
            <a href="{{ route('home') }}" class="cursor-pointer text-lg font-bold tracking-tight text-slate-900">
                Factory<span class="text-red-600">.</span>
            </a>
            <a href="{{ route('shop') }}"
               class="cursor-pointer inline-flex items-center gap-2 rounded-lg bg-red-600 px-6 py-3.5 text-sm font-semibold text-white shadow-lg shadow-red-600/20 transition hover:bg-red-700">
                Continue shopping
            </a>
        </div>
    </header>

    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6">

        {{-- Breadcrumb + heading --}}
        <nav class="mb-4 text-sm text-slate-500">
            <a href="{{ route('account.profile') }}" class="cursor-pointer hover:text-slate-900">Account</a>
            <span class="mx-1.5">/</span>
            <a href="{{ route('account.orders') }}" class="cursor-pointer hover:text-slate-900">Orders</a>
            <span class="mx-1.5">/</span>
            <span class="text-slate-900">{{ $order->order_number }}</span>
        </nav>

        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Order {{ $order->order_number }}</h1>
                <p class="mt-1 text-sm text-slate-500">Placed on {{ $order->created_at->format('d M Y, h:i A') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium capitalize ring-1 ring-inset {{ $statusStyle[$order->status]['bg'] ?? 'bg-slate-50 text-slate-700 ring-slate-600/20' }}">
                    <span class="h-1.5 w-1.5 rounded-full {{ $statusStyle[$order->status]['dot'] ?? 'bg-slate-400' }}"></span>
                    {{ $order->status }}
                </span>
                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium capitalize ring-1 ring-inset {{ $paymentStyle[$order->payment_status] ?? 'bg-slate-50 text-slate-700 ring-slate-600/20' }}">
                    Payment {{ $order->payment_status }}
                </span>
            </div>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-[1fr_320px]">

            {{-- Left column --}}
            <div class="min-w-0 space-y-6">

                {{-- Status --}}
                @if ($order->status === 'cancelled')
                    <section class="rounded-xl border border-rose-200 bg-rose-50 p-5">
                        <p class="font-semibold text-rose-800">This order was cancelled</p>
                        @if ($order->cancelled_at)
                            <p class="mt-1 text-sm text-rose-700">Cancelled on {{ $order->cancelled_at->format('d M Y, h:i A') }}</p>
                        @endif
                    </section>
                @else
                    <section class="rounded-xl border border-slate-200 bg-white p-6">
                        <h2 class="font-semibold text-slate-900">Order status</h2>

                        <ol class="mt-6 grid grid-cols-4">
                            @foreach ($steps as $key => $label)
                                @php $done = $loop->index <= $currentIndex; @endphp
                                <li class="relative flex flex-col items-center text-center">
                                    @if (! $loop->first)
                                        <span class="absolute right-1/2 top-3.5 h-0.5 w-full {{ $done ? 'bg-slate-900' : 'bg-slate-200' }}"></span>
                                    @endif
                                    <span class="relative flex h-7 w-7 items-center justify-center rounded-full text-xs font-semibold
                                        {{ $done ? 'bg-slate-900 text-white' : 'bg-white text-slate-400 ring-1 ring-inset ring-slate-300' }}">
                                        @if ($done)
                                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 4 4L19 6"/></svg>
                                        @else
                                            {{ $loop->iteration }}
                                        @endif
                                    </span>
                                    <p class="mt-2 text-xs font-medium sm:text-sm {{ $done ? 'text-slate-900' : 'text-slate-400' }}">
                                        {{ $label }}
                                    </p>
                                </li>
                            @endforeach
                        </ol>

                        @if ($order->courier || $order->tracking_number)
                            <dl class="mt-6 grid gap-3 rounded-lg bg-slate-50 p-4 text-sm sm:grid-cols-2">
                                @if ($order->courier)
                                    <div>
                                        <dt class="text-slate-500">Courier</dt>
                                        <dd class="mt-0.5 font-medium text-slate-900">{{ $order->courier }}</dd>
                                    </div>
                                @endif
                                @if ($order->tracking_number)
                                    <div>
                                        <dt class="text-slate-500">Tracking number</dt>
                                        <dd class="mt-0.5 font-medium text-slate-900">{{ $order->tracking_number }}</dd>
                                    </div>
                                @endif
                            </dl>
                        @endif

                        @if ($canCancel)
                            <div class="mt-6 flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 pt-5">
                                <div>
                                    @error('cancel')
                                        <p class="text-sm text-red-600">{{ $message }}</p>
                                    @else
                                        <p class="text-sm text-slate-500">You can cancel this order until it ships.</p>
                                    @enderror
                                </div>
                                <button type="button"
                                        wire:click="cancelOrder"
                                        wire:confirm="Are you sure you want to cancel this order?"
                                        wire:loading.attr="disabled"
                                        class="cursor-pointer rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:border-red-300 hover:text-red-600 disabled:cursor-not-allowed disabled:opacity-60">
                                    <span wire:loading.remove wire:target="cancelOrder">Cancel order</span>
                                    <span wire:loading wire:target="cancelOrder">Cancelling...</span>
                                </button>
                            </div>
                        @endif
                    </section>
                @endif

                {{-- Items --}}
                <section class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                    <div class="border-b border-slate-200 px-6 py-4">
                        <h2 class="font-semibold text-slate-900">Items ({{ $order->items->count() }})</h2>
                    </div>

                    <ul class="divide-y divide-slate-100">
                        @foreach ($order->items as $item)
                            @php
                                $img = $item->variant?->image_path ?: $item->variant?->product?->primaryImage?->image_path;
                            @endphp
                            <li wire:key="order-item-{{ $item->id }}" class="flex items-center gap-4 px-6 py-4">
                                <div class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-slate-200 bg-slate-50">
                                    @if ($img)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($img) }}" alt="{{ $item->product_name }}"
                                             class="h-full w-full object-contain p-1">
                                    @else
                                        <svg class="h-6 w-6 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                                    @endif
                                </div>

                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium text-slate-900">{{ $item->product_name }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500">
                                        @if ($item->size) Size {{ $item->size }} @endif
                                        @if ($item->size && $item->color) · @endif
                                        @if ($item->color) Color {{ $item->color }} @endif
                                    </p>
                                    <p class="mt-0.5 text-xs text-slate-500">
                                        ₹{{ number_format($item->price, 2) }} × {{ $item->quantity }}
                                    </p>
                                </div>

                                <p class="shrink-0 text-sm font-semibold text-slate-900">₹{{ number_format($item->line_total, 2) }}</p>
                            </li>
                        @endforeach
                    </ul>

                    <div class="space-y-2 border-t border-slate-200 bg-slate-50/60 px-6 py-4 text-sm">
                        <div class="flex justify-between text-slate-600">
                            <span>Subtotal</span>
                            <span>₹{{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Shipping</span>
                            <span>Calculated later</span>
                        </div>
                        <div class="flex justify-between border-t border-slate-200 pt-3 text-base font-semibold text-slate-900">
                            <span>Total</span>
                            <span>₹{{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                </section>
            </div>

            {{-- Right column --}}
            <aside class="space-y-6 lg:sticky lg:top-8 lg:h-fit">
                <section class="rounded-xl border border-slate-200 bg-white p-6">
                    <h2 class="font-semibold text-slate-900">Delivery address</h2>
                    <div class="mt-3 space-y-0.5 text-sm leading-6 text-slate-600">
                        <p class="font-medium text-slate-900">{{ $order->name }}</p>
                        <p>{{ $order->address_line1 }}</p>
                        @if ($order->address_line2)
                            <p>{{ $order->address_line2 }}</p>
                        @endif
                        <p>{{ $order->city }}, {{ $order->state }} {{ $order->postal_code }}</p>
                        <p>{{ $order->country }}</p>
                        <p class="pt-2">{{ $order->phone }}</p>
                    </div>
                </section>

                <section class="rounded-xl border border-slate-200 bg-white p-6">
                    <h2 class="font-semibold text-slate-900">Payment</h2>
                    <p class="mt-3 text-sm text-slate-600">Cash on delivery</p>

                    <h2 class="mt-6 font-semibold text-slate-900">Order notes</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-600">
                        {{ $order->notes ?: 'No notes for this order.' }}
                    </p>
                </section>
            </aside>
        </div>
    </div>
</div>