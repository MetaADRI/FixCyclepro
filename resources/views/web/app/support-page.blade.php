@component('web.layouts.app', ['title' => 'Help', 'logo' => '', 'slug' => $slug ?? null])
    <div x-data="supportScreen" class="mx-auto max-w-2xl px-5 pb-28 pt-6 lg:pb-8">
        <a href="/app/support" class="mb-4 inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 hover:text-slate-700 dark:text-slate-400">
            <svg class="h-4 w-4" aria-hidden="true"><use href="#i-back"/></svg>
            Support
        </a>

        <div x-show="loading" class="skeleton h-48 rounded-3xl"></div>
        <p x-show="!loading && !page" class="mt-4 text-sm text-slate-500">This page could not be loaded.</p>

        <template x-if="!loading && page">
            <article class="card space-y-4">
                <h1 class="text-xl font-extrabold tracking-tight text-slate-900 dark:text-white"
                    x-text="page.name || page.title || 'Help'"></h1>
                <div class="prose-sm space-y-3 text-sm leading-relaxed text-slate-600 dark:text-slate-300"
                     x-html="page.description || page.content || page.body || ''"></div>
            </article>
        </template>
    </div>
@endcomponent