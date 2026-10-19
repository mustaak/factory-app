<div class="min-h-screen bg-slate-50">
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex h-16 max-w-6xl items-center px-4 sm:px-6">
            <a href="{{ route('home') }}" class="cursor-pointer text-lg font-bold tracking-tight text-slate-900">
                Factory<span class="text-red-600">.</span>
            </a>
        </div>
    </header>

    <div class="mx-auto max-w-md px-4 py-12 sm:px-6">
        <section class="rounded-xl border border-slate-200 bg-white p-8">
            @if ($orderNumber)
                <div class="mb-6 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-800 ring-1 ring-emerald-600/20">
                    Your order <span class="font-semibold">{{ $orderNumber }}</span> has been placed.
                </div>
            @endif

            <h1 class="text-xl font-bold tracking-tight text-slate-950">Set your password</h1>
            <p class="mt-1 text-sm text-slate-500">
                We created an account for you with the email you used at checkout. Set a password to track your orders later.
            </p>

            <form wire:submit="save" class="mt-6 space-y-5">
                <div>
                    <label for="password" class="mb-1.5 block text-sm font-medium text-slate-700">New password</label>
                    <input id="password" type="password" wire:model="password" autocomplete="new-password"
                           class="w-full rounded-lg border-slate-300 px-3 py-2.5 text-sm shadow-sm focus:border-slate-900 focus:ring-slate-900">
                    @error('password') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-slate-700">Confirm password</label>
                    <input id="password_confirmation" type="password" wire:model="password_confirmation" autocomplete="new-password"
                           class="w-full rounded-lg border-slate-300 px-3 py-2.5 text-sm shadow-sm focus:border-slate-900 focus:ring-slate-900">
                </div>

                <button type="submit" wire:loading.attr="disabled"
                        class="cursor-pointer w-full rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800 disabled:opacity-60">
                    <span wire:loading.remove>Save password</span>
                    <span wire:loading>Saving...</span>
                </button>
            </form>
        </section>
    </div>
</div>