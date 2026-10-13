<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Web PWA merchant credentials
    |--------------------------------------------------------------------------
    |
    | The web app (resources/js/app.js) talks to the same public API as the
    | mobile apps. Those endpoints are guarded by ApiMiddleware which expects
    | merchant credentials (public/secret key headers, a bearer token, or an
    | `access_pin` body field). The browser cannot hold a secret key, so the
    | web build authenticates the merchant with a server-injected `access_pin`
    | exposed to the page as `window.FixConfig`.
    |
    | Set these in .env to match the merchant whose countries/branding the web
    | app should serve. When unset, the API returns "unauthorised request".
    |
    */

    'merchant_id' => env('WEB_MERCHANT_ID'),

    'access_pin' => env('WEB_MERCHANT_ACCESS_PIN'),

    /*
    |--------------------------------------------------------------------------
    | Web display currency
    |--------------------------------------------------------------------------
    |
    | Currency shown across the web UI. Payment gateways keep their own
    | configuration and are intentionally not affected by this value.
    |
    */

    'currency' => env('WEB_CURRENCY', 'ZMW'),

    'currency_symbol' => env('WEB_CURRENCY_SYMBOL', 'K'),

];
