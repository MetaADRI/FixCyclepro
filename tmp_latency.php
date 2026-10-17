<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$cfg = config('database.connections.pgsql');
echo "host=" . ($cfg['host'] ?? '?') . " port=" . ($cfg['port'] ?? '?') . " db=" . ($cfg['database'] ?? '?') . "\n";

for ($i = 1; $i <= 6; $i++) {
    $t = microtime(true);
    DB::select('select 1');
    printf("query %d: %.1f ms\n", $i, (microtime(true) - $t) * 1000);
}

$t = microtime(true);
$r = DB::select('select inet_server_addr() as addr, current_setting(\'server_version\') as v');
printf("server: %s  (%.1f ms)\n", json_encode($r[0]), (microtime(true) - $t) * 1000);

$t = microtime(true);
DB::disconnect('pgsql');
DB::reconnect('pgsql');
printf("reconnect: %.1f ms\n", (microtime(true) - $t) * 1000);
