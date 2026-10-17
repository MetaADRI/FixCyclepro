<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>{{ config('app.name', 'Fixcycle') }} — Mobility & on-demand services</title>
    <meta name="theme-color" content="#287D1B">
    <meta name="description" content="Book taxis, deliveries, couriers, handyman services and more with {{ config('app.name', 'Fixcycle') }}.">
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/fixcycle-icon-192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('images/fixcycle-icon-512.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/fixcycle-apple-touch.png') }}">
    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <link rel="stylesheet" href="{{ asset('build/app.css') }}">
    @endif
</head>
<body class="min-h-screen bg-white text-slate-900 dark:bg-slate-950 dark:text-slate-100">
    @include('web.partials.icons')

    <header class="mx-auto flex max-w-6xl items-center justify-between px-5 py-6">
        <a href="/" class="flex items-center gap-2.5" aria-label="{{ config('app.name', 'Fixcycle') }}">
            <img src="{{ asset('images/fixcycle-logo.png') }}" alt="{{ config('app.name', 'Fixcycle') }}" class="h-16 w-16 object-contain">
        </a>
        <nav class="flex items-center gap-3 text-sm font-semibold">
            <a href="/login" class="btn-ghost px-4 py-2">Sign in</a>
            <a href="/signup" class="btn-primary px-4 py-2">Get started</a>
        </nav>
    </header>

    <main>
        <section class="mx-auto max-w-6xl px-5 pb-16 pt-10 text-center sm:pt-16">
            <span class="chip chip-active mb-5">Mobility · Delivery · Services</span>
            <h1 class="mx-auto max-w-3xl text-4xl font-extrabold leading-tight tracking-tight sm:text-6xl">
                Everything you need, <span class="brand-text">on demand</span>
            </h1>
            <p class="mx-auto mt-5 max-w-xl text-lg text-slate-500 dark:text-slate-400">
                Book a taxi, send a parcel, or get a handyman. Real-time tracking, transparent fares, and support that is always one tap away.
            </p>
            <div class="mx-auto mt-8 flex max-w-sm flex-col gap-3 sm:flex-row sm:justify-center">
                <a href="/signup" class="btn-primary px-6 py-3 text-center">Create your account</a>
                <a href="/login" class="btn-ghost px-6 py-3 text-center">I already have an account</a>
            </div>
        </section>

        <section class="mx-auto grid max-w-6xl gap-5 px-5 pb-20 sm:grid-cols-3">
            <div class="feature-card">
                <span class="feature-card-back" aria-hidden="true"></span>
                <article class="feature-card-front">
                    <span class="feature-icon">
                        <svg class="h-6 w-6 brand-text" aria-hidden="true"><use href="#i-navigation"/></svg>
                    </span>
                    <h2 class="text-lg font-bold">Go anywhere</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">City rides, rentals, transfers and outstation trips at transparent fares — tracked live from pickup to drop.</p>
                </article>
            </div>
            <div class="feature-card">
                <span class="feature-card-back" aria-hidden="true"></span>
                <article class="feature-card-front">
                    <span class="feature-icon">
                        <svg class="h-6 w-6 brand-text" aria-hidden="true"><use href="#i-gift"/></svg>
                    </span>
                    <h2 class="text-lg font-bold">Send anything</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Same-day couriers for documents, groceries and parcels — with multiple stops and proof of delivery.</p>
                </article>
            </div>
            <div class="feature-card">
                <span class="feature-card-back" aria-hidden="true"></span>
                <article class="feature-card-front">
                    <span class="feature-icon">
                        <svg class="h-6 w-6 brand-text" aria-hidden="true"><use href="#i-users"/></svg>
                    </span>
                    <h2 class="text-lg font-bold">Fix and hire</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Skilled handymen and service professionals, ready when you are. Book by the hour or by the job.</p>
                </article>
            </div>
        </section>
    </main>

    <footer class="border-t border-slate-200 py-8 text-center text-sm text-slate-400 dark:border-slate-800">
        © {{ date('Y') }} {{ config('app.name', 'Fixcycle') }}. All rights reserved.
    </footer>
</body>
</html>