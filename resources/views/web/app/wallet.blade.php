@component('web.layouts.app', ['title' => 'Wallet', 'logo' => ''])
    <div x-data="walletScreen" class="mx-auto max-w-2xl px-5 pb-28 pt-7 lg:pb-8">
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">Wallet</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Your balance and payment history in one place.</p>

        {{-- Balance --}}
        <div class="brand-bg relative mt-5 overflow-hidden rounded-4xl p-6 text-white shadow-card">
            <div class="pointer-events-none absolute -right-14 -top-14 h-44 w-44 rounded-full bg-white/10"></div>
            <div class="pointer-events-none absolute -bottom-20 -left-8 h-48 w-48 rounded-full bg-black/10"></div>
            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-white/80">Available balance</p>
                    <p class="mt-1 text-4xl font-extrabold tracking-tight" x-text="balance()"></p>
                </div>
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/15">
                    <svg class="h-6 w-6" aria-hidden="true"><use href="#i-wallet"/></svg>
                </span>
            </div>
            <p class="relative mt-4 text-xs font-medium text-white/70">Use your wallet for faster checkout on rides and deliveries.</p>
        </div>

        {{-- Add money --}}
        <div class="card mt-5 p-5">
            <div x-show="topupAvailable" x-cloak class="space-y-4">
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Add money</h2>
                    <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">Top up from your preferred payment method.</p>
                </div>
                <input type="number" inputmode="decimal" min="0" step="0.01" class="input" placeholder="Amount" x-model="amount">
                <select class="input" x-model="payment_option_id">
                    <template x-for="o in options" :key="o.id">
                        <option :value="o.id" x-text="o.name"></option>
                    </template>
                </select>
                <p x-show="error" x-text="error" class="text-sm font-medium text-danger"></p>
                <button type="button" class="btn-primary w-full" @click="add()" x-bind:disabled="adding">
                    <span x-show="!adding">Add to wallet</span>
                    <span x-show="adding">Adding…</span>
                </button>
            </div>

            <div x-show="!topupAvailable" class="flex items-start gap-4">
                <span class="brand-soft flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl">
                    <svg class="h-6 w-6 brand-text" aria-hidden="true"><use href="#i-clock"/></svg>
                </span>
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Top-ups are coming soon</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        We're connecting card and mobile money payments. In the meantime you can pay for trips with cash — any refunds or credits will show up here automatically.
                    </p>
                </div>
            </div>
        </div>

        {{-- Transactions --}}
        <div class="mt-8">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-extrabold text-slate-900 dark:text-white">Transactions</h2>
                <span x-show="!txLoading && transactions.length" class="chip !min-h-0 px-3 py-1 text-xs" x-text="transactions.length + ' recent'"></span>
            </div>

            <div x-show="txLoading" class="mt-3 space-y-2.5">
                <template x-for="i in 3" :key="i">
                    <div class="skeleton h-16 rounded-3xl"></div>
                </template>
            </div>

            <div x-show="!txLoading && transactions.length" class="mt-3 space-y-2.5">
                <template x-for="(t, i) in transactions" :key="t.id || i">
                    <div class="card flex items-center gap-3 p-4">
                        <span class="brand-soft flex h-10 w-10 shrink-0 items-center justify-center rounded-full">
                            <svg class="h-4 w-4 brand-text" aria-hidden="true"><use href="#i-wallet-bill"/></svg>
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold" x-text="t.label"></p>
                            <p class="truncate text-xs text-slate-500" x-text="t.date || t.id"></p>
                        </div>
                        <p class="shrink-0 text-sm font-extrabold" :class="t.type === 'debit' || t.type === 'DEBIT' ? 'text-danger' : 'brand-text'"
                           x-text="money(t.amount)"></p>
                    </div>
                </template>
            </div>

            <div x-show="!txLoading && !transactions.length" class="card mt-3 p-6 text-center">
                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 dark:bg-slate-800">
                    <svg class="h-6 w-6 text-slate-400" aria-hidden="true"><use href="#i-wallet-bill"/></svg>
                </span>
                <p class="mt-3 text-sm font-bold text-slate-900 dark:text-white">No transactions yet</p>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Trips, refunds and wallet credits will appear here.</p>
            </div>
        </div>
    </div>
@endcomponent
