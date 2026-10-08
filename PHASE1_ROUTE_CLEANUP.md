# Phase 1 — Route cleanup, deploy scaffold & Gate 1 results

Recorded during execution of `OPENCODE_MEGA_PROMPT.md` Phase 1 (+ Phase 0 leftovers).

## Gate 1 results

| Check | Result |
|---|---|
| `composer install` (clean, PHP 8.2, ext-gd/soap/pdo_pgsql) | **exit 0**, 107 packages |
| `php artisan route:list` | **exit 0**, **2181 lines** — baseline committed as `route-baseline.txt` |
| `.env.example` exists, documents every `env()` variable (117 found by grep) | ✓ |
| `render.yaml` valid YAML (python yaml.safe_load) | ✓ |
| Debug / queue-control routes in `routes/web.php` | **0 remaining** |
| Payment callback / webhook routes still registered | **80** (stripe, telebirr, cashpay, hub2, viupay, interswitch, razorpay, pesapal, …) |
| Fresh `php artisan migrate` (local MySQL) | **609/609 migration files, 384 tables, exit 0** |
| HTTP smoke (`php artisan serve`): `GET /health` → 200 "OK", `GET /` → 200, `GET /clear-cache` → 404 | ✓ |

## Route deletions (this phase)

### routes/web.php — debug & dummy routes (11)
- `migrateData` (`ImportController@test`), `/send-test-invoice`, `user-otp` (dummy), `subscription` (dummy), `Subscribe_product_place` (dummy URL; controller method still used internally by cron)
- `/clear-cache`, `/queue-start`, `/queue-restart` — replaced by README "Maintenance" artisan commands
- `toll-test`, `/test-referral` — hardcoded-ID debug controllers, zero references
- Added: `GET /health` (named `health`)

### routes/api.php — test / dd routes (6)
- `GET test` (dd), `POST send-mail` (target method `EmailController@test` never existed → was a guaranteed 500), `GET /test-merchant-notification`, `GET /test-email` (dd)
- 3 × `Api\ButtlerController` routes (`/{alias_name}/api/trips*`) — class does not exist anywhere in the repo or git history (was already 500 on every call)

### routes referencing non-existent controller classes (91 removed, 3 replaced)
Systematic scan (`class_exists` over every registered route) found **91 routes** whose controller
class exists nowhere in this snapshot (never committed; absent from the `.kilo` worktree too):
`Api\FoodController`, `Api\DeliveryController`, `Api\JobVacancyController`, `Api\LatraController`,
`Api\BusBooking\DriverBusBookingController`, `LaundryOutlet\Api\DriverController`,
`HandymanStore\Api\StoreApiController`, `BusinessSegment\{Order,Account,Option,WalletTransaction,Card,MembershipPlan}Controller`,
`BusinessSegment\OrderController`, `DashBoardController@getKeys`.
These crashed `php artisan route:list` (reflection) and returned class-not-found 500s if hit.

- **88 deleted outright** (URI+method+class all dead).
- **3 replaced with same-URI/same-name closure routes returning `abort(404)`** because blades render them:
  `order.index`, `order.search` (`merchant/order/index.blade.php`), `merchant.delivery-services-report`
  (`home.blade.php` — admin dashboard). Today those links 500; now they 404 while views keep rendering.
- ⚠️ **OWNER DECISION NEEDED:** if the Android app calls any of these URIs (notably food/delivery/
  business-segment/bus-booking endpoints), restore the controllers from the backup zip — they cannot
  be reconstructed from this repo.

### Not touched (pre-existing, documented)
- **~144 routes** reference an existing class but a **missing method** (e.g. `CountryController@show`,
  `ExtraCharges@NewnightchargeEstimate`, most resource `show` pages). They do not block `route:list`
  and were already 500s; fixing them is out of Phase 1 scope.
- `Route::post('/driver/location/test', as: api.driver-location)` — URI contains "test" but it is a
  named Android API endpoint; kept per contract rule.
- All payment callbacks/webhooks verified intact.

## Other fixes required by Gate 1

- `app/Http/Controllers/PaymentMethods/S3P_Pay/S3PPayController.php:336` — parse error
  (`$response,,'trans_id'` double comma) made the whole class unloadable → typo fixed.
- `resources/lang/en-old/` — backup locale, 0 references → deleted (Phase 0 leftover).
- README: stale "Laravel 5.6 / PHP 7.3" corrected to Laravel 9 / PHP ≥8.1; added Maintenance section
  documenting the artisan commands that replaced the deleted web routes.

## Migration suite repairs (fresh-install blockers)

`php artisan migrate` from an empty DB never worked with this suite. Repairs (all verified by a
full green run):

1. **Trailing-space column names** (12 in `create_web_site_home_page_translations_table`) — MySQL
   trimmed them silently; MariaDB/Postgres reject them.
2. **Duplicate column drift** — 17 lines across 8 `add_columns_*` migrations re-added columns already
   created earlier (e.g. `handyman_booking_dispute`, `order_id_verification`) → removed duplicates.
3. **Duplicate columns inside single CREATE blocks** — `reward_systems` created `trips_type` ×3 →
   deduplicated (runtime-proven failure).
4. **`$table->int()`** — not a Blueprint method (3 migrations, 7 columns) → `integer()`.
5. **FK identifier names > 63 chars** (5) — MySQL caps at 64, Postgres at 63 → explicit short names.
6. **FK column type mismatches vs parent PK** — `unsignedBigInteger` where parent is `int unsigned`
   (errno 150) in `language_vehicle_delivery_packages`, `language_merchant_membership_plans`; and
   `foreignId()->constrained()` vs `int unsigned merchants.id` in `stripe_payouts` → corrected.
7. **Missing `<?php` tag** — `2025_01_20_100002_create_language_merchant_membership_plans_table.php`
   was being "run" as plain text (class never loaded) → tag added.
8. Interrupted-run bookkeeping: `create_country_payment_table` had fully executed before a
   killed process prevented its record insert → record restored (table+FKs verified identical).

## Environment notes (local, not committed)

- PHP **8.2.33** installed via winget (project requires `^8.1`; XAMPP only has 8.0) with
  `php.ini` enabling gd/soap/curl/mbstring/intl/zip/pdo_pgsql/pdo_mysql/fileinfo/exif/openssl.
- Local **MariaDB** (XAMPP) on 3306, database `fixcycle` — `.env` (untracked) now points at it
  (`DB_DATABASE=fixcycle`, `DB_USERNAME=root`).
- Local environment used MySQL for this gate; Postgres/Neon port is Phase 2 (spec: tests must run
  against Postgres).

## Deviations from the mega-prompt

1. Commit labels: added `route-cleanup` and `migration-fixes`-style commits beyond the seven
   suggested labels (route cleanup is part of Phase 1; migration repairs are Phase 2.2 work found
   early because Gate 1 needed a booting app).
2. `php artisan serve` used for the HTTP smoke test (as render.yaml plans to) — works fine here.
3. Removed-classes restoration (see OWNER DECISION above) cannot be done from this repo.
