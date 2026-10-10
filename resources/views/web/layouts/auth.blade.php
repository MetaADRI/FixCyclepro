<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>{{ $title ?? 'Welcome' }} · {{ config('app.name', 'Fixcycle') }}</title>
    <meta name="theme-color" content="#E33A45">
    <meta name="description" content="Sign in to {{ config('app.name', 'Fixcycle') }} to book taxis, deliveries, handyman services and more.">
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
<body class="brand-glow min-h-screen bg-surface-muted">
    <x-web.toast />
    @include('web.partials.icons')

    <div class="flex min-h-screen flex-col items-center justify-center px-4 py-10">
        <a href="/" class="mb-8 flex flex-col items-center gap-3" aria-label="{{ config('app.name', 'Fixcycle') }} home">
            <span class="brand-bg brand-contrast flex h-14 w-14 items-center justify-center rounded-2xl text-2xl font-extrabold shadow-card">
                {{ substr(config('app.name', 'Fixcycle'), 0, 2) }}
            </span>
            <span class="text-xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                {{ config('app.name', 'Fixcycle') }}
            </span>
        </a>

        <div class="card w-full max-w-md px-6 py-8 sm:px-8">
            {{ $slot }}
        </div>

        <p class="mt-8 text-center text-xs text-slate-400 dark:text-slate-500">
            © {{ date('Y') }} {{ config('app.name', 'Fixcycle') }}. All rights reserved.
        </p>
    </div>

    {{ $scripts ?? '' }}
</body>
</html>