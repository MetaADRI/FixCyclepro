@component('web.layouts.auth', ['title' => 'Sign in'])
    <div x-data="loginScreen" class="space-y-5">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">Welcome back</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Sign in to book your next ride or delivery.</p>
        </div>

        <p x-show="notice" x-cloak x-text="notice"
           class="rounded-2xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300"></p>

        <label class="block">
            <span class="input-label">Email address</span>
            <input type="email" inputmode="email" autocomplete="email" class="input" placeholder="you@example.com"
                   x-model="email" @keydown.enter="submit()">
        </label>

        <label class="block">
            <span class="input-label">Password</span>
            <span class="relative block">
                <input :type="showPassword ? 'text' : 'password'" autocomplete="current-password" class="input pr-12"
                       placeholder="••••••••" x-model="password" @keydown.enter="submit()">
                <button type="button" @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 flex min-w-touch items-center justify-center px-3 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
                        :aria-label="showPassword ? 'Hide password' : 'Show password'">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </button>
            </span>
        </label>

        <p x-show="error" x-cloak x-text="error" class="text-sm font-medium text-danger"></p>

        <button type="button" class="btn-primary w-full" @click="submit()" x-bind:disabled="loading">
            <span x-show="!loading">Sign in</span>
            <span x-show="loading">Signing in…</span>
        </button>

        <div class="flex items-center justify-between text-sm">
            <a href="/signup" class="font-semibold brand-text">Create an account</a>
            <a href="/otp" class="font-medium text-slate-500 hover:text-slate-700 dark:text-slate-400">Sign in with a code</a>
        </div>
    </div>
@endcomponent
