<?php
use Illuminate\Support\Facades\Cache;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Bootstrap the application kernel to initialize configurations and service providers
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$start = microtime(true);
$val = Cache::get('home_data_v2');
$end = microtime(true);

echo "Retrieve time: " . ($end - $start) . " seconds\n";
echo "Value is " . ($val ? 'found (size: ' . strlen(serialize($val)) . ')' : 'NOT found') . "\n";

// Let's also see what Cache store is being used
echo "Default cache store: " . config('cache.default') . "\n";
