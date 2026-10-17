<?php
require __DIR__.'/vendor/autoload.php';
$app=require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$u = App\Models\User::find(9);
var_dump($u ? substr($u->password, 0, 20) : 'null');
$u2 = App\Models\User::where('email', 'testuser.fixcycle@example.com')->first();
var_dump($u2 ? substr($u2->password, 0, 20) : 'null');
