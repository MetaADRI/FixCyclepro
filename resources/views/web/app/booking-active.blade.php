@component('web.layouts.app', ['title' => 'Trip in progress', 'logo' => ''])
    <script>
        window.bookingId = @json($booking ?? null);
    </script>

    <div x-data="activeBooking" class="mx-auto max-w-2xl px-5 pb-28 pt-6 lg:pb-8">
        <a href="/app/bookings" class="mb-4 inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 hover:text-slate-700 dark:text-slate-400">
            <svg class="h-4 w-4" aria-hidden="true"><use href="#i-back"/></svg>
            My bookings
        </a>

        <p x-show="error" class="mb-4 rounded-2xl border border-danger/30 bg-danger/10 px-4 py-3 text-sm font-medium text-danger" x-text="error"></p>

        <template x-if="loading && !booking">
            <div class="space-y-4">
                <div class="skeleton h-40 rounded-3xl"></div>
                <div class="skeleton h-20 rounded-3xl"></div>
            </div>
        </template>

        <template x-if="!loading && !booking && !error">
            <div class="card">
                <p class="text-sm text-slate-500">No active trip selected.</p>
                <a href="/app/bookings" class="btn-primary mt-4 inline-block text-sm">View my bookings</a>
            </div>
        </template>

        <template x-if="booking">
            <div class="space-y-5">
                <div class="card space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="chip chip-active" x-text="statusText(booking.booking_status)"></span>
                        <span x-show="booking.merchant_booking_id" class="text-xs font-semibold text-slate-400">
                            ID #<span x-text="booking.merchant_booking_id"></span>
                        </span>
                    </div>

                    <div x-show="booking.pickup_location" class="flex items-start gap-3">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 brand-text" aria-hidden="true"><use href="#i-pin"/></svg>
                        <div class="min-w-0">
                            <p class="text-xs uppercase tracking-wide text-slate-400">Pickup</p>
                            <p class="truncate text-sm font-semibold" x-text="booking.pickup_location"></p>
                        </div>
                    </div>
                    <div x-show="booking.drop_location" class="flex items-start gap-3">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 accent-text" aria-hidden="true"><use href="#i-navigation"/></svg>
                        <div class="min-w-0">
                            <p class="text-xs uppercase tracking-wide text-slate-400">Drop-off</p>
                            <p class="truncate text-sm font-semibold" x-text="booking.drop_location"></p>
                        </div>
                    </div>
                </div>

                <template x-if="booking.driver">
                    <div class="card space-y-3">
                        <div class="flex items-center gap-3">
                            <template x-if="booking.driver.profile_image">
                                <img :src="booking.driver.profile_image" alt="" class="h-12 w-12 rounded-full object-cover" loading="lazy">
                            </template>
                            <template x-if="!booking.driver.profile_image">
                                <span class="brand-bg brand-contrast flex h-12 w-12 items-center justify-center rounded-full text-base font-bold uppercase"
                                      x-text="((booking.driver.first_name || '') + ' ' + (booking.driver.last_name || '')).trim() || 'D'"></span>
                            </template>
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-bold" x-text="(booking.driver.first_name || '') + ' ' + (booking.driver.last_name || '')"></p>
                                <p class="text-sm text-slate-500">
                                    <span x-text="booking.driver.rating ?? '—'"></span>
                                    <svg class="mb-0.5 inline h-4 w-4 text-amber-400" aria-hidden="true"><use href="#i-star"/></svg>
                                </p>
                            </div>
                            <a x-show="booking.driver.phoneNumber"
                               :href="'tel:' + booking.driver.phoneNumber"
                               class="btn-ghost flex h-11 w-11 items-center justify-center rounded-2xl">
                                <svg class="h-5 w-5" aria-hidden="true"><use href="#i-phone"/></svg>
                            </a>
                        </div>
                    </div>
                </template>

                <div class="card" x-show="!isDone()">
                    <button type="button" class="btn-ghost w-full text-danger"
                            @click="showCancel = !showCancel">
                        Cancel booking
                    </button>

                    <div x-show="showCancel" class="mt-4 space-y-3 border-t border-slate-100 pt-4 dark:border-slate-800">
                        <template x-if="!reasons.length">
                            <p class="text-sm text-slate-500">Loading cancel reasons…</p>
                        </template>
                        <template x-for="r in reasons" :key="r.id">
                            <label class="flex items-center gap-2.5 text-sm">
                                <input type="radio" name="cancel-reason" :value="r.id" x-model="reason_id" class="accent-brand">
                                <span x-text="r.reason"></span>
                            </label>
                        </template>
                        <p x-show="error" x-text="error" class="text-sm font-medium text-danger"></p>
                        <button type="button" class="btn-danger w-full" @click="cancel()" x-bind:disabled="cancelling">
                            <span x-show="!cancelling">Confirm cancellation</span>
                            <span x-show="cancelling">Cancelling…</span>
                        </button>
                    </div>
                </div>

                <div x-show="isDone()" class="card">
                    <p class="text-sm text-slate-500">This trip has ended.</p>
                    <a href="/app/bookings" class="btn-ghost mt-3 inline-block text-sm">Back to bookings</a>
                </div>
            </div>
        </template>
    </div>
@endcomponent