<div class="min-h-screen bg-[var(--theme-bg,#f3f4f6)] px-4 py-10 sm:px-6">
    <div class="mx-auto max-w-6xl">

        <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-sm font-semibold uppercase tracking-widest text-red-600">
                    Your shopping bag
                </p>
                <h1 class="mt-2 text-3xl font-bold text-[var(--theme-text,#111827)]">
                    Shopping cart
                </h1>
                <p class="mt-2 text-sm text-[var(--theme-muted,#64748b)]">
                    {{ $cartCount }} {{ $cartCount === 1 ? 'item' : 'items' }} in your cart
                </p>
            </div>

            <a href="{{ route('shop') }}"
               class="rounded-xl border border-[var(--theme-border,#d1d5db)]
                      bg-[var(--theme-surface,#fff)] px-4 py-3 text-sm
                      font-semibold text-[var(--theme-text,#111827)]
                      hover:border-red-300">
                Continue shopping
            </a>
        </div>

        @error('cart')
            <div role="alert" class="mb-5 rounded-xl border border-red-200
                                    bg-red-50 p-4 text-sm text-red-700">
                {{ $message }}
            </div>
        @enderror

        @if ($cartItems->isEmpty())
            <div class="rounded-3xl border border-[var(--theme-border,#e5e7eb)]
                        bg-[var(--theme-surface,#fff)] px-6 py-16 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center
                            rounded-2xl bg-red-50 text-red-600">
                    <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.7">
                        <path d="M3 3h2l2.4 12.2a2 2 0 0 0 2 1.6h8.8a2 2 0 0 0 2-1.6L22 8H6"/>
                        <circle cx="10" cy="21" r="1"/>
                        <circle cx="18" cy="21" r="1"/>
                    </svg>
                </div>

                <h2 class="mt-5 text-xl font-bold text-[var(--theme-text,#111827)]">
                    Your cart is empty
                </h2>
                <p class="mt-2 text-sm text-[var(--theme-muted,#64748b)]">
                    Explore our products and add something you love.
                </p>

                <a href="{{ route('shop') }}"
                   class="mt-6 inline-flex rounded-xl bg-red-600 px-6 py-3
                          text-sm font-semibold text-white hover:bg-red-700">
                    Start shopping
                </a>
            </div>
        @else
            <div class="grid items-start gap-6 lg:grid-cols-3">

                <section class="space-y-4 lg:col-span-2">
                    @foreach ($cartItems as $item)
                        <article wire:key="cart-item-{{ $item['variant_id'] }}"
                                 class="flex flex-col gap-4 rounded-2xl border
                                        border-[var(--theme-border,#e5e7eb)]
                                        bg-[var(--theme-surface,#fff)] p-4
                                        sm:flex-row sm:items-center sm:p-5">

                            <div class="h-28 w-full shrink-0 overflow-hidden
                                        rounded-xl bg-[var(--theme-bg,#f8fafc)]
                                        sm:w-28">
                                @if ($item['image_path'])
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($item['image_path']) }}"
                                         alt="{{ $item['name'] }}"
                                         class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full items-center justify-center
                                                text-sm text-[var(--theme-muted,#64748b)]">
                                        No image
                                    </div>
                                @endif
                            </div>

                            <div class="min-w-0 flex-1">
                                <h2 class="font-bold text-[var(--theme-text,#111827)]">
                                    {{ $item['name'] }}
                                </h2>

                                @if ($item['size'] || $item['color'])
                                    <p class="mt-1 text-sm text-[var(--theme-muted,#64748b)]">
                                        @if ($item['size'])
                                            Size: {{ $item['size'] }}
                                        @endif
                                        @if ($item['size'] && $item['color'])
                                            ·
                                        @endif
                                        @if ($item['color'])
                                            Color: {{ $item['color'] }}
                                        @endif
                                    </p>
                                @endif

                                <p class="mt-2 text-lg font-bold text-red-600">
                                    ${{ number_format($item['price'], 2) }}
                                </p>

                                @if ($item['quantity'] > $item['stock_quantity'])
                                    <p class="mt-2 text-sm text-red-600">
                                        Only {{ $item['stock_quantity'] }} available.
                                        Please update the quantity.
                                    </p>
                                @endif
                            </div>

                            <div class="flex items-center justify-between gap-4 sm:flex-col sm:items-end">
                                <div class="flex items-center rounded-xl border
                                            border-[var(--theme-border,#d1d5db)]">
                                    <button type="button"
                                            aria-label="Decrease quantity"
                                            wire:click="updateQuantity({{ $item['variant_id'] }}, {{ max(1, $item['quantity'] - 1) }})"
                                            class="px-3 py-2 text-[var(--theme-text,#111827)]">
                                        −
                                    </button>

                                    <span class="min-w-8 text-center text-sm font-semibold
                                                 text-[var(--theme-text,#111827)]">
                                        {{ $item['quantity'] }}
                                    </span>

                                    <button type="button"
                                            aria-label="Increase quantity"
                                            wire:click="updateQuantity({{ $item['variant_id'] }}, {{ $item['quantity'] + 1 }})"
                                            @disabled($item['quantity'] >= $item['stock_quantity'])
                                            class="px-3 py-2 text-[var(--theme-text,#111827)]
                                                   disabled:cursor-not-allowed disabled:opacity-40">
                                        +
                                    </button>
                                </div>

                                <div class="text-right">
                                    <p class="font-bold text-[var(--theme-text,#111827)]">
                                        ${{ number_format($item['line_total'], 2) }}
                                    </p>

                                    <button type="button"
                                            wire:click="removeItem({{ $item['variant_id'] }})"
                                            wire:confirm="Remove this item from your cart?"
                                            class="mt-2 text-sm font-medium text-red-600 hover:text-red-700">
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </section>

                <aside class="rounded-2xl border border-[var(--theme-border,#e5e7eb)]
                              bg-[var(--theme-surface,#fff)] p-6">
                    <h2 class="text-lg font-bold text-[var(--theme-text,#111827)]">
                        Order summary
                    </h2>

                    <div class="mt-5 flex justify-between gap-4 text-sm">
                        <span class="text-[var(--theme-muted,#64748b)]">
                            Subtotal ({{ $cartCount }} items)
                        </span>
                        <span class="font-semibold text-[var(--theme-text,#111827)]">
                            ${{ number_format($subtotal, 2) }}
                        </span>
                    </div>

                    <div class="mt-4 flex justify-between gap-4 border-t
                                border-[var(--theme-border,#e5e7eb)] pt-4">
                        <span class="font-bold text-[var(--theme-text,#111827)]">
                            Total
                        </span>
                        <span class="text-xl font-bold text-red-600">
                            ${{ number_format($subtotal, 2) }}
                        </span>
                    </div>

                    <p class="mt-3 text-xs leading-5 text-[var(--theme-muted,#64748b)]">
                        Shipping and applicable taxes will be calculated at checkout.
                    </p>

                    <a href="{{ route('checkout') }}"
                    class="mt-6 block w-full rounded-xl bg-red-600 px-5 py-3.5 text-center text-sm font-semibold text-white transition hover:bg-red-700">
                        Proceed to checkout
                    </a>

                    <button type="button"
                            wire:click="clearCart"
                            wire:confirm="Are you sure you want to clear your cart?"
                            class="mt-3 w-full rounded-xl border
                                   border-[var(--theme-border,#d1d5db)]
                                   px-5 py-3 text-sm font-semibold
                                   text-[var(--theme-text,#111827)] hover:border-red-300">
                        Clear cart
                    </button>
                </aside>
            </div>
        @endif
    </div>
</div>