<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>You're offline · {{ config('app.name', 'Fixcycle') }}</title>
    <meta name="theme-color" content="#E33A45">
    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <link rel="stylesheet" href="{{ asset('build/app.css') }}">
    @endif
</head>
<body class="min-h-screen bg-surface-muted">
    @include('web.partials.icons')
    <div class="flex min-h-screen flex-col items-center justify-center px-6 text-center">
        <span class="mb-6 flex h-16 w-16 items-center justify-center rounded-3xl brand-soft">
            <svg class="h-8 w-8 brand-text" aria-hidden="true"><use href="#i-map"/></svg>
        </span>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">You're offline</h1>
        <p class="mt-2 max-w-sm text-sm text-slate-500 dark:text-slate-400">
            Check your connection and try again — your bookings will be waiting for you when you're back.
        </p>
        <button type="button" class="btn-primary mt-8 px-6" onclick="location.reload()">Retry</button>
    </div>
</body>
</html>