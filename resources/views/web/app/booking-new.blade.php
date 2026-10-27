@component('web.layouts.app', ['title' => 'Book a service', 'logo' => ''])
    <div x-data="bookingFlow" class="mx-auto max-w-2xl px-5 pb-28 pt-6 lg:pb-8">
        <a href="/app" class="mb-4 inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 hover:text-slate-700 dark:text-slate-400">
            <svg class="h-4 w-4" aria-hidden="true"><use href="#i-back"/></svg>
            Back
        </a>

        <p x-show="error" class="mb-4 rounded-2xl border border-danger/30 bg-danger/10 px-4 py-3 text-sm font-medium text-danger" x-text="error"></p>

        <template x-if="!segment">
            <div class="card">
                <p class="text-sm text-slate-500">Choose a service from the home screen first to start booking.</p>
                <a href="/app" class="btn-primary mt-4 inline-block text-sm">Browse services</a>
            </div>
        </template>

        <template x-if="segment">
            <div class="space-y-6">
                <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <span class="chip chip-active" x-text="segment.name"></span>
                    <span x-show="locating" class="flex items-center gap-1.5">
                        <svg class="h-4 w-4 animate-spin text-slate-400" aria-hidden="true"><use href="#i-clock"/></svg>
                        locating…
                    </span>
                </div>

                {{-- addresses --}}
                <div class="card space-y-4">
                    <div>
                        <span class="input-label">Pickup</span>
                        <p class="text-sm font-semibold text-slate-700 dark:text-slate-200" x-text="pickup || 'Using your location'"></p>
                    </div>

                    <div class="border-t border-slate-100 pt-4 dark:border-slate-800">
                        <span class="input-label">Drop-off</span>
                        <template x-for="(row, i) in dropRows" :key="i">
                            <div class="mt-2 flex items-center gap-2">
                                <input type="text" class="input flex-1"
                                       :placeholder="i === 0 ? 'Where to? (optional)' : 'Stop ' + (i + 1)"
                                       x-model="row.label">
                                <button type="button" x-show="dropRows.length > 1"
                                        class="btn-ghost flex h-10 w-10 items-center justify-center rounded-xl text-slate-400"
                                        @click="removeDrop(i)" aria-label="Remove stop">
                                    <svg class="h-4 w-4" aria-hidden="true"><use href="#i-x"/></svg>
                                </button>
                            </div>
                        </template>
                        <button type="button" x-show="dropRows.length < 4"
                                class="mt-2 inline-flex items-center gap-1.5 text-sm font-semibold brand-text"
                                @click="addDrop()">
                            <svg class="h-4 w-4" aria-hidden="true"><use href="#i-plus"/></svg>
                            Add a stop
                        </button>

                        <div class="mt-3">
                            <div class="relative">
                                <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" aria-hidden="true"><use href="#i-search"/></svg>
                                <input type="text" class="input pl-10" placeholder="Search drop-off address…"
                                       x-model="term" @keydown.enter="searchFor(0)">
                            </div>
                            <ul x-show="results.length" class="card mt-2 divide-y divide-slate-100 overflow-hidden p-0 dark:divide-slate-800">
                                <template x-for="(p, i) in results" :key="i">
                                    <li>
                                        <button type="button"
                                                class="flex w-full items-start gap-2 px-4 py-3 text-left text-sm hover:bg-slate-50 dark:hover:bg-slate-800"
                                                @click="dropRows.length ? applyResult(p, 0) : setMainDrop(p)">
                                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-slate-400" aria-hidden="true"><use href="#i-pin"/></svg>
                                            <span x-text="p.label"></span>
                                        </button>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- route preview (OpenStreetMap + OSRM, no API key) --}}
                <div class="card overflow-hidden p-0" x-show="coords">
                    <div x-ref="previewMap" class="h-52 w-full bg-slate-100 dark:bg-slate-800"></div>
                    <p class="px-4 py-2.5 text-xs font-medium text-slate-500 dark:text-slate-400"
                       x-show="previewInfo" x-text="previewInfo"></p>
                </div>

                <div class="card">
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Choose a service</h2>

                    <p x-show="carsLoading" class="mt-3 flex items-center gap-2 text-sm text-slate-500">
                        <svg class="h-4 w-4 animate-spin text-slate-400" aria-hidden="true"><use href="#i-clock"/></svg>
                        Loading services in your area…
                    </p>
                    <p x-show="carsError && !carsLoading" class="mt-3 text-sm font-medium text-danger" x-text="carsError"></p>

                    <div x-show="!carsLoading && !carsError && serviceTypes.length" class="mt-4 space-y-4">
                        <template x-for="(svc, i) in serviceTypes" :key="svc.id">
                            <div>
                                <button type="button"
                                        class="flex w-full items-center justify-between rounded-2xl border px-4 py-3 text-left transition"
                                        :class="activeService && activeService.id === svc.id ? 'border-brand bg-brand/5' : 'border-slate-200 dark:border-slate-700'"
                                        @click="pickService(svc)">
                                    <span class="flex items-center gap-3">
                                        <span :class="activeService && activeService.id === svc.id ? 'brand-bg brand-contrast' : 'bg-slate-100 text-slate-500 dark:bg-slate-800'"
                                              class="flex h-9 w-9 items-center justify-center rounded-xl text-sm font-bold">
                                            <template x-if="svc.sequence"><span x-text="svc.sequence"></span></template>
                                            <template x-if="!svc.sequence"><span>×</span></template>
                                        </span>
                                        <span class="font-semibold" x-text="svc.serviceName"></span>
                                    </span>
                                    <svg class="h-5 w-5 text-slate-400" aria-hidden="true"><use href="#i-chevron-down"/></svg>
                                </button>

                                <template x-if="activeService && activeService.id === svc.id && svc.vehicles.length">
                                    <div class="mt-2 grid grid-cols-1 gap-2 pl-2 sm:grid-cols-2">
                                        <template x-if="!svc.vehicles[0].category">
                                            <template x-for="(v, vi) in svc.vehicles" :key="vi">
                                                <button type="button"
                                                        class="flex items-center gap-3 rounded-2xl border px-3 py-2.5 text-left transition"
                                                        :class="activeVehicle && activeVehicle.id === v.id ? 'border-brand bg-brand/5' : 'border-slate-200 dark:border-slate-700'"
                                                        @click="pickService(svc); activeVehicle = v">
                                                    <template x-if="v.image">
                                                        <img :src="v.image" alt="" class="h-10 w-10 rounded-xl object-contain bg-white shadow-sm" loading="lazy">
                                                    </template>
                                                    <template x-if="!v.image">
                                                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-400 dark:bg-slate-800">
                                                            <svg class="h-5 w-5" aria-hidden="true"><use href="#i-navigation"/></svg>
                                                        </span>
                                                    </template>
                                                    <span class="min-w-0">
                                                        <span class="block truncate text-sm font-semibold" x-text="v.name"></span>
                                                        <span x-show="v.description" class="block truncate text-xs text-slate-500" x-text="v.description"></span>
                                                    </span>
                                                </button>
                                            </template>
                                        </template>
                                        <template x-if="svc.vehicles[0].category">
                                            <template x-for="(c, ci) in svc.vehicles" :key="ci">
                                                <div>
                                                    <button type="button"
                                                            class="flex w-full items-center gap-3 rounded-2xl border px-3 py-2.5 text-left transition"
                                                            :class="activeCategory && activeCategory.category_id === c.category_id ? 'border-brand bg-brand/5' : 'border-slate-200 dark:border-slate-700'"
                                                            @click="pickCategory(c)">
                                                        <template x-if="c.image">
                                                            <img :src="c.image" alt="" class="h-10 w-10 rounded-xl object-contain bg-white shadow-sm" loading="lazy">
                                                        </template>
                                                        <span class="min-w-0 flex-1">
                                                            <span class="block truncate text-sm font-semibold" x-text="c.name"></span>
                                                        </span>
                                                        <svg class="h-5 w-5 text-slate-400" aria-hidden="true"><use href="#i-chevron-down"/></svg>
                                                    </button>
                                                    <template x-if="activeCategory && activeCategory.category_id === c.category_id && c.vehicles.length">
                                                        <div class="mt-2 grid grid-cols-1 gap-2 pl-2">
                                                            <template x-for="(v, vi) in c.vehicles" :key="vi">
                                                                <button type="button"
                                                                        class="flex items-center gap-3 rounded-2xl border px-3 py-2.5 text-left transition"
                                                                        :class="activeVehicle && activeVehicle.id === v.id ? 'border-brand bg-brand/5' : 'border-slate-200 dark:border-slate-700'"
                                                                        @click="activeVehicle = v">
                                                                    <template x-if="v.image">
                                                                        <img :src="v.image" alt="" class="h-10 w-10 rounded-xl object-contain bg-white shadow-sm" loading="lazy">
                                                                    </template>
                                                                    <template x-if="!v.image">
                                                                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-400 dark:bg-slate-800">
                                                                            <svg class="h-5 w-5" aria-hidden="true"><use href="#i-navigation"/></svg>
                                                                        </span>
                                                                    </template>
                                                                    <span class="block truncate text-sm font-semibold" x-text="v.name"></span>
                                                                </button>
                                                            </template>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- estimate & payment --}}
                <template x-if="!carsLoading && !carsError && serviceTypes.length && activeVehicle && activeVehicle.id">
                    <div class="card space-y-4">
                        <button type="button" class="btn-primary w-full" @click="estimateBooking()" x-bind:disabled="estimating">
                            <span x-show="!estimating">Get fare estimate</span>
                            <span x-show="estimating">Calculating…</span>
                        </button>

                        <div x-show="estimate" class="rounded-2xl border border-slate-200 p-4 dark:border-slate-700">
                            <div class="flex items-center justify-between">
                                <p class="text-sm text-slate-500">Estimated fare</p>
                                <p class="text-xl font-extrabold brand-text" x-text="money(estimate && estimate.bill ? estimate.bill : 0)"></p>
                            </div>

                            <div class="mt-4">
                                <span class="input-label">Payment method</span>
                                <div class="mt-2 grid grid-cols-3 gap-2">
                                    <template x-for="m in paymentMethods" :key="m.id">
                                        <button type="button"
                                                class="chip"
                                                :class="payment_method_id === m.id && 'chip-active'"
                                                @click="payment_method_id = m.id; payment_option_id = null">
                                            <span x-text="m.label"></span>
                                        </button>
                                    </template>
                                    <button type="button" x-show="paymentOption.length"
                                            class="chip"
                                            :class="payment_method_id === 4 && 'chip-active'"
                                            @click="payment_method_id = 4">
                                        Online
                                    </button>
                                </div>
                                <div x-show="payment_method_id === 4 && paymentOption.length" class="mt-2">
                                    <select class="input" x-model="payment_option_id">
                                        <option :value="null">Choose a gateway…</option>
                                        <template x-for="o in paymentOption" :key="o.id">
                                            <option :value="o.id" x-text="o.name"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <button type="button" x-show="estimate && checkout"
                                class="btn-primary w-full"
                                @click="confirm()"
                                x-bind:disabled="processing">
                            <span x-show="!processing">Confirm booking</span>
                            <span x-show="processing">Booking…</span>
                        </button>
                    </div>
                </template>
            </div>
        </template>
    </div>
@endcomponent