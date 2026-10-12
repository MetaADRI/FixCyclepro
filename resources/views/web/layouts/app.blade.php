<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>{{ $title ?? 'Fixcycle' }} · {{ config('app.name', 'Fixcycle') }}</title>
    <meta name="theme-color" content="#E33A45">
    <meta name="description" content="Book taxis, deliveries, handyman services and more with {{ config('app.name', 'Fixcycle') }}.">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="icon" type="image/png" sizes="192x192" href="/icons/icon-192.png">
    <link rel="icon" type="image/png" sizes="512x512" href="/icons/icon-512.png">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <link rel="stylesheet" href="{{ asset('build/app.css') }}">
    @endif
    {{ $head ?? '' }}
</head>
<body class="min-h-screen bg-surface-muted">
    <x-web.toast />
    @include('web.partials.icons')

    <div x-data="appShell" class="flex min-h-screen">
        {{-- Desktop sidebar --}}
        <aside class="fixed inset-y-0 left-0 z-30 hidden w-64 flex-col border-r border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-900 lg:flex">
            <a href="/" class="flex items-center gap-3 px-6 py-5" aria-label="{{ config('app.name', 'Fixcycle') }} home">
                <img id="web-logo" src="{{ $logo ?? '' }}" alt="" class="h-9 w-9 rounded-xl object-contain"
                     onerror="this.style.display='none';document.getElementById('web-logo-fallback').style.display='flex';">
                <span id="web-logo-fallback" class="brand-bg brand-contrast hidden items-center justify-center rounded-xl px-2.5 py-1 text-lg font-extrabold" style="display:none">
                    {{ substr(config('app.name', 'Fixcycle'), 0, 2) }}
                </span>
                <span class="text-lg font-extrabold tracking-tight text-slate-900 dark:text-white">
                    {{ config('app.name', 'Fixcycle') }}
                </span>
            </a>
            <nav class="flex-1 space-y-1 px-3 py-2" aria-label="Main">
                <template x-for="item in nav" :key="item.path">
                    <a :href="item.path" class="nav-item" :class="{ 'nav-item-active': isActive(item.path) }">
                        <svg class="h-5 w-5 shrink-0" aria-hidden="true"><use :href="'#i-' + item.icon"/></svg>
                        <span x-text="item.label"></span>
                    </a>
                </template>
            </nav>
            <div class="border-t border-slate-200 px-6 py-4 dark:border-slate-700">
                <div class="flex items-center gap-3" x-data="auth">
                    <template x-if="isLoggedIn">
                        <div class="flex items-center gap-3">
                            <div class="brand-bg brand-contrast flex h-9 w-9 items-center justify-center rounded-full text-sm font-bold uppercase"
                                 x-text="(user?.first_name?.[0] || 'U')"></div>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-slate-900 dark:text-white"
                                   x-text="user?.first_name || 'Account'"></p>
                                <a href="/app/profile" class="text-xs text-slate-500 dark:text-slate-400">Profile</a>
                            </div>
                        </div>
                    </template>
                    <template x-if="!isLoggedIn">
                        <a href="/login" class="btn-primary w-full text-center text-sm">Sign in</a>
                    </template>
                </div>
            </div>
        </aside>

        {{-- Main column --}}
        <div class="flex min-h-screen w-full flex-col lg:pl-64">
            <main class="flex-1">
                {{ $slot }}
            </main>

            {{-- Mobile bottom nav --}}
            <nav class="pb-safe fixed inset-x-0 bottom-0 z-30 border-t border-slate-200 bg-white/95 backdrop-blur dark:border-slate-700 dark:bg-slate-900/95 lg:hidden"
                 x-data="appShell" aria-label="Mobile">
                <div class="grid grid-cols-5">
                    <template x-for="item in nav" :key="item.path">
                        <a :href="item.path"
                           class="flex min-h-touch flex-col items-center justify-center gap-1 py-2 text-[11px] font-medium"
                           :class="isActive(item.path) ? 'brand-text' : 'text-slate-500 dark:text-slate-400'">
                            <svg class="h-6 w-6" aria-hidden="true"><use :href="'#i-' + item.icon"/></svg>
                            <span x-text="item.label"></span>
                        </a>
                    </template>
                </div>
            </nav>
        </div>
    </div>

    <script>
        window.supportSlug = @json($slug ?? null);
    </script>
    {{ $scripts ?? '' }}
</body>
</html>