@component('web.layouts.auth', ['title' => 'Create account'])
    <div x-data="signupScreen" class="space-y-5">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">Create your account</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Start booking services in minutes.</p>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <label class="block">
                <span class="input-label">First name</span>
                <input type="text" autocomplete="given-name" class="input" placeholder="Jane" x-model="first_name">
            </label>
            <label class="block">
                <span class="input-label">Last name</span>
                <input type="text" autocomplete="family-name" class="input" placeholder="Doe" x-model="last_name">
            </label>
        </div>

        <label class="block">
            <span class="input-label">Phone number</span>
            <input type="tel" inputmode="tel" autocomplete="tel" class="input" placeholder="+1 555 000 1234" x-model="phone">
        </label>

        <label class="block">
            <span class="input-label">Email (optional)</span>
            <input type="email" inputmode="email" autocomplete="email" class="input" placeholder="you@example.com" x-model="email">
        </label>

        <label class="block">
            <span class="input-label">Password</span>
            <input type="password" autocomplete="new-password" class="input" placeholder="8+ characters" x-model="password" @keydown.enter="submit()">
        </label>

        <p x-show="error" x-text="error" class="text-sm font-medium text-danger"></p>

        <button type="button" class="btn-primary w-full" @click="submit()" x-bind:disabled="loading">
            <span x-show="!loading">Create account</span>
            <span x-show="loading">Creating…</span>
        </button>

        <p class="text-center text-sm text-slate-500 dark:text-slate-400">
            Already have an account?
            <a href="/login" class="font-semibold brand-text">Sign in</a>
        </p>
    </div>
@endcomponent