<?php

use Illuminate\Support\Facades\Artisan;

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/bootstrap/app.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

Artisan::call('config:clear');
Artisan::call('cache:clear');
Artisan::call('route:clear');
Artisan::call('view:clear');
Artisan::call('config:cache');

echo '✅ Caché limpiada correctamente';
