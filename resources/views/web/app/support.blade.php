@component('web.layouts.app', ['title' => 'Support', 'logo' => '', 'slug' => null])
    <div x-data="supportScreen" class="mx-auto max-w-2xl px-5 pb-28 pt-6 lg:pb-8">
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">Support</h1>

        <div x-show="loading" class="mt-5 space-y-3">
            <template x-for="i in 3" :key="i">
                <div class="skeleton h-16 rounded-3xl"></div>
            </template>
        </div>

        <template x-if="!loading">
            <div class="mt-5 space-y-6">
                <template x-if="pages.length">
                    <div class="card p-3">
                        <template x-for="p in pages" :key="p.slug">
                            <a :href="'/app/support/' + encodeURIComponent(p.slug)"
                               class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-semibold hover:bg-slate-50 dark:hover:bg-slate-800">
                                <span x-text="p.name"></span>
                                <svg class="ml-auto h-4 w-4 text-slate-300 dark:text-slate-600" aria-hidden="true"><use href="#i-chevron-right"/></svg>
                            </a>
                        </template>
                    </div>
                </template>

                <template x-if="faqs.length">
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">Frequently asked questions</h2>
                        <div class="mt-3 space-y-2">
                            <template x-for="f in faqs" :key="f.id || f">
                                <details class="card !p-4">
                                    <summary class="cursor-pointer text-sm font-semibold"
                                             x-text="typeof f === 'string' ? f : (f.title || f.name || f.question || f)"></summary>
                                    <p class="mt-2 text-sm text-slate-500"
                                       x-text="typeof f === 'string' ? '' : (f.description || f.answer || f.details || '')"></p>
                                </details>
                            </template>
                        </div>
                    </div>
                </template>

                <div class="card">
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Contact us</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Can't find what you need? Reach our team any time.
                    </p>
                    <div class="mt-4 flex flex-col gap-2 sm:flex-row">
                        <a href="/app/support/about_us" class="btn-ghost flex-1 text-center text-sm">Browse policies</a>
                        <a href="/app/profile" class="btn-primary flex-1 text-center text-sm">Manage account</a>
                    </div>
                </div>
            </div>
        </template>
    </div>
@endcomponent