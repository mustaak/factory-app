@php
    $cartCount = collect(session('factory_cart', []))->sum('quantity');
@endphp

<div class="min-h-screen bg-slate-100">

    {{-- Top bar --}}
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

    <main class="mx-auto max-w-5xl px-6 py-8 lg:px-10">

        {{-- Breadcrumb --}}
        <nav class="mb-3 flex flex-wrap items-center gap-2 text-sm text-slate-500">
            <a href="{{ route('account.profile') }}" class="cursor-pointer hover:text-slate-900">Account</a>
            <span aria-hidden="true">›</span>
            @if ($showForm)
                <a href="{{ route('account.addresses') }}" class="cursor-pointer hover:text-slate-900">My Addresses</a>
                <span aria-hidden="true">›</span>
                <span class="text-slate-700">{{ $editingId ? 'Edit Address' : 'Add New Address' }}</span>
            @else
                <span class="text-slate-700">My Addresses</span>
            @endif
        </nav>

        @if (! $showForm)
            {{-- ===== LIST VIEW ===== --}}
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-slate-950">My Addresses</h1>
                    <p class="mt-2 text-sm text-slate-500">Manage your shipping addresses. You can add multiple addresses for faster checkout.</p>
                </div>
                <button type="button" wire:click="create"
                        class="cursor-pointer inline-flex items-center gap-2 rounded-lg bg-red-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                    Add New Address
                </button>
            </div>

            @if (session('address-status'))
                <div role="status" class="mt-6 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-800 ring-1 ring-emerald-600/20">
                    {{ session('address-status') }}
                </div>
            @endif

            <div class="mt-8 grid gap-6 md:grid-cols-2">
                @forelse ($addresses as $address)
                    <article wire:key="address-{{ $address->id }}"
                             class="flex flex-col rounded-xl border bg-white p-6 shadow-sm transition
                                {{ $address->is_default ? 'border-slate-300' : 'border-slate-200 hover:border-slate-300' }}">

                        <div class="flex items-start justify-between gap-3">
                            @if ($address->is_default)
                                <span class="inline-flex items-center gap-1.5 rounded-md bg-emerald-600 px-2.5 py-1 text-xs font-semibold text-white">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="m12 2 3.1 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.8 21l1.2-6.8-5-4.9 6.9-1z"/></svg>
                                    Primary Address
                                </span>
                            @else
                                <span class="inline-flex rounded-md bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">{{ $address->label ?: 'Address' }}</span>
                            @endif

                            <button type="button" wire:click="setDefault({{ $address->id }})"
                                    aria-label="Set as primary address"
                                    class="cursor-pointer flex h-5 w-5 shrink-0 items-center justify-center rounded-full border-2 transition
                                        {{ $address->is_default ? 'border-red-600' : 'border-slate-300 hover:border-slate-400' }}">
                                @if ($address->is_default)
                                    <span class="h-2.5 w-2.5 rounded-full bg-red-600"></span>
                                @endif
                            </button>
                        </div>

                        <div class="mt-5 flex gap-4">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-red-50 text-red-600">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a7 7 0 0 0-7 7c0 5.2 7 13 7 13s7-7.8 7-13a7 7 0 0 0-7-7Zm0 9.5a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5Z"/></svg>
                            </span>
                            <div class="min-w-0 space-y-2">
                                <p class="text-base font-semibold text-slate-900">{{ $address->name }}</p>
                                <p class="text-sm text-slate-600">{{ $address->phone }}</p>
                                <p class="text-sm leading-6 text-slate-600">
                                    {{ $address->address_line1 }}@if ($address->address_line2), {{ $address->address_line2 }}@endif<br>
                                    {{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}, {{ $address->country }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-wrap items-center gap-3 border-t border-slate-100 pt-5">
                            <button type="button" wire:click="edit({{ $address->id }})"
                                    class="cursor-pointer inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                                Edit
                            </button>
                            <button type="button" wire:click="delete({{ $address->id }})"
                                    wire:confirm="Delete this address?"
                                    class="cursor-pointer inline-flex items-center gap-2 rounded-lg border border-red-200 px-4 py-2 text-sm font-medium text-red-600 transition hover:bg-red-50">
                                Delete
                            </button>
                            @if (! $address->is_default)
                                <button type="button" wire:click="setDefault({{ $address->id }})"
                                        class="cursor-pointer ml-auto inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-700">
                                    Set as primary
                                </button>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="rounded-xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center md:col-span-2">
                        <p class="text-sm font-semibold text-slate-900">No saved addresses yet</p>
                        <p class="mx-auto mt-1 max-w-sm text-sm text-slate-500">Add an address to use it at checkout.</p>
                        <button type="button" wire:click="create"
                                class="cursor-pointer mt-6 rounded-lg bg-red-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700">
                            Add New Address
                        </button>
                    </div>
                @endforelse
            </div>

        @else
            {{-- ===== FORM VIEW ===== --}}
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-slate-950">
                    {{ $editingId ? 'Edit Address' : 'Add New Address' }}
                </h1>
                <p class="mt-2 text-sm text-slate-500">Add a shipping address to your account. You can use it for faster checkout.</p>
            </div>

            <div class="mt-8 grid items-start gap-6 xl:grid-cols-[1fr_320px]">

                <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                    <form wire:submit="save" class="space-y-6">
                        <div class="grid gap-6 sm:grid-cols-2">
                            <div>
                                <label for="addr-name" class="mb-2 block text-sm font-medium text-slate-800">Full Name <span class="text-red-600">*</span></label>
                                <input id="addr-name" type="text" wire:model="name" placeholder="Enter full name" autocomplete="name"
                                       class="w-full rounded-lg border-slate-300 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-red-500 focus:ring-red-500">
                                @error('name') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="addr-phone" class="mb-2 block text-sm font-medium text-slate-800">Phone Number <span class="text-red-600">*</span></label>
                                <input id="addr-phone" type="tel" inputmode="numeric" wire:model="phone" placeholder="10 digit mobile number" autocomplete="tel"
                                       class="w-full rounded-lg border-slate-300 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-red-500 focus:ring-red-500">
                                @error('phone') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label for="addr-line1" class="mb-2 block text-sm font-medium text-slate-800">Address Line 1 <span class="text-red-600">*</span></label>
                            <input id="addr-line1" type="text" wire:model="address_line1" placeholder="House No., Building Name, Street Name" autocomplete="address-line1"
                                   class="w-full rounded-lg border-slate-300 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-red-500 focus:ring-red-500">
                            @error('address_line1') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="addr-line2" class="mb-2 block text-sm font-medium text-slate-800">Address Line 2 <span class="font-normal text-slate-400">(Optional)</span></label>
                            <input id="addr-line2" type="text" wire:model="address_line2" placeholder="Apartment, Suite, Floor, etc." autocomplete="address-line2"
                                   class="w-full rounded-lg border-slate-300 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-red-500 focus:ring-red-500">
                            @error('address_line2') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid gap-6 sm:grid-cols-2">
                            <div>
                                <label for="addr-city" class="mb-2 block text-sm font-medium text-slate-800">City <span class="text-red-600">*</span></label>
                                <input id="addr-city" type="text" wire:model="city" placeholder="Enter city" autocomplete="address-level2"
                                       class="w-full rounded-lg border-slate-300 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-red-500 focus:ring-red-500">
                                @error('city') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="addr-state" class="mb-2 block text-sm font-medium text-slate-800">State <span class="text-red-600">*</span></label>
                                <input id="addr-state" type="text" wire:model="state" placeholder="Enter state" autocomplete="address-level1"
                                       class="w-full rounded-lg border-slate-300 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-red-500 focus:ring-red-500">
                                @error('state') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="addr-postal" class="mb-2 block text-sm font-medium text-slate-800">Pincode <span class="text-red-600">*</span></label>
                                <input id="addr-postal" type="text" inputmode="numeric" wire:model="postal_code" placeholder="Enter pincode" autocomplete="postal-code"
                                       class="w-full rounded-lg border-slate-300 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-red-500 focus:ring-red-500">
                                @error('postal_code') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="addr-country" class="mb-2 block text-sm font-medium text-slate-800">Country code <span class="text-red-600">*</span></label>
                                <input id="addr-country" type="text" maxlength="2" wire:model="country" placeholder="IN" autocomplete="country"
                                       class="w-full rounded-lg border-slate-300 py-2.5 text-sm uppercase text-slate-900 placeholder:text-slate-400 focus:border-red-500 focus:ring-red-500">
                                @error('country') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label for="addr-label" class="mb-2 block text-sm font-medium text-slate-800">Label <span class="font-normal text-slate-400">(Optional)</span></label>
                            <input id="addr-label" type="text" wire:model="label" placeholder="Home, Office, Parents"
                                   class="w-full rounded-lg border-slate-300 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-red-500 focus:ring-red-500">
                            @error('label') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <label class="flex cursor-pointer items-start justify-between gap-4 rounded-lg border border-slate-200 bg-slate-50 p-4">
                            <span>
                                <span class="block text-sm font-semibold text-slate-900">Set as primary address</span>
                                <span class="mt-0.5 block text-xs text-slate-500">This will be your default address for checkout.</span>
                            </span>
                            <span class="relative mt-0.5 inline-flex shrink-0">
                                <input type="checkbox" wire:model="is_default" class="peer sr-only">
                                <span class="h-6 w-11 rounded-full bg-slate-300 transition peer-checked:bg-red-600"></span>
                                <span class="absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white shadow transition peer-checked:translate-x-5"></span>
                            </span>
                        </label>

                        <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-6 sm:flex-row sm:items-center sm:justify-between">
                            <button type="button" wire:click="cancel"
                                    class="cursor-pointer inline-flex items-center justify-center rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                                Back to Addresses
                            </button>

                            <button type="submit" wire:loading.attr="disabled" wire:target="save"
                                    class="cursor-pointer inline-flex items-center justify-center rounded-lg bg-red-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-60">
                                <span wire:loading.remove wire:target="save">{{ $editingId ? 'Update Address' : 'Save Address' }}</span>
                                <span wire:loading wire:target="save">Saving...</span>
                            </button>
                        </div>
                    </form>
                </section>

                <aside class="space-y-6 xl:sticky xl:top-24">
                    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="text-lg font-bold text-slate-950">Why add multiple addresses?</h2>
                        <ul class="mt-4 space-y-3 text-sm text-slate-700">
                            <li class="flex gap-3"><span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600"><svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 4 4L19 6"/></svg></span>Deliver to your home, office or friends</li>
                            <li class="flex gap-3"><span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600"><svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 4 4L19 6"/></svg></span>Set a primary address for quick checkout</li>
                            <li class="flex gap-3"><span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600"><svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 4 4L19 6"/></svg></span>Manage and edit anytime</li>
                        </ul>
                    </div>

                    <div class="rounded-xl border border-red-100 bg-red-50 p-5">
                        <p class="text-sm font-semibold text-red-700">Quick tips</p>
                        <p class="mt-1 text-sm leading-6 text-slate-600">Make sure your address is complete and accurate to avoid delivery delays.</p>
                    </div>
                </aside>
            </div>
        @endif
    </main>
</div>