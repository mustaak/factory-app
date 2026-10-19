@php
    $statusStyle = [
        'pending' => ['bg' => 'bg-amber-50 text-amber-700 ring-amber-600/20', 'dot' => 'bg-amber-500'],
        'processing' => ['bg' => 'bg-blue-50 text-blue-700 ring-blue-600/20', 'dot' => 'bg-blue-500'],
        'shipped' => ['bg' => 'bg-indigo-50 text-indigo-700 ring-indigo-600/20', 'dot' => 'bg-indigo-500'],
        'delivered' => ['bg' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20', 'dot' => 'bg-emerald-500'],
        'cancelled' => ['bg' => 'bg-rose-50 text-rose-700 ring-rose-600/20', 'dot' => 'bg-rose-500'],
    ];
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
            <span class="text-slate-900">Orders</span>
        </nav>

        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Your orders</h1>
                <p class="mt-1 text-sm text-slate-500">Select an order to view its details and status.</p>
            </div>
            <p class="text-sm text-slate-500">
                {{ $orders->total() }} {{ $orders->total() === 1 ? 'order' : 'orders' }}
            </p>
        </div>

        <section class="mt-8 overflow-hidden rounded-xl border border-slate-200 bg-white">
            @forelse ($orders as $order)
                @php $thumbs = $order->items->take(3); @endphp

                <a href="{{ route('account.orders.show', $order->order_number) }}"
                   wire:key="order-{{ $order->id }}"
                   class="cursor-pointer block border-b border-slate-100 px-5 py-5 transition last:border-b-0 hover:bg-slate-50">

                    <div class="flex flex-wrap items-center justify-between gap-3 text-xs text-slate-500">
                        <div class="flex flex-wrap gap-x-6 gap-y-1">
                            <div>
                                <span class="block">Order placed</span>
                                <span class="mt-0.5 block text-sm font-medium text-slate-900">{{ $order->created_at->format('d M Y') }}</span>
                            </div>
                            <div>
                                <span class="block">Total</span>
                                <span class="mt-0.5 block text-sm font-medium text-slate-900">₹{{ number_format($order->total, 2) }}</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="block">Order number</span>
                            <span class="mt-0.5 block text-sm font-medium text-slate-900">{{ $order->order_number }}</span>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center gap-4">
                        <div class="flex -space-x-2">
                            @foreach ($thumbs as $item)
                                @php
                                    $img = $item->variant?->image_path ?: $item->variant?->product?->primaryImage?->image_path;
                                @endphp
                                <div class="flex h-14 w-14 items-center justify-center overflow-hidden rounded-lg border-2 border-white bg-slate-100 ring-1 ring-slate-200">
                                    @if ($img)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($img) }}" alt="{{ $item->product_name }}"
                                             class="h-full w-full object-cover">
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-slate-900">
                                {{ $thumbs->first()?->product_name }}
                                @if ($order->items_count > 1)
                                    <span class="font-normal text-slate-500">+ {{ $order->items_count - 1 }} more</span>
                                @endif
                            </p>
                            <span class="mt-1.5 inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium capitalize ring-1 ring-inset {{ $statusStyle[$order->status]['bg'] ?? 'bg-slate-50 text-slate-700 ring-slate-600/20' }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ $statusStyle[$order->status]['dot'] ?? 'bg-slate-400' }}"></span>
                                {{ $order->status }}
                            </span>
                        </div>

                        <span class="hidden shrink-0 text-sm font-medium text-slate-600 sm:block">View details →</span>
                    </div>
                </a>
            @empty
                <div class="px-6 py-16 text-center">
                    <p class="text-sm font-medium text-slate-900">No orders yet</p>
                    <p class="mt-1 text-sm text-slate-500">Orders you place will show up here.</p>
                    <a href="{{ route('shop') }}"
                       class="cursor-pointer mt-6 inline-flex rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                        Start shopping
                    </a>
                </div>
            @endforelse
        </section>

        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    </div>
</div>