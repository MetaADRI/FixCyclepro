@component('web.layouts.app', ['title' => 'Profile', 'logo' => ''])
    <div x-data="profileScreen" class="mx-auto max-w-2xl px-5 pb-28 pt-6 lg:pb-8">
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">Profile</h1>

        <div x-show="loading" class="mt-5 space-y-4">
            <div class="skeleton h-28 rounded-3xl"></div>
            <div class="skeleton h-16 rounded-2xl"></div>
        </div>

        <p x-show="error && !loading" class="mt-4 text-sm font-medium text-danger" x-text="error"></p>

        <template x-if="!loading && user">
            <div class="mt-5 space-y-5">
                <div class="card flex items-center gap-4">
                    <span class="brand-bg brand-contrast flex h-16 w-16 items-center justify-center rounded-2xl text-xl font-extrabold" x-text="initials()"></span>
                    <div class="min-w-0">
                        <p class="truncate text-lg font-extrabold text-slate-900 dark:text-white"
                           x-text="(user.first_name || '') + ' ' + (user.last_name || '')"></p>
                        <p class="truncate text-sm text-slate-500" x-text="user.email || user.UserPhone || ''"></p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="card">
                        <p class="text-xs uppercase tracking-wide text-slate-400">Wallet</p>
                        <p class="mt-1 text-xl font-extrabold brand-text"
                           x-text="user.wallet_balance !== null && user.wallet_balance !== undefined ? user.wallet_balance : '—'"></p>
                    </div>
                    <div class="card">
                        <p class="text-xs uppercase tracking-wide text-slate-400">Country</p>
                        <p class="mt-1 truncate text-sm font-semibold" x-text="user.country_name || '—'"></p>
                    </div>
                </div>

                <div class="card space-y-1">
                    <a href="/app/wallet" class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-semibold hover:bg-slate-50 dark:hover:bg-slate-800">
                        <svg class="h-5 w-5 text-slate-400" aria-hidden="true"><use href="#i-wallet"/></svg>
                        Wallet
                        <svg class="ml-auto h-4 w-4 text-slate-300 dark:text-slate-600" aria-hidden="true"><use href="#i-chevron-right"/></svg>
                    </a>
                    <a href="/app/bookings" class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-semibold hover:bg-slate-50 dark:hover:bg-slate-800">
                        <svg class="h-5 w-5 text-slate-400" aria-hidden="true"><use href="#i-bookings"/></svg>
                        My bookings
                        <svg class="ml-auto h-4 w-4 text-slate-300 dark:text-slate-600" aria-hidden="true"><use href="#i-chevron-right"/></svg>
                    </a>
                    <a href="/app/support" class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-semibold hover:bg-slate-50 dark:hover:bg-slate-800">
                        <svg class="h-5 w-5 text-slate-400" aria-hidden="true"><use href="#i-support"/></svg>
                        Support
                        <svg class="ml-auto h-4 w-4 text-slate-300 dark:text-slate-600" aria-hidden="true"><use href="#i-chevron-right"/></svg>
                    </a>
                </div>

                <button type="button" class="btn-ghost w-full text-danger" @click="logout()">
                    <svg class="mr-2 inline h-4 w-4" aria-hidden="true"><use href="#i-logout"/></svg>
                    Sign out
                </button>
            </div>
        </template>
    </div>
@endcomponent