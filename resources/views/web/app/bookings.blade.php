@component('web.layouts.app', ['title' => 'My bookings', 'logo' => ''])
    <div x-data="bookingsScreen" class="mx-auto max-w-2xl px-5 pb-28 pt-6 lg:pb-8">
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">My bookings</h1>

        <template x-if="segment">
            <div class="mt-4 flex items-center gap-2">
                <span class="chip chip-active" x-text="segment.name"></span>
                <div class="ml-auto grid grid-cols-2 gap-1 rounded-2xl bg-slate-100 p-1 dark:bg-slate-800">
                    <button type="button" class="chip !py-1.5" :class="tab === 'ACTIVE' && 'chip-active'" @click="switchTab('ACTIVE')">Active</button>
                    <button type="button" class="chip !py-1.5" :class="tab === 'PAST' && 'chip-active'" @click="switchTab('PAST')">Past</button>
                </div>
            </div>
        </template>

        <p x-show="error" class="mt-4 text-sm font-medium text-danger" x-text="error"></p>

        <div x-show="loading" class="mt-6 space-y-3">
            <template x-for="i in 3" :key="i">
                <div class="skeleton h-20 rounded-3xl"></div>
            </template>
        </div>

        <div x-show="!loading && items.length" class="mt-6 space-y-3">
            <template x-for="b in items" :key="b.id">
                <a :href="'/' + (b.is_in_drive ? 'app/booking/active/' : 'app/bookings/') + b.id"
                   class="card flex items-center gap-3 transition active:scale-[0.99]">
                    <span class="brand-soft flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl">
                        <svg class="h-5 w-5 brand-text" aria-hidden="true"><use href="#i-navigation"/></svg>
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-bold text-slate-900 dark:text-white" x-text="b.title"></p>
                        <p class="mt-0.5 truncate text-xs text-slate-500" x-text="b.subtitle"></p>
                        <p class="mt-0.5 truncate text-xs font-semibold brand-text" x-text="b.status"></p>
                    </div>
                    <div class="shrink-0 text-right">
                        <p class="text-sm font-extrabold" x-text="b.estimate ? (b.currency + ' ' + b.estimate) : ''"></p>
                        <svg class="ml-auto mt-1 h-4 w-4 text-slate-300 dark:text-slate-600" aria-hidden="true"><use href="#i-chevron-right"/></svg>
                    </div>
                </a>
            </template>
        </div>

        <div x-show="!loading && !items.length && !error" class="card mt-6 text-center">
            <svg class="mx-auto h-8 w-8 text-slate-300 dark:text-slate-600" aria-hidden="true"><use href="#i-bookings"/></svg>
            <p class="mt-2 text-sm text-slate-500">No bookings here yet.</p>
            <a href="/app" class="btn-ghost mt-3 inline-block text-sm">Book your first service</a>
        </div>
    </div>
@endcomponent