<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

$tables = [
    'mobile_maintenance',
    'mobile_sales',
    'mobile_inventory',
    'mobile_debts',
    'mobile_expenses',
];

foreach ($tables as $t) {
    echo $t . ': ' . (Schema::hasTable($t) ? 'exists' : 'missing') . PHP_EOL;
}
