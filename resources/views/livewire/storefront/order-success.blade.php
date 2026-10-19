<x-layouts.storefront>
    <div class="min-h-screen bg-slate-50">
        <div class="mx-auto max-w-2xl px-4 py-12 sm:px-6 sm:py-16">

            {{-- Confirmation --}}
            <section class="rounded-xl border border-slate-200 bg-white p-8 text-center sm:p-10">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 ring-1 ring-inset ring-emerald-600/20">
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m5 12 4 4L19 6" />
                    </svg>
                </div>

                <h1 class="mt-6 text-2xl font-bold tracking-tight text-slate-950 sm:text-3xl">Thank you for your order</h1>
                <p class="mt-2 text-sm text-slate-500">
                    We have received your order and will process it shortly.
                </p>

                <div class="mt-6 inline-flex flex-col items-center rounded-lg bg-slate-50 px-6 py-4 ring-1 ring-inset ring-slate-200">
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-500">Order number</p>
                    <p class="mt-1 text-base font-semibold text-slate-900">{{ $order->order_number }}</p>
                </div>
            </section>

            {{-- Order summary --}}
            <section class="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white">
                <div class="border-b border-slate-200 px-6 py-4">
                    <h2 class="font-semibold text-slate-900">Order summary</h2>
                </div>

                <ul class="divide-y divide-slate-100">
                    @foreach ($order->items as $item)
                        <li class="flex items-start justify-between gap-4 px-6 py-4">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-slate-900">{{ $item->product_name }}</p>
                                <p class="mt-0.5 text-xs text-slate-500">
                                    @if ($item->size) Size {{ $item->size }} @endif
                                    @if ($item->size && $item->color) · @endif
                                    @if ($item->color) Color {{ $item->color }} @endif
                                    @if ($item->size || $item->color) · @endif
                                    Qty {{ $item->quantity }}
                                </p>
                            </div>
                            <p class="shrink-0 text-sm font-semibold text-slate-900">₹{{ number_format($item->line_total, 2) }}</p>
                        </li>
                    @endforeach
                </ul>

                <div class="space-y-2 border-t border-slate-200 bg-slate-50/60 px-6 py-4 text-sm">
                    <div class="flex justify-between text-slate-600">
                        <span>Shipping</span>
                        <span>Calculated later</span>
                    </div>
                    <div class="flex justify-between border-t border-slate-200 pt-3 text-base font-semibold text-slate-900">
                        <span>Total</span>
                        <span>₹{{ number_format($order->total, 2) }}</span>
                    </div>
                    <p class="pt-1 text-xs text-slate-500">Payment method: Cash on delivery</p>
                </div>
            </section>

            {{-- Delivery address --}}
            <section class="mt-6 rounded-xl border border-slate-200 bg-white p-6">
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

            {{-- Actions --}}
            <div class="mt-8 flex flex-col-reverse items-center justify-center gap-3 sm:flex-row">
                <a href="{{ route('account.orders') }}"
                   class="cursor-pointer inline-flex w-full items-center justify-center rounded-lg border border-slate-300 bg-white px-5 py-3 text-sm font-medium text-slate-700 transition hover:border-slate-400 hover:text-slate-900 sm:w-auto">
                    View my orders
                </a>
                <a href="{{ route('shop') }}"
                   class="cursor-pointer inline-flex w-full items-center justify-center rounded-lg bg-slate-950 px-5 py-3 text-sm font-medium text-white transition hover:bg-slate-800 sm:w-auto">
                    Continue shopping
                </a>
            </div>
        </div>
    </div>
</x-layouts.storefront>