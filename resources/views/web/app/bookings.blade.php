@component('web.layouts.app', ['title' => 'My bookings', 'logo' => ''])
    <div x-data="bookingsScreen" class="mx-auto max-w-2xl px-5 pb-28 pt-7 lg:pb-8">
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">My bookings</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Track active trips and revisit past ones.</p>

        <template x-if="segment">
            <div class="mt-5 flex items-center gap-2">
                <span class="chip chip-active" x-text="segment.name"></span>
                <div class="ml-auto grid grid-cols-2 gap-1 rounded-2xl bg-slate-100 p-1 dark:bg-slate-800">
                    <button type="button" class="chip !min-h-0 !py-1.5" :class="tab === 'ACTIVE' && 'chip-active'" @click="switchTab('ACTIVE')">Active</button>
                    <button type="button" class="chip !min-h-0 !py-1.5" :class="tab === 'PAST' && 'chip-active'" @click="switchTab('PAST')">Past</button>
                </div>
            </div>
        </template>

        <p x-show="error" class="mt-4 text-sm font-medium text-danger" x-text="error"></p>

        <div x-show="loading" class="mt-6 space-y-3">
            <template x-for="i in 3" :key="i">
                <div class="skeleton h-24 rounded-3xl"></div>
            </template>
        </div>

        <div x-show="!loading && items.length" class="mt-6 space-y-3">
            <template x-for="b in items" :key="b.id">
                <a :href="'/' + (b.is_in_drive ? 'app/booking/active/' : 'app/bookings/') + b.id"
                   class="card flex items-center gap-3.5 p-4 transition duration-200 hover:-translate-y-0.5 hover:shadow-lifted active:scale-[0.99]">
                    <span class="brand-soft flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl">
                        <svg class="h-5 w-5 brand-text" aria-hidden="true"><use href="#i-navigation"/></svg>
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-bold text-slate-900 dark:text-white" x-text="b.title"></p>
                        <p class="mt-0.5 truncate text-xs text-slate-500" x-text="b.subtitle"></p>
                        <p class="mt-1 inline-flex items-center gap-1 rounded-full bg-brand-50 px-2 py-0.5 text-[11px] font-bold brand-text dark:bg-brand-950/60" x-text="b.status"></p>
                    </div>
                    <div class="flex shrink-0 flex-col items-end gap-1">
                        <p class="text-sm font-extrabold" x-text="b.estimate ? b.money : ''"></p>
                        <svg class="h-4 w-4 text-slate-300 dark:text-slate-600" aria-hidden="true"><use href="#i-chevron-right"/></svg>
                    </div>
                </a>
            </template>
        </div>

        {{-- No service selected yet --}}
        <div x-show="!loading && needsService && !items.length" class="card mt-6 p-6 text-center">
            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 dark:bg-slate-800">
                <svg class="h-6 w-6 text-slate-400" aria-hidden="true"><use href="#i-navigation"/></svg>
            </span>
            <p class="mt-3 text-base font-bold text-slate-900 dark:text-white">Pick a service to see your bookings</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Bookings are grouped by service type. Choose one from the home screen and we'll show your trips here.</p>
            <a href="/app" class="btn-primary mt-4 inline-block text-sm">Browse services</a>
        </div>

        {{-- Loaded, nothing here --}}
        <div x-show="!loading && !needsService && !items.length && !error" class="card mt-6 p-6 text-center">
            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 dark:bg-slate-800">
                <svg class="h-6 w-6 text-slate-400" aria-hidden="true"><use href="#i-bookings"/></svg>
            </span>
            <p class="mt-3 text-base font-bold text-slate-900 dark:text-white" x-text="tab === 'ACTIVE' ? 'No active trips' : 'No past bookings'"></p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400" x-text="tab === 'ACTIVE' ? 'When you book a ride or delivery, it will show up here.' : 'Completed trips will appear here once you have taken one.'"></p>
            <a href="/app" class="btn-ghost mt-4 inline-block text-sm">Book a service</a>
        </div>
    </div>
@endcomponent
