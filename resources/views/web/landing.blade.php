<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>{{ config('app.name', 'Fixcycle') }} — Mobility, delivery & on-demand services</title>
    <meta name="theme-color" content="#287D1B">
    <meta name="description" content="Mobility, delivery services, everything you need on demand. Book taxis, send parcels and hire trusted handymen with {{ config('app.name', 'Fixcycle') }}.">
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/fixcycle-icon-192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('images/fixcycle-icon-512.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/fixcycle-apple-touch.png') }}">
    @include('web.partials.theme-init')
    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <link rel="stylesheet" href="{{ asset('build/app.css') }}">
    @endif
</head>
<body class="landing-shell min-h-screen bg-white text-slate-900 dark:bg-slate-950 dark:text-slate-100">
    @include('web.partials.icons')

    <header class="sticky top-0 z-40 border-b border-transparent bg-white/80 backdrop-blur dark:bg-slate-950/80">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-5 py-4">
            <a href="/" class="flex items-center gap-2.5" aria-label="{{ config('app.name', 'Fixcycle') }}">
                <img data-logo="light" src="{{ asset('images/fixcycle-logo.png') }}" alt="{{ config('app.name', 'Fixcycle') }}" class="h-11 w-11 object-contain dark:hidden sm:h-14 sm:w-14">
                <img data-logo="dark" src="{{ asset('images/fixcycle-logo-dark.png') }}" alt="" aria-hidden="true" class="hidden h-11 w-11 object-contain dark:block sm:h-14 sm:w-14">
            </a>
            <nav class="flex items-center gap-1.5 text-sm font-semibold sm:gap-3">
                <button type="button" class="btn-ghost h-10 w-10 !min-h-0 !min-w-0 rounded-xl !p-0" x-data @click="Alpine.store('theme').toggle()" :aria-pressed="Alpine.store('theme').mode === 'dark'" aria-label="Toggle dark mode">
                    <svg class="h-5 w-5 dark:hidden" aria-hidden="true"><use href="#i-moon"/></svg>
                    <svg class="hidden h-5 w-5 dark:block" aria-hidden="true"><use href="#i-sun"/></svg>
                </button>
                <a href="/login" class="btn-ghost px-3 py-2 sm:px-4">Sign in</a>
                <a href="/signup" class="btn-primary px-3 py-2 sm:px-4">Get started</a>
            </nav>
        </div>
    </header>

    <main>
        {{-- Hero --}}
        <section class="mx-auto max-w-6xl px-5 pb-14 pt-10 text-center sm:pb-20 sm:pt-16">
            <span class="chip chip-active mb-5">Mobility · Delivery · Services</span>
            <h1 class="mx-auto max-w-4xl text-balance text-4xl font-extrabold leading-tight tracking-tight sm:text-6xl">
                Mobility, delivery services —
                <span class="brand-text">everything you need on demand</span>
            </h1>
            <p class="mx-auto mt-5 max-w-xl text-balance text-lg text-slate-500 dark:text-slate-400">
                Book a taxi, send a parcel, or get a trusted handyman in minutes. Live tracking, upfront fares, and support that is always one tap away.
            </p>
            <div class="mx-auto mt-8 flex max-w-sm flex-col gap-3 sm:flex-row sm:justify-center">
                <a href="/signup" class="btn-primary px-6 py-3 text-center">Create your account</a>
                <a href="/login" class="btn-ghost px-6 py-3 text-center">I already have an account</a>
            </div>
            <ul class="mx-auto mt-10 flex max-w-2xl flex-wrap items-center justify-center gap-x-6 gap-y-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
                <li class="flex items-center gap-1.5"><svg class="h-4 w-4 brand-text" aria-hidden="true"><use href="#i-check"/></svg> Live trip tracking</li>
                <li class="flex items-center gap-1.5"><svg class="h-4 w-4 brand-text" aria-hidden="true"><use href="#i-check"/></svg> Upfront pricing</li>
                <li class="flex items-center gap-1.5"><svg class="h-4 w-4 brand-text" aria-hidden="true"><use href="#i-check"/></svg> Verified professionals</li>
            </ul>
        </section>

        {{-- How it works --}}
        <section class="mx-auto max-w-6xl px-5 pb-20">
            <div class="reveal text-center">
                <p class="section-eyebrow">How it works</p>
                <h2 class="mt-2 text-3xl font-extrabold tracking-tight sm:text-4xl">From tap to done in minutes</h2>
                <p class="mx-auto mt-3 max-w-lg text-slate-500 dark:text-slate-400">Three simple steps, whether you need a ride, a courier or a helping hand.</p>
            </div>
            <div class="mt-10 grid gap-5 sm:grid-cols-3">
                <div class="reveal card p-6" style="--reveal-delay: 0ms">
                    <div class="flex items-center justify-between">
                        <span class="brand-soft flex h-12 w-12 items-center justify-center rounded-2xl">
                            <svg class="h-6 w-6 brand-text" aria-hidden="true"><use href="#i-search"/></svg>
                        </span>
                        <span class="text-4xl font-extrabold text-slate-200 dark:text-slate-800">01</span>
                    </div>
                    <h3 class="mt-5 text-lg font-bold">Pick a service</h3>
                    <p class="mt-1.5 text-sm text-slate-500 dark:text-slate-400">Ride, courier or handyman — all from one account, all in one app.</p>
                </div>
                <div class="reveal card p-6" style="--reveal-delay: 90ms">
                    <div class="flex items-center justify-between">
                        <span class="brand-soft flex h-12 w-12 items-center justify-center rounded-2xl">
                            <svg class="h-6 w-6 brand-text" aria-hidden="true"><use href="#i-pin"/></svg>
                        </span>
                        <span class="text-4xl font-extrabold text-slate-200 dark:text-slate-800">02</span>
                    </div>
                    <h3 class="mt-5 text-lg font-bold">Set your trip or job</h3>
                    <p class="mt-1.5 text-sm text-slate-500 dark:text-slate-400">Drop a pin for pickup and destination, or describe the job you need done.</p>
                </div>
                <div class="reveal card p-6" style="--reveal-delay: 180ms">
                    <div class="flex items-center justify-between">
                        <span class="brand-soft flex h-12 w-12 items-center justify-center rounded-2xl">
                            <svg class="h-6 w-6 brand-text" aria-hidden="true"><use href="#i-check"/></svg>
                        </span>
                        <span class="text-4xl font-extrabold text-slate-200 dark:text-slate-800">03</span>
                    </div>
                    <h3 class="mt-5 text-lg font-bold">Track, pay, done</h3>
                    <p class="mt-1.5 text-sm text-slate-500 dark:text-slate-400">Follow progress live and pay by cash or wallet when it is complete.</p>
                </div>
            </div>
        </section>

        {{-- Features --}}
        <section class="mx-auto max-w-6xl px-5 pb-20">
            <div class="reveal text-center">
                <p class="section-eyebrow">Why it feels different</p>
                <h2 class="mt-2 text-3xl font-extrabold tracking-tight sm:text-4xl">Built around every trip</h2>
                <p class="mx-auto mt-3 max-w-lg text-slate-500 dark:text-slate-400">Small details that make moving people and things around the city effortless.</p>
            </div>
            <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div class="reveal feature-card" style="--reveal-delay: 0ms">
                    <span class="feature-card-back" aria-hidden="true"></span>
                    <article class="feature-card-front">
                        <span class="feature-icon">
                            <svg class="h-6 w-6 brand-text" aria-hidden="true"><use href="#i-navigation"/></svg>
                        </span>
                        <h2 class="text-lg font-bold">Go anywhere</h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">City rides, rentals, transfers and outstation trips at transparent fares.</p>
                    </article>
                </div>
                <div class="reveal feature-card" style="--reveal-delay: 70ms">
                    <span class="feature-card-back" aria-hidden="true"></span>
                    <article class="feature-card-front">
                        <span class="feature-icon">
                            <svg class="h-6 w-6 brand-text" aria-hidden="true"><use href="#i-gift"/></svg>
                        </span>
                        <h2 class="text-lg font-bold">Send anything</h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Same-day couriers for documents, groceries and parcels — with multiple stops.</p>
                    </article>
                </div>
                <div class="reveal feature-card" style="--reveal-delay: 140ms">
                    <span class="feature-card-back" aria-hidden="true"></span>
                    <article class="feature-card-front">
                        <span class="feature-icon">
                            <svg class="h-6 w-6 brand-text" aria-hidden="true"><use href="#i-users"/></svg>
                        </span>
                        <h2 class="text-lg font-bold">Fix and hire</h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Skilled handymen and professionals, ready when you are — by the hour or job.</p>
                    </article>
                </div>
                <div class="reveal feature-card" style="--reveal-delay: 210ms">
                    <span class="feature-card-back" aria-hidden="true"></span>
                    <article class="feature-card-front">
                        <span class="feature-icon">
                            <svg class="h-6 w-6 brand-text" aria-hidden="true"><use href="#i-map"/></svg>
                        </span>
                        <h2 class="text-lg font-bold">Live tracking</h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Watch your driver or courier move in real time from pickup to drop-off.</p>
                    </article>
                </div>
                <div class="reveal feature-card" style="--reveal-delay: 0ms">
                    <span class="feature-card-back" aria-hidden="true"></span>
                    <article class="feature-card-front">
                        <span class="feature-icon">
                            <svg class="h-6 w-6 brand-text" aria-hidden="true"><use href="#i-cash"/></svg>
                        </span>
                        <h2 class="text-lg font-bold">Upfront fares</h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">See the price before you confirm. No surprises when the trip ends.</p>
                    </article>
                </div>
                <div class="reveal feature-card" style="--reveal-delay: 70ms">
                    <span class="feature-card-back" aria-hidden="true"></span>
                    <article class="feature-card-front">
                        <span class="feature-icon">
                            <svg class="h-6 w-6 brand-text" aria-hidden="true"><use href="#i-shield"/></svg>
                        </span>
                        <h2 class="text-lg font-bold">Secure wallet</h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Keep credit ready for faster checkout and track every transaction in one place.</p>
                    </article>
                </div>
                <div class="reveal feature-card sm:col-span-2 lg:col-span-2" style="--reveal-delay: 140ms">
                    <span class="feature-card-back" aria-hidden="true"></span>
                    <article class="feature-card-front sm:flex sm:items-start sm:gap-5">
                        <span class="feature-icon sm:mb-0">
                            <svg class="h-6 w-6 brand-text" aria-hidden="true"><use href="#i-chat"/></svg>
                        </span>
                        <div>
                            <h2 class="text-lg font-bold">Support, 24/7</h2>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Questions mid-trip? Reach a real person from the app, day or night — no phone queues.</p>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        {{-- Services --}}
        <section class="mx-auto max-w-6xl px-5 pb-20">
            <div class="reveal text-center">
                <p class="section-eyebrow">One account</p>
                <h2 class="mt-2 text-3xl font-extrabold tracking-tight sm:text-4xl">Every service, one place</h2>
            </div>
            <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="reveal card flex items-center gap-4 p-5 transition hover:-translate-y-1 hover:shadow-lifted" style="--reveal-delay: 0ms">
                    <span class="brand-bg flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl">
                        <svg class="h-6 w-6 text-white" aria-hidden="true"><use href="#i-navigation"/></svg>
                    </span>
                    <div>
                        <p class="font-bold">Ride</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Point-to-point and hourly</p>
                    </div>
                </div>
                <div class="reveal card flex items-center gap-4 p-5 transition hover:-translate-y-1 hover:shadow-lifted" style="--reveal-delay: 70ms">
                    <span class="brand-bg flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl">
                        <svg class="h-6 w-6 text-white" aria-hidden="true"><use href="#i-gift"/></svg>
                    </span>
                    <div>
                        <p class="font-bold">Courier</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Parcels, food and documents</p>
                    </div>
                </div>
                <div class="reveal card flex items-center gap-4 p-5 transition hover:-translate-y-1 hover:shadow-lifted" style="--reveal-delay: 140ms">
                    <span class="brand-bg flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl">
                        <svg class="h-6 w-6 text-white" aria-hidden="true"><use href="#i-users"/></svg>
                    </span>
                    <div>
                        <p class="font-bold">Handyman</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Repairs, installs and more</p>
                    </div>
                </div>
                <div class="reveal card flex items-center gap-4 p-5 transition hover:-translate-y-1 hover:shadow-lifted" style="--reveal-delay: 210ms">
                    <span class="brand-bg flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl">
                        <svg class="h-6 w-6 text-white" aria-hidden="true"><use href="#i-wallet"/></svg>
                    </span>
                    <div>
                        <p class="font-bold">Wallet</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Faster, cashless checkout</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Why Fixcycle --}}
        <section class="mx-auto max-w-6xl px-5 pb-20">
            <div class="reveal grid items-center gap-10 lg:grid-cols-2">
                <div>
                    <p class="section-eyebrow">Why Fixcycle</p>
                    <h2 class="mt-2 text-3xl font-extrabold tracking-tight sm:text-4xl">Reliability you can see</h2>
                    <p class="mt-3 max-w-lg text-slate-500 dark:text-slate-400">
                        We built Fixcycle for how the city actually moves — busy roads, quick errands, and jobs that cannot wait.
                    </p>
                    <ul class="mt-6 space-y-4">
                        <li class="flex items-start gap-3">
                            <span class="brand-soft mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-xl">
                                <svg class="h-4 w-4 brand-text" aria-hidden="true"><use href="#i-shield"/></svg>
                            </span>
                            <div>
                                <p class="text-sm font-bold">Verified professionals</p>
                                <p class="text-sm text-slate-500 dark:text-slate-400">Every driver and provider is screened before they take a job.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="brand-soft mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-xl">
                                <svg class="h-4 w-4 brand-text" aria-hidden="true"><use href="#i-cash"/></svg>
                            </span>
                            <div>
                                <p class="text-sm font-bold">Transparent pricing</p>
                                <p class="text-sm text-slate-500 dark:text-slate-400">Estimates before you book, receipts after every trip.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="brand-soft mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-xl">
                                <svg class="h-4 w-4 brand-text" aria-hidden="true"><use href="#i-chat"/></svg>
                            </span>
                            <div>
                                <p class="text-sm font-bold">Support that answers</p>
                                <p class="text-sm text-slate-500 dark:text-slate-400">In-app chat with real people, whenever you need help.</p>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="brand-bg relative overflow-hidden rounded-4xl p-8 text-white shadow-card sm:p-10">
                    <div class="pointer-events-none absolute -right-16 -top-16 h-52 w-52 rounded-full bg-white/10"></div>
                    <div class="pointer-events-none absolute -bottom-24 -left-10 h-56 w-56 rounded-full bg-black/10"></div>
                    <p class="relative text-sm font-semibold text-white/80">Fixcycle in numbers</p>
                    <div class="relative mt-6 grid grid-cols-2 gap-3">
                        <div class="stat-tile">
                            <p class="text-3xl font-extrabold">3</p>
                            <p class="mt-1 text-xs font-semibold text-white/80">Service types</p>
                        </div>
                        <div class="stat-tile">
                            <p class="text-3xl font-extrabold">Live</p>
                            <p class="mt-1 text-xs font-semibold text-white/80">GPS tracking</p>
                        </div>
                        <div class="stat-tile">
                            <p class="text-3xl font-extrabold">24/7</p>
                            <p class="mt-1 text-xs font-semibold text-white/80">Support</p>
                        </div>
                        <div class="stat-tile">
                            <p class="text-3xl font-extrabold">1</p>
                            <p class="mt-1 text-xs font-semibold text-white/80">Account for all</p>
                        </div>
                    </div>
                    <p class="relative mt-6 text-sm text-white/80">Built for Zambia — one app for getting people and things moving.</p>
                </div>
            </div>
        </section>

        {{-- Final CTA --}}
        <section class="mx-auto max-w-6xl px-5 pb-20">
            <div class="reveal brand-bg relative overflow-hidden rounded-4xl px-6 py-12 text-center text-white shadow-card sm:px-12 sm:py-16">
                <div class="pointer-events-none absolute -left-20 top-0 h-64 w-64 rounded-full bg-white/10"></div>
                <div class="pointer-events-none absolute -right-16 bottom-0 h-56 w-56 rounded-full bg-black/10"></div>
                <h2 class="relative text-balance text-3xl font-extrabold tracking-tight sm:text-4xl">Ready when you are</h2>
                <p class="relative mx-auto mt-3 max-w-md text-balance text-white/80">
                    Create your account in under a minute and book your first ride, delivery or job today.
                </p>
                <div class="relative mx-auto mt-8 flex max-w-sm flex-col gap-3 sm:flex-row sm:justify-center">
                    <a href="/signup" class="btn bg-white px-6 py-3 text-center font-bold text-brand-700 hover:bg-brand-50">Create your account</a>
                    <a href="/login" class="btn border border-white/40 px-6 py-3 text-center font-bold text-white hover:bg-white/10">I already have an account</a>
                </div>
            </div>
        </section>
    </main>

    <footer class="border-t border-slate-200 py-10 dark:border-slate-800">
        <div class="mx-auto grid max-w-6xl gap-8 px-5 sm:grid-cols-3">
            <div>
                <p class="text-lg font-extrabold tracking-tight">{{ config('app.name', 'Fixcycle') }}</p>
                <p class="mt-2 max-w-xs text-sm text-slate-500 dark:text-slate-400">Mobility, delivery services — everything you need on demand.</p>
            </div>
            <div>
                <p class="text-sm font-bold text-slate-900 dark:text-white">Get started</p>
                <ul class="mt-3 space-y-2 text-sm text-slate-500 dark:text-slate-400">
                    <li><a href="/signup" class="transition-colors hover:text-brand-600 dark:hover:text-brand-400">Create an account</a></li>
                    <li><a href="/login" class="transition-colors hover:text-brand-600 dark:hover:text-brand-400">Sign in</a></li>
                </ul>
            </div>
            <div>
                <p class="text-sm font-bold text-slate-900 dark:text-white">Support</p>
                <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">In-app chat, 24/7 — sign in and tap Support.</p>
            </div>
        </div>
        <p class="mx-auto mt-10 max-w-6xl px-5 text-sm text-slate-400">© {{ date('Y') }} {{ config('app.name', 'Fixcycle') }}. All rights reserved.</p>
    </footer>
</body>
</html>
