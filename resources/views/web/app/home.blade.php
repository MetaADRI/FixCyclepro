@component('web.layouts.app', ['title' => 'Home', 'logo' => ''])
    <div x-data="homeScreen" class="pb-24 lg:pb-8">
        <div class="mx-auto max-w-3xl px-5 pt-6">
            <template x-if="!error">
                <div>
                    <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">What do you need today?</h1>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Choose a service to start booking.</p>
                </div>
            </template>
            <template x-if="error">
                <div class="card mt-2">
                    <p class="text-sm font-semibold text-danger" x-text="error"></p>
                    <button type="button" class="btn-ghost mt-3 text-sm" @click="retry()">Try again</button>
                </div>
            </template>

            <div class="mt-5">
                <label class="block">
                    <span class="input-label">Delivery area</span>
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" aria-hidden="true"><use href="#i-search"/></svg>
                        <input type="text" class="input pl-10" placeholder="Search for an area…" x-model="searchTerm" @keydown.enter="search()">
                    </div>
                </label>
                <div x-show="searching" class="mt-2 text-sm text-slate-500">Searching…</div>
                <ul x-show="results.length" class="card mt-2 divide-y divide-slate-100 overflow-hidden p-0 dark:divide-slate-800">
                    <template x-for="(p, i) in results" :key="i">
                        <li>
                            <button type="button" class="flex w-full items-start gap-2 px-4 py-3 text-left text-sm hover:bg-slate-50 dark:hover:bg-slate-800"
                                    @click="usePlace(p)">
                                <svg class="mt-0.5 h-4 w-4 shrink-0 text-slate-400" aria-hidden="true"><use href="#i-pin"/></svg>
                                <span x-text="p.label"></span>
                            </button>
                        </li>
                    </template>
                </ul>
            </div>

            <div class="mt-5 flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                <span x-show="locating" class="flex items-center gap-2">
                    <svg class="h-4 w-4 animate-spin text-slate-400" aria-hidden="true"><use href="#i-clock"/></svg>
                    Finding your location…
                </span>
                <span x-show="!locating && address" class="flex min-w-0 items-center gap-2">
                    <svg class="h-4 w-4 shrink-0 brand-text" aria-hidden="true"><use href="#i-pin"/></svg>
                    <span class="truncate" x-text="address"></span>
                </span>
            </div>

            <div x-show="loading" class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-3">
                <template x-for="i in 6" :key="i">
                    <div class="skeleton h-28 rounded-3xl"></div>
                </template>
            </div>

            <div x-show="!loading && segments.length" class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-3">
                <template x-for="seg in segments" :key="seg.segment_id">
                    <button type="button"
                            class="group relative overflow-hidden rounded-3xl border border-slate-200 p-5 text-left transition active:scale-[0.98] dark:border-slate-700"
                            :style="'background:linear-gradient(135deg,' + seg.gradient_1 + ',' + seg.gradient_2 + ')'"
                            @click="choose(seg)">
                        <template x-if="seg.icon">
                            <img :src="seg.icon" alt="" class="h-10 w-10 rounded-xl object-contain bg-white/70 p-1" loading="lazy">
                        </template>
                        <template x-if="!seg.icon">
                            <span class="brand-bg brand-contrast flex h-10 w-10 items-center justify-center rounded-xl"
                                  x-text="seg.name.slice(0, 1)"></span>
                        </template>
                        <p class="mt-4 font-bold text-slate-900" x-text="seg.name"></p>
                        <p class="mt-0.5 text-xs font-medium text-slate-500">Book now →</p>
                    </button>
                </template>
            </div>

            <div x-show="!loading && !segments.length && !error" class="card mt-6 text-center">
                <p class="text-sm text-slate-500">No services found for your current area.</p>
            </div>
        </div>
    </div>
@endcomponent