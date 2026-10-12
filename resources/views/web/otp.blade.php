@component('web.layouts.auth', ['title' => 'Login with OTP'])
    <div x-data="otpScreen" class="space-y-5">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">Login with OTP</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">We'll text a one-time code to verify it's you.</p>
        </div>

        <div class="grid grid-cols-2 gap-2" role="tablist">
            <button type="button" class="chip" :class="for === 'PHONE' && 'chip-active'" @click="switchFor('PHONE')">Phone</button>
            <button type="button" class="chip" :class="for === 'EMAIL' && 'chip-active'" @click="switchFor('EMAIL')">Email</button>
        </div>

        <template x-if="!sent">
            <label class="block">
                <span class="input-label" x-text="for === 'PHONE' ? 'Phone number' : 'Email address'"></span>
                <input type="text" class="input" placeholder="you@example.com"
                       x-model="userName" @keydown.enter="send()">
            </label>
        </template>

        <template x-if="sent">
            <label class="block">
                <span class="input-label">One-time code</span>
                <input type="text" inputmode="numeric" autocomplete="one-time-code" class="input" placeholder="000000"
                       x-model="otp" @keydown.enter="verify()">
            </label>
        </template>

        <p x-show="error" x-text="error" class="text-sm font-medium text-danger"></p>

        <button type="button" x-show="!sent" class="btn-primary w-full" @click="send()" x-bind:disabled="sending">
            <span x-show="!sending">Send code</span>
            <span x-show="sending">Sending…</span>
        </button>
        <button type="button" x-show="sent" class="btn-primary w-full" @click="verify()" x-bind:disabled="loading">
            <span x-show="!loading">Verify & sign in</span>
            <span x-show="loading">Verifying…</span>
        </button>

        <p class="text-center text-sm text-slate-500 dark:text-slate-400">
            Prefer a password?
            <a href="/login" class="font-semibold brand-text">Sign in</a>
        </p>
    </div>
@endcomponent