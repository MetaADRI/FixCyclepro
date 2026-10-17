<?php
require __DIR__.'/vendor/autoload.php';
$app=require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$u = App\Models\User::find(9);
$u->password = Illuminate\Support\Facades\Hash::make('Passw0rd!23');
$u->save();
echo 'ok';
