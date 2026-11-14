@component('web.layouts.app', ['title' => 'Home', 'logo' => ''])
    <div x-data="homeScreen" class="pb-24 lg:pb-8">
        <div class="mx-auto max-w-3xl px-5 pt-7">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                        What do you need today<span x-show="$store.auth.user" x-cloak x-text="', ' + ($store.auth.user?.first_name || '')"></span>?
                    </h1>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Choose a service to start booking.</p>
                </div>
                <template x-if="$store.auth.user">
                    <a href="/app/profile"
                       class="brand-bg brand-contrast flex h-11 w-11 shrink-0 items-center justify-center rounded-full text-sm font-bold uppercase"
                       :aria-label="'Open profile'"
                       x-text="$store.auth.user?.first_name?.[0] || 'U'"></a>
                </template>
            </div>

            {{-- Location + search --}}
            <div class="card mt-6 space-y-4 p-4 sm:p-5">
                <div class="flex items-center gap-3">
                    <span class="brand-soft flex h-10 w-10 shrink-0 items-center justify-center rounded-xl">
                        <svg class="h-5 w-5 brand-text" aria-hidden="true"><use href="#i-pin"/></svg>
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Your area</p>
                        <p class="truncate text-sm font-semibold text-slate-800 dark:text-slate-100" x-show="!locating && address" x-text="address"></p>
                        <p class="truncate text-sm font-medium text-slate-500 dark:text-slate-400" x-show="locating">Finding your location…</p>
                        <p class="truncate text-sm font-medium text-slate-500 dark:text-slate-400" x-show="!locating && !address">Location off — search an area below</p>
                    </div>
                    <span x-show="locating" class="h-2.5 w-2.5 shrink-0 animate-pulse rounded-full bg-accent-500"></span>
                    <span x-show="!locating && address" class="h-2.5 w-2.5 shrink-0 rounded-full bg-brand-500"></span>
                </div>

                <label class="block">
                    <span class="input-label">Search for an area</span>
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" aria-hidden="true"><use href="#i-search"/></svg>
                        <input type="text" class="input pl-10" placeholder="e.g. Kabulonga, Lusaka" x-model="searchTerm" @keydown.enter="search()">
                    </div>
                </label>
                <div x-show="searching" class="text-sm text-slate-500">Searching…</div>
                <ul x-show="results.length" class="overflow-hidden rounded-2xl border border-slate-200 p-0 dark:border-slate-700">
                    <template x-for="(p, i) in results" :key="i">
                        <li class="border-b border-slate-100 last:border-b-0 dark:border-slate-800">
                            <button type="button" class="flex w-full items-start gap-2.5 px-4 py-3 text-left text-sm hover:bg-slate-50 dark:hover:bg-slate-800"
                                    @click="usePlace(p)">
                                <svg class="mt-0.5 h-4 w-4 shrink-0 text-slate-400" aria-hidden="true"><use href="#i-pin"/></svg>
                                <span x-text="p.label"></span>
                            </button>
                        </li>
                    </template>
                </ul>
            </div>

            {{-- Services --}}
            <div class="mt-8 flex items-end justify-between gap-3">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900 dark:text-white">Services near you</h2>
                    <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">Tap a card to set up your booking.</p>
                </div>
                <span x-show="!loading && segments.length" class="chip !min-h-0 shrink-0 px-3 py-1 text-xs" x-text="segments.length + (segments.length === 1 ? ' service' : ' services')"></span>
            </div>

            <div x-show="loading" class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">
                <template x-for="i in 6" :key="i">
                    <div class="skeleton h-32 rounded-3xl"></div>
                </template>
            </div>

            <div x-show="!loading && segments.length" class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">
                <template x-for="seg in segments" :key="seg.segment_id">
                    <button type="button"
                            class="group relative flex min-h-32 flex-col overflow-hidden rounded-3xl border border-slate-200 p-4 text-left transition duration-200 hover:-translate-y-1 hover:shadow-lifted active:scale-[0.98] dark:border-slate-700"
                            :style="'background:linear-gradient(135deg,' + seg.gradient_1 + ',' + seg.gradient_2 + ')'"
                            @click="choose(seg)">
                        <template x-if="seg.icon">
                            <img :src="seg.icon" alt="" class="h-11 w-11 rounded-xl bg-white/80 object-contain p-1.5" loading="lazy">
                        </template>
                        <template x-if="!seg.icon">
                            <span class="brand-bg brand-contrast flex h-11 w-11 items-center justify-center rounded-xl text-base font-extrabold"
                                  x-text="seg.name.slice(0, 1)"></span>
                        </template>
                        <p class="mt-3 text-base font-extrabold text-slate-900" x-text="seg.name"></p>
                        <p class="mt-auto flex items-center gap-1 pt-2 text-xs font-bold text-slate-700">
                            Book now
                            <svg class="h-3.5 w-3.5 transition-transform duration-200 group-hover:translate-x-0.5" aria-hidden="true"><use href="#i-chevron-right"/></svg>
                        </p>
                    </button>
                </template>
            </div>

            {{-- Empty / error states --}}
            <div x-show="!loading && !segments.length && error" class="card mt-4 p-6 text-center">
                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 dark:bg-slate-800">
                    <svg class="h-6 w-6 text-slate-400" aria-hidden="true"><use href="#i-map"/></svg>
                </span>
                <p class="mt-3 text-base font-bold text-slate-900 dark:text-white">We couldn't load services</p>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400" x-text="error"></p>
                <div class="mt-4 flex flex-col justify-center gap-2 sm:flex-row">
                    <button type="button" class="btn-primary text-sm" @click="retryLocation()">Use my location</button>
                    <button type="button" class="btn-ghost text-sm" @click="retry()">Try again</button>
                </div>
            </div>

            <div x-show="!loading && !segments.length && !error" class="card mt-4 p-6 text-center">
                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 dark:bg-slate-800">
                    <svg class="h-6 w-6 text-slate-400" aria-hidden="true"><use href="#i-pin"/></svg>
                </span>
                <p class="mt-3 text-base font-bold text-slate-900 dark:text-white">No services at this exact spot yet</p>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Try refreshing your location, or search for a nearby area above. We're expanding coverage all the time.</p>
                <button type="button" class="btn-primary mt-4 text-sm" @click="retryLocation()">Use my location</button>
            </div>
        </div>
    </div>
@endcomponent
