<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
foreach (['orders', 'order_items', 'couriers', 'products', 'users'] as $table) {
    $res = Illuminate\Support\Facades\DB::select("show tables like '$table'");
    echo "$table: " . (count($res) ? 'exists' : 'missing') . "\n";
}
