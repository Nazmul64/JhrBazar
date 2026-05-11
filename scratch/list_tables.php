<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

foreach(DB::select('SHOW TABLES') as $v) {
    echo array_values((array)$v)[0] . PHP_EOL;
}
