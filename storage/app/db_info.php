<?php

use Illuminate\Support\Facades\DB;

echo 'size=' . DB::table('pg_database')->selectRaw('pg_size_pretty(pg_database_size(datname)) as size')->where('datname', DB::getDatabaseName())->value('size') . PHP_EOL;
foreach (['merchants', 'users', 'countries', 'configurations', 'application_configurations', 'booking_configurations', 'cms_pages', 'payment_options'] as $table) {
    try {
        echo $table . '=' . DB::table($table)->count() . PHP_EOL;
    } catch (\Throwable $e) {
        echo $table . '=ERR ' . $e->getMessage() . PHP_EOL;
    }
}
