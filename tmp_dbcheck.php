<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

foreach (['users', 'countries', 'country_areas', 'language_user', 'user_languages', 'oauth_access_tokens', 'oauth_clients', 'user_details', 'payment_options_configurations'] as $t) {
    try {
        echo str_pad($t, 32) . DB::table($t)->count() . "\n";
    } catch (\Exception $e) {
        echo str_pad($t, 32) . "MISSING/FK: " . substr($e->getMessage(), 0, 80) . "\n";
    }
}
