# MEGA PROMPT — Convert the Fixcycle Android System into a Responsive PWA Web Version

> **For Open Code (or any coding agent): execute this end-to-end. Read the whole prompt before starting. Work phase by phase, verify each phase before moving on, and never skip the verification gates.**

---

## 0. MISSION

Transform this repository — currently a Laravel API + admin panel that exists to serve an **Android app** — into a **production-ready, responsive, installable PWA web version of the actual product** (rider/customer side), while:

1. **Deleting** everything Android-era, junk, secret, or deployment-hostile that must not exist in a public GitHub repo.
2. **Porting** the database from **MySQL to PostgreSQL (Neon.tech)**.
3. **Building** a customer-facing PWA with Laravel Blade + Tailwind CSS + Alpine.js, responsive at smartphone, tablet, and desktop widths, and **more visually appealing than the Android app**.
4. **Modernizing** the existing 560-view admin panel so it is responsive and visually consistent with the new product.
5. **Preparing** the repo to be pushed to GitHub and deployed on **Render** (web + worker) with a **Neon** Postgres database.

A backup of the original system already exists elsewhere. You are authorized to delete what must be deleted — but **delete only what is listed or clearly belongs to the same categories**.

---

## 1. SYSTEM FACTS (verified by recon — trust these, but re-verify anything you're about to change)

| Fact | Value |
|---|---|
| Framework | Laravel **9** (`laravel/framework: ^9.0`), PHP **^8.1** (README claiming "Laravel 5.6" is stale — composer.json is source of truth) |
| API | `routes/api.php` — **2,307 lines / ~1,315 routes**, consumed by the Android app via `merchant` middleware (public_key + secretKey) and login tokens |
| Web routes | `routes/web.php` — 1,506 lines: admin panel, cron endpoints, **payment gateway callbacks**, webhook routes |
| Views | **675** Blade files — **560 in `resources/views/merchant/`** (admin panel), plus `corporate/`, `delivery/`, `auth/`, 100+ payment views |
| Admin assets | Precompiled Bootstrap 4 admin template in `public/global/` + `public/theme/` (+ `public/developer/`) — **no build step needed for admin** |
| Migrations | **610** in `database/migrations/` — no `->enum()`, no `->json()`, no raw `ENGINE=` found (good sign for Postgres); heavy `->foreign()` usage |
| Raw SQL | **297** `DB::raw` / `whereRaw` / `DB::select` call sites in `app/` — must be audited for MySQL-only syntax |
| Known MySQL-only code | `DATE_FORMAT`, `GROUP_CONCAT`, `FIND_IN_SET` confirmed in `app/Http/Controllers/Helper/ExtraCharges.php` (and likely elsewhere) |
| Frontend tooling | Ancient `package.json` (laravel-mix 2, Vue 2) referencing `resources/assets/*` which may not exist — verify |
| Realtime | Android app uses a socket microservice: `MICRO_SERVICE_APP_FULL_URL` (node socket on :8443) for ride tracking/chat |
| Push | OneSignal — root `OneSignalSDKWorker.js` already loads OneSignal **Web** SDK v16 (usable for web push) |
| Tests | **None** (`tests/` is empty) |
| Git | **Not a git repository yet.** `.gitignore` exists and already excludes `vendor/`, `.env`, `queue.log`, `storage/logs` |
| DB config | `DB_CONNECTION=mysql`, `DB_DATABASE` empty in `.env`; `QUEUE_CONNECTION=database` |
| Deploy files | **No** `.env.example`, **no** `Procfile`, **no** `render.yaml` |
| CORS | `routes/api.php` sends `Access-Control-Allow-Origin: *` with wildcard headers at file top |

---

## 2. PHASE 0 — REPO CLEANUP: WHAT TO DELETE

Execute these deletions first so nothing toxic ever reaches GitHub.

### 2.1 Hard deletes (never push these)

| Path | Why |
|---|---|
| `Fixcycle Source Codes Android.zip` (553 MB) | Android source archive. GitHub rejects files >100 MB; this alone makes push impossible. User has a backup. |
| `queue.log` (21 MB) | Rotated queue log at repo root. |
| Root `error_log`, and stray `error_log` files under `routes/`, `app/`, `config/`, `database/migrations/`, `resources/` | PHP error dumps. |
| `BOSS_MESSAGE_DRAFT.md`, `NIDHI_MESSAGE_DRAFT.md`, `SESSION_WORK_SUMMARY.md`, `COMPLETE_FIX_DOCUMENTATION.md`, `PAYMENT_CHECKOUT_ANALYSIS.md`, `PAYMENT_FIXES_SUMMARY.md` | Internal/personal working notes — some contain session and payment details. Not part of a public repo. |
| `.ftpconfig`, `.project`, `.user.ini`, root `php.ini` | Old shared-hosting/IDE artifacts. Render uses nginx; these do nothing there. |
| `OnlineCode.php` | Empty file. |
| `cgi-bin/` | Empty legacy directory. |
| `OneSignalSDKWorker_old.js` | Dead duplicate. |
| `apple-app-site-association` + `apple-app-site-association-dir/` | iOS universal-links file; there is no iOS app. (If Android deep-linking is needed later, add `/.well-known/assetlinks.json` instead — do not add it now.) |
| `resources/views/merchant/website/user_headings.blade-20190628-.php` | Accidental backup file in views. |
| `app/Http/Controllers/Api/test/` (if it only contains scratch/test controllers — inspect first) | Dead code. |

### 2.2 Secrets must leave the web root (CRITICAL)

These files are inside `public/` and are **web-accessible**:

- `public/telebirr_private_key.pem`
- `public/pem-file/user.pem`, `public/pem-file/driver.pem`, `public/pem-file/test.pem`
- Inspect `public/certificates/` and `public/external.pub` the same way.

**Actions:** (1) find every code reference (`grep -rn "pem-file\|telebirr_private_key\|certificates/" app config routes`), (2) move the files to `storage/app/secrets/`, (3) update references to read from there (or from env vars), (4) delete them from `public/`, (5) **flag in your final report that these keys were ever public and must be rotated by the owner.**

### 2.3 Inspect-then-delete (check references before removing)

- Root `index.php` (45 bytes) and `server.php` — legacy dev/shared-hosting front controllers. Confirm nothing routes through them, then delete; `public/index.php` is the only front controller Laravel 9 needs.
- Root `.htaccess` — Apache rules + security headers for shared hosting. Render is nginx → delete root `.htaccess`. **Keep `public/.htaccess`** (Laravel standard, harmless). Note: `.gitignore` currently ignores all `.htaccess` files — un-ignore `public/.htaccess`.
- `documents/` — internal technical docs/diagrams. Remove from repo (user keeps backup) unless they are wanted as public docs; default = remove.
- `public/sound/` — Android notification sounds; keep only if the web app will use them (cheap to keep; default = keep).
- `resources/lang/*` is **gitignored** — verify whether language files exist locally. If the app needs them at runtime, stop ignoring them (translate-keys are baked into views via `@lang()`), otherwise every fresh deploy breaks localization.
- Duplicate/misnamed views: run a scan for `*-old*`, `*-old-*`, `*.blade-*.php`, `*backup*` patterns under `resources/views/` and remove confirmed dead copies.

### 2.4 Gitignore additions

Append (and keep existing entries):

```gitignore
# Deployment & local junk
/public/pem-file/
/public/*.pem
/public/certificates/
error_log
*.log
/storage/logs/
.env.backup
.env.production
.DS_Store
Thumbs.db
*.zip
/documents/
*_DRAFT.md
*_SUMMARY.md
*_ANALYSIS.md
/public/local-files/
```

### 2.5 Git initialization

```bash
git init
git add -A
git status   # MUST NOT list: *.zip, queue.log, .env, *.pem, *_DRAFT.md
```

**Gate 0:** `git ls-files | xargs -I{} du -k "{}"` total < 500 MB; `git ls-files | grep -i "\.pem\|\.zip\|queue.log\|^\.env$"` returns nothing; `git status` clean.

---

## 3. PHASE 1 — DEPLOYMENT SCAFFOLD (Render + Neon)

### 3.1 `.env.example` (create it — it does not exist)

Document every variable the app reads (`grep -rhoE "env\('[A-Z_0-9]+'" app config routes | sort -u`), including at minimum:

```
APP_NAME=Fixcycle
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://your-app.onrender.com

LOG_CHANNEL=daily
QUEUE_CONNECTION=database
SESSION_DRIVER=file

DB_CONNECTION=pgsql
DB_HOST=
DB_PORT=5432
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
DB_SSLMODE=require

MICRO_SERVICE_APP_URL=/
MICRO_SERVICE_APP_FULL_URL=https://your-socket-host

# OneSignal, payment gateways, SMS, mail, maps — list every key the grep finds
```

Delete the checked-in `.env` from any commit (it holds a real `APP_KEY` — generate a fresh one at deploy time; note this in the report).

### 3.2 `render.yaml` (root)

```yaml
services:
  - type: web
    runtime: php
    name: fixcycle-web
    buildCommand: composer install --no-dev --prefer-dist --optimize-autoloader && php artisan migrate --force && npm ci && npm run build
    startCommand: php artisan serve --host 0.0.0.0 --port $PORT
    envVars:
      - key: APP_ENV
        value: production
      - key: APP_DEBUG
        value: "false"
      - key: APP_KEY
        generateValue: true
      - key: DB_CONNECTION
        value: pgsql
      - key: SESSION_DRIVER
        value: file
      - key: QUEUE_CONNECTION
        value: database
      - key: CACHE_DRIVER
        value: file
      - key: LOG_CHANNEL
        value: daily
  - type: worker
    runtime: php
    name: fixcycle-worker
    buildCommand: composer install --no-dev --prefer-dist --optimize-autoloader
    startCommand: php artisan queue:work --tries=3
    envVarsFrom:
      - type: web
```

Notes for you (the agent):
- Confirm the PHP runtime image has `pdo_pgsql`, `gd`, `soap`, `bcmath`, `zip`, `intl` (composer.json requires `ext-gd`, `ext-soap`). If a required extension is missing, add the install step to `buildCommand`.
- The app uses **database queues** — the worker service is mandatory or payments/notifications silently stall.
- Add a `GET /health` route (returns 200) for uptime checks.
- `php artisan serve` is acceptable on Render for this app's scale; if you prefer nginx + php-fpm, document it, but do not over-engineer.

### 3.3 Route security cleanup (web.php)

These are publicly reachable and must go or be guarded:

- `GET /clear-cache`, `GET /queue-start`, `GET /queue-restart` → delete (replace with documented `php artisan` commands in README).
- `GET /migrateData` (`ImportController@test`), `/send-test-invoice`, `/user-otp`, `/subscription`, `/Subscribe_product_place` "dummy" routes → delete if unreferenced, else gate behind `auth` + local env.
- Any route containing `dd(` / `->dump()` / `test` in `routes/api.php` (e.g. `Route::get('test', ...)`) → delete.
- After cleanup: `php artisan route:list` must still contain **every payment callback route** (stripe, flutterwave, paystack, telebirr, etc.) — these are hit by external gateways; never remove them.

**Gate 1:** `composer install` succeeds on a clean clone; `php artisan route:list` runs without error; `.env.example` exists; `render.yaml` valid YAML; no debug/queue-control routes remain in web.php.

---

## 4. PHASE 2 — PORT MySQL → POSTGRESQL (Neon)

This is the riskiest phase. Do it **before** writing new features so everything is tested against Postgres.

### 4.1 Config

- `config/database.php`: default `env('DB_CONNECTION', 'pgsql')`; ensure `pgsql` driver block has `sslmode` support (`'sslmode' => env('DB_SSLMODE', 'prefer')`) — **Neon requires `sslmode=require`**.
- Add `"ext-pdo_pgsql": "*"` to composer `require` **only if** you confirm all deploy targets have it; otherwise document it as a server requirement.
- Convert any MySQL-specific DSN/charset config (`utf8mb4` collation settings do not apply to pgsql — strip `charset`/`collation` keys from the pgsql config block).

### 4.2 Migration audit

- Run the full 610-migration suite against a **local Postgres** database: `php artisan migrate:fresh`.
- Known-good: no `->enum()`, no `->json()`, no raw `ENGINE=` statements found in recon.
- Fix, in order of likelihood:
  - **Index names > 63 chars** (Postgres truncates; two indexes then collide) → shorten.
  - **Foreign keys added before the referenced table exists** → reorder migrations (Postgres enforces FK existence at ALTER time; MySQL in older modes was lenient).
  - `->after(...)` column positioning is supported — OK. `DB::statement('ALTER TABLE ...')` with MySQL syntax → rewrite.
  - `change()` calls need `doctrine/dbal` (already pinned at `2.13.1` — verify it works with the column types used).
- Seeders live in `database/seeds/` (classmap-autoloaded — keep that structure): `php artisan db:seed` must succeed on Postgres.

### 4.3 Raw SQL audit (297 call sites)

```bash
grep -rn "DB::raw\|whereRaw\|orWhereRaw\|DB::select\|DB::statement\|orderByRaw\|havingRaw" app routes config --include="*.php" > raw-sql-inventory.txt
```

Work through the inventory file by file. Rewrite MySQL-only constructs:

| MySQL | PostgreSQL |
|---|---|
| `FIND_IN_SET(a, b)` | `a = ANY(string_to_array(b, ','))` |
| `DATE_FORMAT(d, '%Y-%m')` | `to_char(d, 'YYYY-MM')` |
| `GROUP_CONCAT(x SEPARATOR ',')` | `string_agg(x, ',')` |
| `IFNULL(a,b)` / `IF(...)` | `COALESCE(a,b)` / `CASE WHEN ... END` |
| `` `backticks` `` | `"double quotes"` (or drop quoting) |
| `RAND()` | `RANDOM()` |
| `NOW()` / `CURDATE()` | OK (keep) |
| `ON DUPLICATE KEY UPDATE` | `ON CONFLICT (...) DO UPDATE` |
| `LIMIT x,y` in UPDATE/DELETE | `LIMIT x OFFSET y` (works) but subquery-in-UPDATE differences need review |
| Implicit `GROUP BY` relaxation (selecting non-aggregated columns) | Postgres is strict — add columns to GROUP BY or use aggregates |
| String `+` concatenation via `CONCAT()` | `CONCAT()` exists in PG — OK; bare `+` on strings → `||` |

**Rule:** never "fix" the SQL by weakening business logic. Every rewrite must be behavior-equivalent. Keep the inventory file updated and delete it (or commit it as docs) at the end of the phase.

### 4.4 Verification

- `php artisan migrate:fresh --seed` on local Postgres: clean.
- Boot the app and smoke-test: admin login, one dashboard page, one list page with filters/search (these hit the most raw SQL), one payment-config page.
- Run the Android-facing API against Postgres with a few known requests (login, home-screen, configuration) and confirm identical JSON shapes.

**Gate 2:** full migrate + seed green on Postgres; zero MySQL-only functions left (`grep -rn "FIND_IN_SET\|DATE_FORMAT\|GROUP_CONCAT\|ON DUPLICATE KEY\|RAND()" app` returns nothing); admin panel boots and lists data.

---

## 5. PHASE 3 — BUILD THE CUSTOMER-FACING PWA

This is the core deliverable: the **web version of the Android app**.

### 5.1 Frontend toolchain

- Replace the dead laravel-mix 2 setup with **Laravel 9 Vite** (`laravel-vite-plugin`) + **Tailwind CSS** + **Alpine.js**.
- If `resources/assets/` referenced by `webpack.mix.js` doesn't exist (verify), delete `webpack.mix.js` and the stale scripts from `package.json` rather than carrying dead config.
- Admin panel keeps its precompiled `public/global`/`public/theme` assets — do **not** run those through Vite.
- Structure: `resources/css/app.css` (Tailwind + design tokens), `resources/js/app.js` (Alpine + map + API helpers), views in `resources/views/web/`.

### 5.2 Design system (must look better than the Android app)

Define once in Tailwind config, use everywhere:

- **Brand palette:** one primary + one accent, 50–900 shades; success/warning/danger; light **and** dark surface tokens.
- **Typography:** modern sans (e.g. Inter/Plus Jakarta Sans), fluid type scale with `clamp()`.
- **Shape/elevation:** 16–24 px radii, soft layered shadows, subtle borders — no flat 2015 Bootstrap look.
- **Components:** buttons, inputs, cards, bottom sheets, chips/badges, skeletons (loading), toasts, empty states — all as Blade partials under `resources/views/web/components/`.
- **Motion:** short (150–250 ms) transitions on taps/hovers; respect `prefers-reduced-motion`.
- **Accessibility:** WCAG AA contrast, visible focus rings, 44 px minimum touch targets, semantic landmarks, form labels bound to inputs.

### 5.3 Responsive strategy (three breakpoints, one codebase)

- **Mobile (< 640 px):** single column; fixed bottom nav bar (Home / Bookings / Wallet / Profile); map + draggable bottom sheet for booking; full-screen tracking.
- **Tablet (640–1024 px):** two-column layouts (map 60% / panel 40%); side nav collapses to icon rail.
- **Desktop (> 1024 px):** max-w-7xl centered layouts; left sidebar nav; map beside booking panel; hover states everywhere.
- Use Tailwind responsive variants only — **no `user-scalable=0`, no fixed pixel widths, no horizontal scrolling at 360 px.**
- Test every page at **360, 768, 1024, 1440, 1920 px** widths.

### 5.4 Routes & pages (new `Route::prefix('web')` group in `routes/web.php`)

Build the customer journey by **reusing the existing API/controllers** — call the same controller methods the Android API routes call (extract shared logic into services if duplication appears). Do not fork business logic.

| Route | Page | Notes |
|---|---|---|
| `/` | Landing / marketing hero | Service segments (taxi, handyman, delivery…), CTAs, app-store-for-Android link, install-PWA prompt |
| `/login`, `/signup`, `/otp` | Auth | Phone/OTP + email; same verification flow as `Account\UserController` |
| `/app` | **Home** (core screen) | Interactive map (Leaflet + OpenStreetMap — no API key needed; use Google Maps only if env key supplied), pickup pin, service/vehicle picker from `HomeController@cars` / `WebsiteController` data, saved addresses |
| `/app/booking/new` | Booking flow | Pickup/drop, fare estimate (`estimate`), options, payment method, confirm (`api.booking-confirm`) |
| `/app/booking/active` | Live tracking | Driver location polling, ETA, driver card, **call + in-app chat** (poll `Api\ChatController`), SOS, cancel |
| `/app/bookings`, `/app/bookings/{id}` | History + invoice | List, filters, detail, receipt |
| `/app/wallet` | Wallet + payments | Balance, add money (reuse existing gateway webviews — they are already browser flows), transactions |
| `/app/profile` | Profile, addresses, notifications settings, referral | |
| `/app/support` | Help / FAQ / CMS pages | `CommonController@UserFaq`, `UserCmsPage` |
| `offline` | Offline fallback page | Served by service worker |

**Realtime (replacing the Android socket microservice):** implement **polling first** — 5–10 s intervals on the active-booking screen via Alpine (`fetch` + backoff), because it needs zero new infrastructure and works on Render free/hobby. Structure it behind a small `resources/js/booking-tracker.js` so a Pusher/Soketi/WebSocket upgrade later is a one-file change. Do **not** port the Node socket into this repo.

**Push notifications:** register the existing OneSignal Web SDK (v16 worker already present) in the app layout; move `OneSignalSDKWorker.js` into `public/` if not already served from there; add `manifest` service-worker merging notes (OneSignal SW + PWA offline SW must coexist — either chain `importScripts` or use OneSignal's own SW for both; pick one approach and verify installability + push).

### 5.5 PWA installability

- `public/manifest.webmanifest`: name, short_name, `start_url: "/app"`, `display: standalone`, theme/background colors matching brand, maskable icons (192 + 512 px + 512 maskable) — generate from the existing logo.
- `public/sw.js`: cache-first for static assets (hashed), network-first for HTML/API, offline fallback page; **never cache API POST responses or authenticated HTML.**
- Link manifest + `theme-color` + apple-touch-icon in the shared web layout.
- Serve over HTTPS (Render does), add HSTS if possible.

**Gate 3:** full rider journey works end-to-end in a browser at 360/768/1440 px; Chrome Lighthouse **PWA installable** (manifest + SW valid); Lighthouse Performance ≥ 80 on `/` and `/app`; no console errors; booking appears in admin panel; chat and wallet work; app installs to home screen on Android Chrome and desktop Chrome.

---

## 6. PHASE 4 — MODERNIZE THE ADMIN PANEL (responsive, consistent)

560 views — **do not rewrite them all.** Use leverage:

1. **Shared layouts first:** `resources/views/merchant/layouts/{header,nav,sidebar,footer,main}.blade.php` and `corporate/layouts/main.blade.php`. Make the sidebar an off-canvas drawer on mobile (hamburger toggle), tables horizontally scrollable, cards stacking via responsive grid classes.
2. **Responsive CSS layer:** one new stylesheet (loaded after `theme/css/site.min.css`) that overrides the admin template at mobile breakpoints — `.row`/`.card`/`.dataTable` behavior, touch-friendly nav, font scaling. This fixes hundreds of views without editing them.
3. **Surgical view fixes:** only open individual views that remain broken after 1–2 (dashboard `home.blade.php`, list/index pages with wide tables, forms). Priority order: Dashboard → Bookings/Orders → Drivers/Users → Payments → everything else.
4. **Visual refresh:** new color tokens + typography from Phase 3's design system, refreshed card/panel styling, consistent icons (one icon set — keep FontAwesome already vendored or add a modern set), empty/loading states.
5. Keep **all** existing routes, permissions (`spatie/laravel-permission`), and form actions byte-identical — this phase must be purely presentational.

**Gate 4:** admin login → dashboard → 10 most-used pages usable at 380 px (iPhone) and 1440 px; no permission regressions; `grep` confirms no route/controller changes in this phase's diff.

---

## 7. PHASE 5 — SECURITY & PRODUCTION HARDENING

- `.env` for production: `APP_DEBUG=false`, fresh `APP_KEY`, real `APP_URL` (fixes `asset()` URLs), `SESSION_SECURE_COOKIE=true`.
- **CORS:** replace the wildcard `header('Access-Control-Allow-Origin: *')` block at the top of `routes/api.php` with `config/cors.php` + `fruitcake/laravel-cors` (ships with Laravel 9) restricted to your domains; keep wildcard temporarily **only** if the Android app is known to call from arbitrary origins — document the decision.
- Confirm `TrustProxies` is configured for Render's load balancer (`X-Forwarded-Proto`), otherwise redirects loop to http.
- Rate limiting: verify `limit_api` middleware is registered and applies to OTP/login routes; add a throttle to `/web` auth routes.
- Ensure `public/.htaccess`-equivalent protections hold on nginx (Render): sensitive dotfiles are not served by default; still verify `.env`, `*.md`, `storage/` return 404 via the public path.
- Remove `laravel/telescope` & `debugbar` from production (`dont-discover` already lists Telescope — verify it's actually disabled at runtime, and that no `telescope` route is exposed).
- `php artisan optimize` in build; `config:cache`, `route:cache`, `view:cache`.
- Rotate every credential that was ever committed (`.env` APP_KEY, pem keys) — list them in your final report.

---

## 8. PHASE 6 — TESTS & FINAL VERIFICATION

There are currently **zero tests**. Add a minimal, high-value suite (PHPUnit, already a dev dependency):

1. **Route smoke tests:** GET each Phase-3 web route → 200/302 (never 500).
2. **API contract tests:** user `login`, `signup`, `configuration`, `home-screen`, `cars` — assert JSON shape (`result`/`message` keys) so the Android app can't be broken by the port.
3. **Postgres-only test:** `tests` run against `sqlite` is NOT acceptable for this port — run CI/locally against Postgres (document how; use `phpunit.xml` env overrides).
4. **Admin auth test:** merchant login → dashboard 200; wrong password → redirect.

### Final acceptance checklist (run and paste results in your report)

```bash
composer install
npm ci && npm run build          # Vite build succeeds
php artisan migrate:fresh --seed # on Postgres
php artisan route:list | wc -l   # compare to baseline captured in Phase 1
php artisan test                 # new suite green
git ls-files | grep -iE "\.zip$|\.pem$|queue\.log$|^\.env$"   # must be empty
git count-objects -vH            # packed repo size sane (< 500 MB)
```

Manual: complete a booking as a guest-turned-user in the browser at 360 px and 1440 px; confirm it appears in the admin panel; confirm the payment-gateway callback routes still exist; run Lighthouse (Performance + PWA + Best Practices) on `/` and `/app`.

### Report format (end of job)

1. Files deleted (grouped by category) + keys needing rotation.
2. Raw SQL inventory: total found / rewritten / remaining (must be 0 MySQL-only).
3. New routes and pages shipped.
4. Lighthouse scores.
5. Anything you deliberately did not do, with reasons.

---

## 9. RULES OF ENGAGEMENT

1. **The Android API contract must not break.** `routes/api.php` endpoints keep their URIs, methods, and JSON shapes — an installed Android app still points at this server. Additive changes only.
2. **Never remove payment callback/webhook routes.**
3. Prefer editing shared layouts/asset files over mass-editing the 560 admin views.
4. No new runtime dependencies without checking they're already in `composer.json`/`package.json` first; when a new one is genuinely needed (e.g. Tailwind, Vite), add it project-locally and justify it.
5. No `dd()`, no swallowed exceptions, no `@` suppression, no commented-out "temporary" code in the final diff.
6. Keep changes in separable commits per phase (`cleanup`, `deploy-scaffold`, `postgres-port`, `customer-pwa`, `admin-responsive`, `security`, `tests`) so each can be reviewed/reverted independently.
7. If something in this prompt contradicts what you find in the code, **trust the code, adapt the plan, and note the deviation in your report.**
