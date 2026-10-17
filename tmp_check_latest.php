<?php
require __DIR__.'/vendor/autoload.php';
$app=require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$u = Illuminate\Support\Facades\DB::table('users')->orderByDesc('id')->first(['id','email','UserPhone','country_id','return_data']);
echo "id={$u->id} email={$u->email} phone={$u->UserPhone} country={$u->country_id} has_return_data=" . (!is_null($u->return_data) ? 'yes' : 'no') . "\n";
