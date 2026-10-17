<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Route;

$req = Illuminate\Http\Request::create('oauth/token', 'POST');
// no Accept header (mirrors UserController::SignUp line 452)
$resp = Route::dispatch($req);
echo "STATUS: " . $resp->getStatusCode() . "\n";
echo "CT: " . $resp->headers->get('content-type') . "\n";
echo "BODY: " . substr($resp->getContent(), 0, 800) . "\n";
