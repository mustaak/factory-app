<div class="flex min-h-screen items-center justify-center bg-[#f3f4f6] px-4 py-8 sm:px-6 lg:py-12">

    <div class="grid w-full max-w-[990px] overflow-hidden rounded-3xl
                border border-slate-200 bg-white shadow-xl
                lg:min-h-[600px] lg:grid-cols-2">

        @if (session('login-notice'))
            <div class="mb-4 rounded-lg bg-amber-50 px-4 py-3 text-sm text-amber-800 ring-1 ring-amber-600/20">
                {{ session('login-notice') }}
            </div>
        @endif

        {{-- Left branding --}}
        <section class="flex flex-col justify-between bg-[#111111]
                        p-8 text-white sm:p-10 lg:p-12">

            <a href="{{ route('home') }}"
               class="flex items-center gap-3 text-xl font-bold text-white">
                <span class="flex h-11 w-11 items-center justify-center
                             rounded-xl bg-red-600 text-xl text-white">
                    M
                </span>
                Factory
            </a>

            <div class="my-10">
                <span class="rounded-full border border-white/20
                             px-3 py-1.5 text-xs text-slate-200">
                    YOUR MARKETPLACE
                </span>

                <h1 class="mt-7 max-w-sm text-4xl font-bold leading-tight
                           text-white sm:text-5xl">
                    Good to see you again.
                </h1>

                <p class="mt-5 max-w-sm text-sm leading-7 text-slate-300">
                    Sign in to discover products, manage your orders,
                    and enjoy a smoother shopping experience.
                </p>

                <div class="mt-8 space-y-4 text-sm text-slate-200">
                    @foreach ([
                        'Discover products from multiple sellers',
                        'Keep track of your orders',
                        'Manage your account in one place',
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
                Your next great find is just a sign-in away.
            </p>
        </section>

        {{-- Right login panel --}}
        <section class="flex items-center justify-center bg-white
                        px-6 py-10 sm:px-10 lg:px-12">

            <div class="w-full max-w-md">

                <a href="{{ route('home') }}"
                   class="text-sm text-slate-500 hover:text-red-600">
                    ← Back to store
                </a>

                <h2 class="mt-8 text-3xl font-bold tracking-tight text-slate-900">
                    Sign in
                </h2>

                <p class="mt-2 text-sm leading-6 text-slate-500">
                    Enter your details to access your account.
                </p>

                @if (session('status'))
                    <div class="mt-5 rounded-lg bg-green-50 p-3
                                text-sm text-green-700">
                        {{ session('status') }}
                    </div>
                @endif

                <form wire:submit="login" class="mt-8 space-y-5">

                    <div>
                        <label for="email"
                               class="mb-2 block text-sm font-semibold text-slate-800">
                            Email address
                        </label>

                        <input id="email" type="email" wire:model="email"
                               autocomplete="email" required autofocus
                               placeholder="you@example.com"
                               class="w-full rounded-xl border border-slate-300
                                      bg-white px-4 py-3 text-sm text-slate-900
                                      placeholder:text-slate-400
                                      focus:border-red-500 focus:outline-none
                                      focus:ring-4 focus:ring-red-500/10">

                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="mb-2 flex items-center justify-between gap-3">
                            <label for="password"
                                   class="text-sm font-semibold text-slate-800">
                                Password
                            </label>

                            <a href="{{ route('password.request') }}"
                               class="text-sm font-semibold text-red-600 hover:text-red-700">
                                Forgot password?
                            </a>
                        </div>

                        <input id="password" type="password" wire:model="password"
                               autocomplete="current-password" required
                               placeholder="Enter your password"
                               class="w-full rounded-xl border border-slate-300
                                      bg-white px-4 py-3 text-sm text-slate-900
                                      placeholder:text-slate-400
                                      focus:border-red-500 focus:outline-none
                                      focus:ring-4 focus:ring-red-500/10">

                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <label class="flex items-center gap-3 text-sm text-slate-600">
                        <input type="checkbox" wire:model="remember"
                               class="h-4 w-4 rounded accent-red-600">
                        Remember me
                    </label>

                    <button type="submit" wire:loading.attr="disabled"
                            wire:target="login"
                            class="w-full rounded-xl bg-red-600 px-4 py-3.5
                                   text-sm font-semibold text-white
                                   transition hover:bg-red-700
                                   disabled:opacity-60">
                        <span wire:loading.remove wire:target="login">
                            Sign in to your account
                        </span>
                        <span wire:loading wire:target="login">
                            Signing in...
                        </span>
                    </button>
                </form>

                <p class="mt-7 text-center text-sm text-slate-500">
                    Don't have an account?
                    <a href="/register"
                       class="font-semibold text-red-600 hover:text-red-700">
                        Create an account
                    </a>
                </p>

                <p class="mt-7 text-center text-xs leading-5 text-slate-400">
                    Your account information is protected.
                </p>
            </div>
        </section>
    </div>
</div>