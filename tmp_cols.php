<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$cols = DB::select("select column_name, data_type from information_schema.columns where table_name = 'users' order by ordinal_position");
echo "users columns (" . count($cols) . "):\n";
foreach ($cols as $c) {
    echo "  " . $c->column_name . " : " . $c->data_type . "\n";
}
echo "\nuser_details columns:\n";
$c2 = DB::select("select column_name, data_type from information_schema.columns where table_name = 'user_details' order by ordinal_position");
foreach ($c2 as $c) {
    echo "  " . $c->column_name . " : " . $c->data_type . "\n";
}
