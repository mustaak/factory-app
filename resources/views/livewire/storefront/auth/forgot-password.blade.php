<div class="flex min-h-screen items-center justify-center bg-[var(--theme-bg,#f3f4f6)] px-4 py-10 sm:px-6">

    <div class="grid w-full max-w-5xl overflow-hidden rounded-3xl
                border border-[var(--theme-border,#e5e7eb)]
                bg-[var(--theme-surface,#ffffff)] shadow-xl
                lg:min-h-[560px] lg:grid-cols-2">

        {{-- Left branding panel --}}
        <section class="relative hidden flex-col justify-between
                        overflow-hidden bg-[#111111] p-10 text-white
                        lg:flex lg:p-12">

            <a href="{{ route('home') }}"
               class="relative z-10 text-3xl font-extrabold tracking-tight text-white">
                Factory<span class="text-red-500">.</span>
            </a>

            <div class="relative z-10">
                <div class="flex h-14 w-14 items-center justify-center
                            rounded-2xl bg-red-600/15 text-red-400">
                    <svg class="h-7 w-7" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="1.7">
                        <rect x="3" y="11" width="18" height="11" rx="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        <path d="M12 15v3"/>
                    </svg>
                </div>

                <h2 class="mt-7 max-w-sm text-4xl font-bold leading-tight text-white">
                    A fresh start is just one step away.
                </h2>

                <p class="mt-5 max-w-sm text-sm leading-7 text-slate-400">
                    Forgot your password? No worries. Recover access to
                    your account and get back to discovering great products.
                </p>
            </div>

            <p class="relative z-10 text-xs text-slate-500">
                Factory Marketplace · Account recovery
            </p>

            <div class="pointer-events-none absolute -right-24 top-1/4
                        h-72 w-72 rounded-full bg-red-600/20 blur-3xl"></div>
        </section>

        {{-- Right form panel --}}
        <section class="flex items-center justify-center px-6 py-10 sm:px-10 lg:px-12">

            <div class="w-full max-w-md">

                <a href="{{ route('login') }}"
                   class="inline-flex items-center gap-2 text-sm font-medium
                          text-[var(--theme-muted,#64748b)]
                          transition hover:text-red-600">
                    <svg class="h-4 w-4" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="m15 18-6-6 6-6"/>
                        <path d="M9 12h12"/>
                    </svg>
                    Back to sign in
                </a>

                <a href="{{ route('home') }}"
                   class="mt-8 block text-xl font-extrabold tracking-tight
                          text-[var(--theme-text,#111827)] lg:hidden">
                    Factory<span class="text-red-600">.</span>
                </a>

                <div class="mt-8 flex h-14 w-14 items-center justify-center
                            rounded-2xl bg-red-50 text-red-600">
                    <svg class="h-7 w-7" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="1.7">
                        <rect x="3" y="11" width="18" height="11" rx="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        <path d="M12 15v3"/>
                    </svg>
                </div>

                <h1 class="mt-6 text-3xl font-bold tracking-tight
                           text-[var(--theme-text,#111827)]">
                    Forgot password?
                </h1>

                <p class="mt-3 text-sm leading-6
                          text-[var(--theme-muted,#64748b)]">
                    Enter the email address linked to your account.
                    We'll send you a password reset link if the account exists.
                </p>

                @if (session('status'))
                    <div role="status"
                         class="mt-6 rounded-xl border border-green-200
                                bg-green-50 p-4 text-sm leading-6 text-green-800">
                        {{ session('status') }}
                    </div>
                @endif

                @if (session('error'))
                    <div role="alert"
                         class="mt-6 rounded-xl border border-red-200
                                bg-red-50 p-4 text-sm text-red-700">
                        {{ session('error') }}
                    </div>
                @endif

                <form wire:submit="sendResetLink" class="mt-7 space-y-5">

                    <div>
                        <label for="email"
                               class="mb-2 block text-sm font-semibold
                                      text-[var(--theme-text,#1f2937)]">
                            Email address
                        </label>

                        <input
                            id="email"
                            type="email"
                            wire:model="email"
                            autocomplete="email"
                            required
                            autofocus
                            placeholder="you@example.com"
                            aria-describedby="email-error"
                            @error('email') aria-invalid="true" @enderror
                            class="w-full rounded-xl border
                                   border-[var(--theme-border,#d1d5db)]
                                   bg-[var(--theme-surface,#ffffff)]
                                   px-4 py-3.5 text-sm
                                   text-[var(--theme-text,#111827)]
                                   outline-none transition
                                   placeholder:text-slate-400
                                   focus:border-red-500 focus:ring-4
                                   focus:ring-red-500/10"
                        >

                        @error('email')
                            <p id="email-error" class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="sendResetLink"
                        class="flex w-full items-center justify-center gap-2
                               rounded-xl bg-red-600 px-4 py-3.5
                               text-sm font-semibold text-white shadow-sm
                               transition hover:bg-red-700
                               focus:outline-none focus:ring-4
                               focus:ring-red-500/20 disabled:cursor-wait
                               disabled:opacity-60">

                        <span wire:loading.remove wire:target="sendResetLink">
                            Send reset link
                        </span>

                        <span wire:loading wire:target="sendResetLink">
                            Sending reset link...
                        </span>
                    </button>
                </form>

                <div class="mt-7 rounded-xl bg-[var(--theme-bg,#f8fafc)] p-4">
                    <p class="text-xs leading-5
                              text-[var(--theme-muted,#64748b)]">
                        <strong class="text-[var(--theme-text,#334155)]">
                            Security tip:
                        </strong>
                        Never share your password or password reset link with anyone.
                    </p>
                </div>

                <p class="mt-7 text-center text-xs
                          text-[var(--theme-muted,#94a3b8)]">
                    Need help? Return to
                    <a href="{{ route('login') }}"
                       class="font-semibold text-red-600 hover:text-red-700">
                        sign in
                    </a>.
                </p>

            </div>
        </section>
    </div>
</div>