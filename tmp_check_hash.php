<?php
require __DIR__.'/vendor/autoload.php';
$app=require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
var_dump(Illuminate\Support\Facades\Hash::check('Passw0rd!23', '$2y$10$uQlxZ.TFxAn0zdhKUSJtDuQ0dYO5IZ7DPLeXVIr5/9yrMunDx3zUO'));
