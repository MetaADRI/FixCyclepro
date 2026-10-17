@component('web.layouts.auth', ['title' => 'Create account'])
    <div x-data="signupScreen" class="space-y-5">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">Create your account</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Start booking services in minutes.</p>
        </div>

        {{-- Country --}}
        <div class="relative" @click.outside="countriesOpen = false">
            <span class="input-label">Country</span>
            <button type="button" class="input flex items-center justify-between gap-2 text-left"
                    @click="countriesOpen = !countriesOpen" :aria-expanded="countriesOpen" aria-haspopup="listbox">
                <span class="flex min-w-0 items-center gap-2" x-show="country">
                    <span class="text-lg leading-none" x-text="country && country.flag"></span>
                    <span class="truncate font-medium" x-text="country && country.name"></span>
                    <span class="shrink-0 text-slate-400" x-text="country && country.dial"></span>
                </span>
                <span class="text-slate-400" x-show="!country">Select your country</span>
                <svg class="h-5 w-5 shrink-0 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9"/>
                </svg>
            </button>

            <div x-show="countriesOpen" x-cloak
                 class="absolute z-20 mt-2 w-full overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lifted dark:border-slate-700 dark:bg-slate-900">
                <div class="border-b border-slate-100 p-2 dark:border-slate-800">
                    <input type="search" class="input" placeholder="Search country or dial code"
                           x-model="countryQuery" x-ref="countryInput" autocomplete="off">
                </div>
                <ul class="max-h-60 overflow-y-auto py-1" role="listbox">
                    <template x-if="countriesLoading">
                        <li class="px-4 py-3 text-sm text-slate-400">Loading countries…</li>
                    </template>
                    <template x-for="c in filteredCountries" :key="c.id ?? c.iso">
                        <li>
                            <button type="button" role="option"
                                    class="flex w-full items-center gap-3 px-4 py-2.5 text-left text-sm hover:bg-slate-50 dark:hover:bg-slate-800"
                                    @click="selectCountry(c)">
                                <span class="text-lg leading-none" x-text="c.flag"></span>
                                <span class="flex-1 truncate" x-text="c.name"></span>
                                <span class="shrink-0 text-slate-400" x-text="c.dial"></span>
                            </button>
                        </li>
                    </template>
                    <template x-if="!countriesLoading && filteredCountries.length === 0">
                        <li class="px-4 py-3 text-sm text-slate-400">No matches.</li>
                    </template>
                </ul>
            </div>
        </div>

        {{-- Phone --}}
        <label class="block">
            <span class="input-label">Phone number</span>
            <div class="flex items-stretch">
                <span class="flex min-w-[4.5rem] items-center justify-center rounded-l-2xl border border-r-0 border-slate-300 bg-slate-50 px-3 font-medium text-slate-600 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300"
                      x-text="country && country.dial ? country.dial : '+'"></span>
                <input type="tel" inputmode="tel" autocomplete="tel" class="input rounded-l-none"
                       placeholder="97 000 0000" x-model="phone" @keydown.enter="submit()">
            </div>
        </label>

        {{-- Email --}}
        <label class="block">
            <span class="input-label">Email address</span>
            <input type="email" inputmode="email" autocomplete="email" class="input"
                   placeholder="you@example.com" x-model="email" @keydown.enter="submit()">
        </label>

        {{-- Password --}}
        <label class="block">
            <span class="input-label">Password</span>
            <span class="relative block">
                <input :type="showPassword ? 'text' : 'password'" autocomplete="new-password" class="input pr-12"
                       placeholder="8+ characters" x-model="password" @keydown.enter="submit()">
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
            <span x-show="!loading">Create account</span>
            <span x-show="loading">Creating…</span>
        </button>

        <p class="text-center text-sm text-slate-500 dark:text-slate-400">
            Already have an account?
            <a href="/login" class="font-semibold brand-text">Sign in</a>
        </p>
    </div>
@endcomponent
