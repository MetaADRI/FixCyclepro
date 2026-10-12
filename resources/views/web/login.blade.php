@component('web.layouts.auth', ['title' => 'Sign in'])
    <div x-data="loginScreen" class="space-y-5">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">Welcome back</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Sign in to book your next ride or delivery.</p>
        </div>

        <div class="grid grid-cols-2 gap-2" role="tablist">
            <button type="button" class="chip" :class="mode === 'phone' && 'chip-active'" @click="switchMode('phone')">Phone</button>
            <button type="button" class="chip" :class="mode === 'email' && 'chip-active'" @click="switchMode('email')">Email</button>
        </div>

        <template x-if="mode === 'phone'">
            <label class="block">
                <span class="input-label">Phone number</span>
                <input type="tel" inputmode="tel" autocomplete="tel" class="input" placeholder="+1 555 000 1234"
                       x-model="phone">
            </label>
        </template>
        <template x-if="mode === 'email'">
            <label class="block">
                <span class="input-label">Email address</span>
                <input type="email" inputmode="email" autocomplete="email" class="input" placeholder="you@example.com"
                       x-model="email">
            </label>
        </template>

        <label class="block">
            <span class="input-label">Password</span>
            <input type="password" autocomplete="current-password" class="input" placeholder="••••••••"
                   x-model="password" @keydown.enter="submit()">
        </label>

        <p x-show="error" x-text="error" class="text-sm font-medium text-danger"></p>

        <button type="button" class="btn-primary w-full" @click="submit()" x-bind:disabled="loading">
            <span x-show="!loading">Sign in</span>
            <span x-show="loading">Signing in…</span>
        </button>

        <div class="flex items-center justify-between text-sm">
            <a href="/signup" class="font-semibold brand-text">Create an account</a>
            <a href="/otp" class="font-medium text-slate-500 hover:text-slate-700 dark:text-slate-400">Login with OTP</a>
        </div>
    </div>
@endcomponent