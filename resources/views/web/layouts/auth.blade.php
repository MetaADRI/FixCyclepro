<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>{{ $title ?? 'Welcome' }} · {{ config('app.name', 'Fixcycle') }}</title>
    <meta name="theme-color" content="#287D1B">
    <meta name="description" content="Sign in to {{ config('app.name', 'Fixcycle') }} to book taxis, deliveries, handyman services and more.">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/fixcycle-icon-192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('images/fixcycle-icon-512.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/fixcycle-apple-touch.png') }}">
    @include('web.partials.theme-init')
    @include('web.partials.config')
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
        <div class="mb-4">
            <button type="button" class="btn-ghost gap-2" @click="Alpine.store('theme').toggle()" x-data :aria-pressed="Alpine.store('theme').mode === 'dark'" aria-label="Toggle dark mode">
                <svg class="h-4 w-4 dark:hidden" aria-hidden="true"><use href="#i-moon"/></svg>
                <svg class="hidden h-4 w-4 dark:block" aria-hidden="true"><use href="#i-sun"/></svg>
                <span x-text="Alpine.store('theme').mode === 'dark' ? 'Light mode' : 'Dark mode'"></span>
            </button>
        </div>
        <a href="/" class="mb-8 flex flex-col items-center gap-3" aria-label="{{ config('app.name', 'Fixcycle') }} home">
            <img data-logo="light" src="{{ asset('images/fixcycle-logo.png') }}" alt="{{ config('app.name', 'Fixcycle') }}" class="h-24 w-24 object-contain dark:hidden">
            <img data-logo="dark" src="{{ asset('images/fixcycle-logo-dark.png') }}" alt="" aria-hidden="true" class="hidden h-24 w-24 object-contain dark:block">
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