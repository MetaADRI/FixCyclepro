<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\DB;

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$t0 = microtime(true);
DB::listen(function ($q) use ($t0) {
    $sql = $q->sql;
    $bindings = json_encode($q->bindings);
    fwrite(STDERR, sprintf("[%6.2fs] (%5.1fms) %s | %s\n", microtime(true) - $t0, $q->time, $sql, $bindings));
});

$payload = [
    'access_pin' => '568938',
    'first_name' => 'Test',
    'last_name' => 'User',
    'email' => 'testuser.fixcycle@example.com',
    'phone' => '+260970000001',
    'password' => 'Passw0rd!23',
    'country_id' => 2,
    'country_code' => 'ZM',
    'login_type' => 'EMAIL',
    'requested_from' => 'web',
];

$request = Illuminate\Http\Request::create('/api/user/signup', 'POST', $payload);
$request->headers->set('Accept', 'application/json');

fwrite(STDERR, "=== HANDLING ===\n");
$response = $kernel->handle($request);
fwrite(STDERR, "=== DONE status=" . $response->getStatusCode() . " ===\n");
echo $response->getContent() . "\n";
