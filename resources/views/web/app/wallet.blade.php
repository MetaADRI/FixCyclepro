@component('web.layouts.app', ['title' => 'Wallet', 'logo' => ''])
    <div x-data="walletScreen" class="mx-auto max-w-2xl px-5 pb-28 pt-6 lg:pb-8">
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">Wallet</h1>

        <div class="brand-bg card mt-5 border-0 text-white shadow-card">
            <p class="text-sm font-medium text-white/80">Available balance</p>
            <p class="mt-1 text-4xl font-extrabold" x-text="balance()"></p>
        </div>

        <div class="card mt-5 space-y-3">
            <h2 class="text-base font-bold text-slate-900 dark:text-white">Add money</h2>
            <input type="number" inputmode="decimal" min="0" step="0.01" class="input" placeholder="Amount" x-model="amount">
            <template x-if="options.length">
                <select class="input" x-model="payment_option_id">
                    <template x-for="o in options" :key="o.id">
                        <option :value="o.id" x-text="o.name"></option>
                    </template>
                </select>
            </template>
            <p x-show="error" x-text="error" class="text-sm font-medium text-danger"></p>
            <button type="button" class="btn-primary w-full" @click="add()" x-bind:disabled="adding">
                <span x-show="!adding">Add to wallet</span>
                <span x-show="adding">Adding…</span>
            </button>
        </div>

        <div class="mt-6">
            <h2 class="text-base font-bold text-slate-900 dark:text-white">Transactions</h2>
            <div x-show="txLoading" class="mt-3 space-y-3">
                <template x-for="i in 3" :key="i">
                    <div class="skeleton h-14 rounded-2xl"></div>
                </template>
            </div>
            <div x-show="!txLoading && transactions.length" class="mt-3 space-y-2">
                <template x-for="(t, i) in transactions" :key="t.id || i">
                    <div class="card flex items-center gap-3 !p-4">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-500 dark:bg-slate-800">
                            <svg class="h-4 w-4" aria-hidden="true"><use href="#i-wallet-bill"/></svg>
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold" x-text="t.label"></p>
                            <p class="truncate text-xs text-slate-500" x-text="t.date || t.id"></p>
                        </div>
                        <p class="text-sm font-extrabold" :class="t.type === 'debit' || t.type === 'DEBIT' ? 'text-danger' : 'brand-text'"
                           x-text="t.amount !== null && t.amount !== undefined ? t.amount : ''"></p>
                    </div>
                </template>
            </div>
            <div x-show="!txLoading && !transactions.length" class="card mt-3 text-center">
                <p class="text-sm text-slate-500">No transactions yet.</p>
            </div>
        </div>
    </div>
@endcomponent