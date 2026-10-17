<?php
require __DIR__.'/vendor/autoload.php';
$app=require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$payload = [
    'access_pin' => '568938',
    'phone' => 'testuser.fixcycle@example.com',
    'password' => 'Passw0rd!23',
    'login_type' => 'EMAIL',
    'requested_from' => 'web',
];

$req = Illuminate\Http\Request::create('/api/user/login', 'POST', $payload);
$req->headers->set('Accept', 'application/json');
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$resp = $kernel->handle($req);
var_dump($resp->getStatusCode(), $resp->getContent());
