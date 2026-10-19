@php
    $input = 'w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-slate-900 focus:ring-slate-900';
    $label = 'mb-1.5 block text-sm font-medium text-slate-700';
    $card = 'rounded-xl border border-slate-200 bg-white p-6';
@endphp

<div class="min-h-screen bg-slate-50">

    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6">

        <div class="mb-8">
            <h1 class="text-2xl font-bold tracking-tight text-slate-950">Checkout</h1>
            <p class="mt-1 text-sm text-slate-500">Review your details and place your order.</p>
        </div>

        @error('cart')
            <div role="alert" class="mb-6 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700 ring-1 ring-red-600/20">
                {{ $message }}
            </div>
        @enderror

        <form wire:submit="placeOrder" class="grid items-start gap-8 lg:grid-cols-3">

            <div class="space-y-6 lg:col-span-2">

                {{-- Contact --}}
                @guest
                    <section class="{{ $card }}">
                        <h2 class="mb-5 font-semibold text-slate-900">Contact details</h2>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label for="name" class="{{ $label }}">Full name</label>
                                <input id="name" type="text" wire:model="name" autocomplete="name" class="{{ $input }}">
                                @error('name') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="email" class="{{ $label }}">Email</label>
                                <input id="email" type="email" wire:model="email" autocomplete="email" class="{{ $input }}">
                                @error('email') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </section>
                @endguest

                {{-- Shipping --}}
                <section class="{{ $card }}">
                    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                        <h2 class="font-semibold text-slate-900">Shipping address</h2>
                        @auth
                            <a href="{{ route('account.addresses') }}"
                               class="cursor-pointer text-sm font-medium text-slate-600 hover:text-slate-900">Manage addresses</a>
                        @endauth
                    </div>

                    @auth
                        @if ($savedAddresses->isNotEmpty())
                            <div class="grid gap-3 sm:grid-cols-2">
                                @foreach ($savedAddresses as $address)
                                    @php $isSelected = ! $useNewAddress && $selectedAddressId === $address->id; @endphp
                                    <label wire:key="checkout-address-{{ $address->id }}"
                                           class="cursor-pointer flex gap-3 rounded-lg border p-4 transition
                                              {{ $isSelected ? 'border-slate-900 bg-slate-50 ring-1 ring-slate-900' : 'border-slate-200 hover:border-slate-300' }}">
                                        <input type="radio" name="saved_address"
                                               value="{{ $address->id }}"
                                               wire:click="selectAddress({{ $address->id }})"
                                               @checked($isSelected)
                                               class="mt-1 text-slate-900 focus:ring-slate-900">
                                        <div class="min-w-0 text-sm">
                                            <p class="font-semibold text-slate-900">
                                                {{ $address->label ?: 'Address' }}
                                                @if ($address->is_default)
                                                    <span class="ml-1 text-xs font-normal text-slate-500">(Default)</span>
                                                @endif
                                            </p>
                                            <p class="mt-1 text-slate-600">
                                                {{ $address->name }}, {{ $address->address_line1 }}@if ($address->address_line2), {{ $address->address_line2 }}@endif
                                            </p>
                                            <p class="text-slate-600">{{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}</p>
                                            <p class="text-slate-600">{{ $address->phone }}</p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @endif

                        <label class="mt-4 inline-flex cursor-pointer items-center gap-2 text-sm font-medium text-slate-700">
                            <input type="radio" name="saved_address" value="new"
                                   wire:click="useNewAddressForm"
                                   @checked($useNewAddress)
                                   class="text-slate-900 focus:ring-slate-900">
                            Use a new address
                        </label>
                    @endauth

                    @if ($useNewAddress || ! auth()->check())
                        <div class="mt-6 grid gap-4 border-t border-slate-200 pt-6 sm:grid-cols-2">
                            <div>
                                <label for="phone" class="{{ $label }}">Mobile number</label>
                                <input id="phone" type="tel" inputmode="numeric" wire:model="phone" autocomplete="tel" class="{{ $input }}">
                                @error('phone') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label for="address_line1" class="{{ $label }}">Address line 1</label>
                                <input id="address_line1" type="text" wire:model="address_line1" autocomplete="address-line1" class="{{ $input }}">
                                @error('address_line1') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label for="address_line2" class="{{ $label }}">Address line 2 <span class="font-normal text-slate-400">(optional)</span></label>
                                <input id="address_line2" type="text" wire:model="address_line2" autocomplete="address-line2" class="{{ $input }}">
                                @error('address_line2') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="city" class="{{ $label }}">City</label>
                                <input id="city" type="text" wire:model="city" autocomplete="address-level2" class="{{ $input }}">
                                @error('city') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="state" class="{{ $label }}">State</label>
                                <input id="state" type="text" wire:model="state" autocomplete="address-level1" class="{{ $input }}">
                                @error('state') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="postal_code" class="{{ $label }}">PIN code</label>
                                <input id="postal_code" type="text" inputmode="numeric" wire:model="postal_code" autocomplete="postal-code" class="{{ $input }}">
                                @error('postal_code') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="country" class="{{ $label }}">Country code</label>
                                <input id="country" type="text" maxlength="2" wire:model="country" autocomplete="country" class="{{ $input }} uppercase">
                                @error('country') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    @endif
                </section>

                {{-- Notes --}}
                <section class="{{ $card }}">
                    <h2 class="mb-5 font-semibold text-slate-900">Order notes <span class="text-sm font-normal text-slate-400">(optional)</span></h2>
                    <textarea id="notes" rows="3" wire:model="notes" class="{{ $input }}"></textarea>
                    @error('notes') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </section>

                {{-- Payment --}}
                <section class="{{ $card }}">
                    <h2 class="mb-5 font-semibold text-slate-900">Payment method</h2>
                    <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-slate-900 bg-slate-50 p-4 ring-1 ring-slate-900">
                        <input type="radio" wire:model="paymentMethod" value="cod" class="text-slate-900 focus:ring-slate-900">
                        <span class="text-sm font-medium text-slate-900">Cash on delivery</span>
                    </label>
                    @error('paymentMethod') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                </section>
            </div>

            {{-- Summary --}}
            <aside class="{{ $card }} lg:sticky lg:top-8">
                <h2 class="font-semibold text-slate-900">Order summary</h2>

                <ul class="mt-5 divide-y divide-slate-100">
                    @foreach ($cartItems as $item)
                        <li wire:key="checkout-item-{{ $item['variant_id'] }}" class="flex justify-between gap-4 py-3 text-sm">
                            <div class="min-w-0">
                                <p class="truncate font-medium text-slate-900">{{ $item['name'] }}</p>
                                <p class="mt-0.5 text-xs text-slate-500">
                                    @if ($item['size']) Size {{ $item['size'] }} @endif
                                    @if ($item['size'] && $item['color']) · @endif
                                    @if ($item['color']) Color {{ $item['color'] }} @endif
                                    @if ($item['size'] || $item['color']) · @endif
                                    Qty {{ $item['quantity'] }}
                                </p>
                            </div>
                            <p class="shrink-0 font-semibold text-slate-900">₹{{ number_format($item['line_total'], 2) }}</p>
                        </li>
                    @endforeach
                </ul>

                <div class="mt-4 space-y-2 border-t border-slate-200 pt-4 text-sm">
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal ({{ $cartCount }} {{ $cartCount === 1 ? 'item' : 'items' }})</span>
                        <span>₹{{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Shipping</span>
                        <span>Calculated later</span>
                    </div>
                </div>

                <div class="mt-4 flex justify-between border-t border-slate-200 pt-4">
                    <span class="font-semibold text-slate-900">Total</span>
                    <span class="text-lg font-bold text-slate-950">₹{{ number_format($subtotal, 2) }}</span>
                </div>

                <button type="submit" wire:loading.attr="disabled" wire:target="placeOrder"
                        class="cursor-pointer mt-6 w-full rounded-lg bg-slate-950 px-5 py-3 text-sm font-medium text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-60">
                    <span wire:loading.remove wire:target="placeOrder">Place order</span>
                    <span wire:loading wire:target="placeOrder">Placing order...</span>
                </button>

                <a href="{{ route('cart') }}"
                   class="cursor-pointer mt-3 block text-center text-sm font-medium text-slate-600 hover:text-slate-900">
                    Back to cart
                </a>
            </aside>
        </form>
    </div>
</div>