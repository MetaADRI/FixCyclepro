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
                <div class="card overflow-hidden p-0">
                    <div x-ref="map" class="h-64 w-full bg-slate-100 dark:bg-slate-800"></div>
                    <div class="flex items-start justify-between gap-3 px-4 py-3">
                        <div class="min-w-0">
                            <p class="text-xs uppercase tracking-wide text-slate-400">Heading to</p>
                            <p class="truncate text-sm font-semibold" x-text="headingTo() || 'Live location'"></p>
                        </div>
                        <a x-show="targetLatLng()"
                           :href="'https://www.openstreetmap.org/?mlat=' + (targetLatLng()?.lat || '') + '&mlon=' + (targetLatLng()?.lng || '') + '#map=16/' + (targetLatLng()?.lat || '') + '/' + (targetLatLng()?.lng || '')"
                           target="_blank" rel="noopener"
                           class="btn-ghost shrink-0 text-xs">Open map</a>
                    </div>
                </div>

                <div class="card space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="chip chip-active" x-text="statusText(booking.booking_status)"></span>
                        <span class="text-xs font-semibold text-slate-400">
                            Booking #<span x-text="bookingId"></span>
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-3" x-show="liveEta() || liveDistance()">
                        <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-800">
                            <p class="text-xs uppercase tracking-wide text-slate-400">ETA</p>
                            <p class="text-sm font-bold" x-text="liveEta() || '—'"></p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-800">
                            <p class="text-xs uppercase tracking-wide text-slate-400">Distance</p>
                            <p class="text-sm font-bold" x-text="liveDistance() || '—'"></p>
                        </div>
                    </div>
                </div>

                <template x-if="liveDriverName()">
                    <div class="card space-y-3">
                        <div class="flex items-center gap-3">
                            <template x-if="booking.location_updates && booking.location_updates.driver_image">
                                <img :src="booking.location_updates.driver_image" alt="" class="h-12 w-12 rounded-full object-cover" loading="lazy">
                            </template>
                            <template x-if="!booking.location_updates || !booking.location_updates.driver_image">
                                <span class="brand-bg brand-contrast flex h-12 w-12 items-center justify-center rounded-full text-base font-bold uppercase"
                                      x-text="liveDriverName().charAt(0) || 'D'"></span>
                            </template>
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-bold" x-text="liveDriverName()"></p>
                                <p class="text-sm text-slate-500" x-text="liveVehicle()"></p>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400" x-text="liveVehicleNumber()"></p>
                            </div>
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