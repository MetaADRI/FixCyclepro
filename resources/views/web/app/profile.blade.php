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
                {{-- Deletion scheduled state: everything else is hidden --}}
                <template x-if="deletionScheduled">
                    <div class="card space-y-4 p-6">
                        <div class="flex items-center gap-3">
                            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-red-100 dark:bg-red-950/50">
                                <svg class="h-6 w-6 text-danger" aria-hidden="true"><use href="#i-logout"/></svg>
                            </span>
                            <h2 class="text-lg font-extrabold text-slate-900 dark:text-white">Account deletion scheduled</h2>
                        </div>
                        <p class="text-sm text-slate-600 dark:text-slate-300">
                            Your account will be permanently deleted
                            <span class="font-semibold" x-show="deletionDate" x-text="'on ' + deletionDate"></span>
                            after a 30-day grace period. Your data has been hidden and you have been signed out everywhere.
                        </p>
                        <p class="text-sm text-slate-600 dark:text-slate-300">
                            Changed your mind? Simply sign in with your password before the grace period ends and the deletion will be cancelled automatically.
                        </p>
                        <button type="button" class="btn-primary w-full" @click="finishDeletion()">Sign out</button>
                    </div>
                </template>

                <template x-if="!deletionScheduled">
                    <div class="space-y-5">
                        <div class="card flex items-center gap-4">
                            <span class="brand-bg brand-contrast flex h-16 w-16 items-center justify-center rounded-2xl text-xl font-extrabold" x-text="initials()"></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-lg font-extrabold text-slate-900 dark:text-white"
                                   x-text="(user.first_name || '') + ' ' + (user.last_name || '')"></p>
                                <p class="truncate text-sm text-slate-500" x-text="user.email || user.UserPhone || ''"></p>
                            </div>
                            <button type="button" class="btn-ghost !min-h-0 !min-w-0 shrink-0 rounded-xl px-3 py-2 text-xs"
                                    @click="editing ? (editing = false) : startEdit()"
                                    x-text="editing ? 'Close' : 'Edit'"></button>
                        </div>

                        {{-- Edit profile form --}}
                        <div class="card space-y-4 p-5" x-show="editing" x-cloak>
                            <h2 class="text-base font-extrabold text-slate-900 dark:text-white">Edit profile</h2>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <label class="block">
                                    <span class="input-label">First name</span>
                                    <input type="text" class="input" x-model="form.first_name" autocomplete="given-name">
                                </label>
                                <label class="block">
                                    <span class="input-label">Last name</span>
                                    <input type="text" class="input" x-model="form.last_name" autocomplete="family-name">
                                </label>
                            </div>
                            <label class="block">
                                <span class="input-label">Email address</span>
                                <input type="email" class="input" x-model="form.email" autocomplete="email">
                            </label>
                            <label class="block">
                                <span class="input-label">Phone number</span>
                                <input type="tel" class="input" x-model="form.phone" autocomplete="tel">
                            </label>
                            <p x-show="editError" x-cloak x-text="editError" class="text-sm font-medium text-danger"></p>
                            <div class="flex gap-3">
                                <button type="button" class="btn-primary flex-1" @click="saveEdit()" x-bind:disabled="saving">
                                    <span x-show="!saving">Save changes</span>
                                    <span x-show="saving">Saving…</span>
                                </button>
                                <button type="button" class="btn-ghost" @click="editing = false" x-bind:disabled="saving">Cancel</button>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="card">
                                <p class="text-xs uppercase tracking-wide text-slate-400">Wallet</p>
                                <p class="mt-1 text-xl font-extrabold brand-text"
                                   x-text="user.wallet_balance !== null && user.wallet_balance !== undefined ? money(user.wallet_balance) : '—'"></p>
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

                        {{-- Delete account --}}
                        <div class="card space-y-3 p-5">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-sm font-bold text-slate-900 dark:text-white">Delete account</p>
                                    <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Permanently remove your account and data.</p>
                                </div>
                                <button type="button" class="btn-danger !min-h-0 !min-w-0 shrink-0 rounded-xl px-3 py-2 text-xs"
                                        @click="deleteModal = true; deleteError = ''">Delete</button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </template>

        {{-- Delete account confirmation modal --}}
        <div x-show="deleteModal" x-cloak class="fixed inset-0 z-50 flex items-end justify-center bg-slate-900/60 p-4 sm:items-center"
             @keydown.escape.window="deleteModal = false" role="dialog" aria-modal="true" aria-label="Delete account">
            <div class="card w-full max-w-md space-y-4 p-6">
                <h2 class="text-lg font-extrabold text-slate-900 dark:text-white">Delete your account?</h2>
                <p class="text-sm text-slate-600 dark:text-slate-300">
                    This starts a 30-day grace period, after which your account and personal data are permanently deleted.
                    Signing in with your password during the grace period cancels the deletion.
                </p>
                <label class="block">
                    <span class="input-label">Password</span>
                    <input type="password" class="input" autocomplete="current-password" placeholder="••••••••"
                           x-model="deletePassword" @keydown.enter="requestDeletion()">
                </label>
                <label class="block">
                    <span class="input-label">Reason (optional)</span>
                    <textarea class="input min-h-20 py-3" rows="3" maxlength="1000" placeholder="Tell us why you're leaving"
                              x-model="deleteReason"></textarea>
                </label>
                <p x-show="deleteError" x-cloak x-text="deleteError" class="text-sm font-medium text-danger"></p>
                <div class="flex gap-3">
                    <button type="button" class="btn-danger flex-1" @click="requestDeletion()" x-bind:disabled="deleting">
                        <span x-show="!deleting">Delete my account</span>
                        <span x-show="deleting">Deleting…</span>
                    </button>
                    <button type="button" class="btn-ghost" @click="deleteModal = false" x-bind:disabled="deleting">Keep account</button>
                </div>
            </div>
        </div>
    </div>
@endcomponent
