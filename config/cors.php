<?php

$allowedOrigins = array_values(array_filter(array_map(
    'trim',
    explode(',', (string) env('CORS_ALLOWED_ORIGINS', '*'))
)));

// Empty/unset means "keep the legacy wildcard" rather than "deny everything".
if ($allowedOrigins === []) {
    $allowedOrigins = ['*'];
}

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Phase 5 hardening. This replaces the previous global
    | `header('Access-Control-Allow-Origin: *')` block that lived at the top
    | of routes/api.php. Laravel 9 ships the CORS middleware in the framework
    | (Illuminate\Http\Middleware\HandleCors), so no fruitcake/laravel-cors
    | dependency is required (deviation from the plan, per Rules of Engagement
    | #7 — the code/framework is the source of truth).
    |
    | NOTE ON THE ANDROID CLIENT: CORS is a browser-only mechanism. The native
    | Android app (OkHttp/Retrofit) ignores it entirely, so locking this down
    | cannot affect the installed Android app. The PWA is same-origin with the
    | API, so it needs no CORS either. Cross-origin browser callers are the
    | only consumers, and they should be allow-listed.
    |
    | Default preserves today's behaviour ('*') until an operator sets
    | CORS_ALLOWED_ORIGINS explicitly, e.g.:
    |   CORS_ALLOWED_ORIGINS=https://app.example.com,https://admin.example.com
    |
    */

    'paths' => ['*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => $allowedOrigins,

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // Wildcard origins are incompatible with credentials per the CORS spec
    // (browsers reject `Access-Control-Allow-Origin: *` + credentials). The
    // legacy headers set both, which is why credentialed browser calls never
    // actually worked. Keep it false unless explicit origins are configured.
    'supports_credentials' => (bool) env('CORS_SUPPORTS_CREDENTIALS', false),

];
