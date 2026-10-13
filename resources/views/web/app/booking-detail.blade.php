@component('web.layouts.app', ['title' => 'Booking details', 'logo' => ''])
    <script>
        window.bookingId = @json($booking ?? null);
    </script>

    <div x-data="bookingDetail" class="mx-auto max-w-2xl px-5 pb-28 pt-6 lg:pb-8">
        <a href="/app/bookings" class="mb-4 inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 hover:text-slate-700 dark:text-slate-400">
            <svg class="h-4 w-4" aria-hidden="true"><use href="#i-back"/></svg>
            My bookings
        </a>

        <div x-show="loading" class="space-y-4">
            <div class="skeleton h-40 rounded-3xl"></div>
            <div class="skeleton h-20 rounded-3xl"></div>
        </div>

        <p x-show="error && !loading" class="mb-4 rounded-2xl border border-danger/30 bg-danger/10 px-4 py-3 text-sm font-medium text-danger" x-text="error"></p>

        <template x-if="booking">
            <div class="space-y-5">
                <div class="card space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="chip chip-active" x-text="statusText(booking.booking_status)"></span>
                        <span x-show="booking.merchant_booking_id" class="text-xs font-semibold text-slate-400">
                            ID #<span x-text="booking.merchant_booking_id"></span>
                        </span>
                    </div>
                    <div x-show="booking.estimate_bill" class="rounded-2xl border border-slate-200 p-4 text-center dark:border-slate-700">
                        <p class="text-xs uppercase tracking-wide text-slate-400">Estimated fare</p>
                        <p class="mt-1 text-2xl font-extrabold brand-text" x-text="money(booking.estimate_bill)"></p>
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
                    <div class="card flex items-center gap-3">
                        <template x-if="booking.driver.profile_image">
                            <img :src="booking.driver.profile_image" alt="" class="h-12 w-12 rounded-full object-cover" loading="lazy">
                        </template>
                        <template x-if="!booking.driver.profile_image">
                            <span class="brand-bg brand-contrast flex h-12 w-12 items-center justify-center rounded-full text-base font-bold uppercase"
                                  x-text="((booking.driver.first_name || '') + ' ' + (booking.driver.last_name || '')).trim() || 'D'"></span>
                        </template>
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-bold" x-text="(booking.driver.first_name || '') + ' ' + (booking.driver.last_name || '')"></p>
                            <p class="text-sm text-slate-500">Driver</p>
                        </div>
                        <a x-show="booking.driver.phoneNumber" :href="'tel:' + booking.driver.phoneNumber"
                           class="btn-ghost flex h-11 w-11 items-center justify-center rounded-2xl">
                            <svg class="h-5 w-5" aria-hidden="true"><use href="#i-phone"/></svg>
                        </a>
                    </div>
                </template>
            </div>
        </template>
    </div>
@endcomponent