<div class="flex min-h-screen items-center justify-center bg-[#f3f4f6] px-4 py-8 sm:px-6">

    <div class="grid w-full max-w-5xl overflow-hidden rounded-3xl
                border border-slate-200 bg-white shadow-xl
                lg:min-h-[650px] lg:grid-cols-2">

        {{-- Left branding panel --}}
        <section class="flex flex-col justify-between bg-[#111111]
                        p-8 text-white sm:p-10 lg:p-12">

            <a href="{{ route('home') }}"
               class="flex items-center gap-3 text-xl font-bold text-white">
                <span class="flex h-11 w-11 items-center justify-center
                             rounded-xl bg-red-600 text-xl">
                    F
                </span>
                Factory<span class="text-red-500">.</span>
            </a>

            <div class="my-10">
                <span class="rounded-full border border-white/20
                             px-3 py-1.5 text-xs text-slate-300">
                    JOIN OUR MARKETPLACE
                </span>

                <h1 class="mt-7 max-w-sm text-4xl font-bold leading-tight
                           text-white sm:text-5xl">
                    Your next favourite thing is waiting.
                </h1>

                <p class="mt-5 max-w-sm text-sm leading-7 text-slate-300">
                    Create your account to discover products, explore
                    new brands and keep your orders in one place.
                </p>

                <div class="mt-8 space-y-4 text-sm text-slate-200">
                    @foreach ([
                        'Explore products from multiple sellers',
                        'Save your details for easier checkout',
                        'Track your shopping orders',
                    ] as $feature)
                        <div class="flex items-center gap-3">
                            <span class="flex h-8 w-8 shrink-0 items-center
                                         justify-center rounded-lg bg-white/10
                                         text-red-400">✓</span>
                            {{ $feature }}
                        </div>
                    @endforeach
                </div>
            </div>

            <p class="text-xs text-slate-400">
                One account. A world of shopping.
            </p>
        </section>

        {{-- Registration form --}}
        <section class="flex items-center justify-center bg-white
                        px-6 py-10 sm:px-10 lg:px-12">

            <div class="w-full max-w-md">

                <a href="{{ route('home') }}"
                   class="text-sm text-slate-500 transition hover:text-red-600">
                    ← Back to store
                </a>

                <h2 class="mt-7 text-3xl font-bold tracking-tight text-slate-900">
                    Create your account
                </h2>

                <p class="mt-2 text-sm leading-6 text-slate-500">
                    Get started with your free Factory account.
                </p>

                <form wire:submit="register" class="mt-7 space-y-4">

                    {{-- Name --}}
                    <div>
                        <label for="name"
                               class="mb-2 block text-sm font-semibold text-slate-800">
                            Full name
                        </label>

                        <input
                            id="name"
                            type="text"
                            wire:model="name"
                            autocomplete="name"
                            placeholder="Enter your full name"
                            required
                            class="w-full rounded-xl border border-slate-300
                                   bg-white px-4 py-3 text-sm text-slate-900
                                   placeholder:text-slate-400 outline-none
                                   transition focus:border-red-500
                                   focus:ring-4 focus:ring-red-500/10"
                        >

                        @error('name')
                            <p class="mt-1.5 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email"
                               class="mb-2 block text-sm font-semibold text-slate-800">
                            Email address
                        </label>

                        <input
                            id="email"
                            type="email"
                            wire:model="email"
                            autocomplete="email"
                            placeholder="you@example.com"
                            required
                            class="w-full rounded-xl border border-slate-300
                                   bg-white px-4 py-3 text-sm text-slate-900
                                   placeholder:text-slate-400 outline-none
                                   transition focus:border-red-500
                                   focus:ring-4 focus:ring-red-500/10"
                        >

                        @error('email')
                            <p class="mt-1.5 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password"
                               class="mb-2 block text-sm font-semibold text-slate-800">
                            Password
                        </label>

                        <input
                            id="password"
                            type="password"
                            wire:model="password"
                            autocomplete="new-password"
                            placeholder="At least 8 characters"
                            required
                            class="w-full rounded-xl border border-slate-300
                                   bg-white px-4 py-3 text-sm text-slate-900
                                   placeholder:text-slate-400 outline-none
                                   transition focus:border-red-500
                                   focus:ring-4 focus:ring-red-500/10"
                        >

                        @error('password')
                            <p class="mt-1.5 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Confirm password --}}
                    <div>
                        <label for="password_confirmation"
                               class="mb-2 block text-sm font-semibold text-slate-800">
                            Confirm password
                        </label>

                        <input
                            id="password_confirmation"
                            type="password"
                            wire:model="password_confirmation"
                            autocomplete="new-password"
                            placeholder="Enter password again"
                            required
                            class="w-full rounded-xl border border-slate-300
                                   bg-white px-4 py-3 text-sm text-slate-900
                                   placeholder:text-slate-400 outline-none
                                   transition focus:border-red-500
                                   focus:ring-4 focus:ring-red-500/10"
                        >
                    </div>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="register"
                        class="flex w-full items-center justify-center
                               rounded-xl bg-red-600 px-4 py-3.5
                               text-sm font-semibold text-white shadow-sm
                               transition hover:bg-red-700
                               focus:outline-none focus:ring-4
                               focus:ring-red-500/20 disabled:opacity-60"
                    >
                        <span wire:loading.remove wire:target="register">
                            Create account
                        </span>

                        <span wire:loading wire:target="register">
                            Creating account...
                        </span>
                    </button>
                </form>

                <p class="mt-6 text-center text-sm text-slate-500">
                    Already have an account?
                    <a href="{{ route('login') }}"
                       class="font-semibold text-red-600 hover:text-red-700">
                        Sign in
                    </a>
                </p>

                <p class="mt-6 text-center text-xs leading-5 text-slate-400">
                    By creating an account, you agree to our Terms of
                    Service and Privacy Policy.
                </p>
            </div>
        </section>
    </div>
</div>