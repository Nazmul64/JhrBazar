<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);
echo "Asset URL: " . asset('uploads/test.jpg') . "\n";
echo "Request URL: " . request()->root() . "\n";
echo "Config APP_URL: " . config('app.url') . "\n";
