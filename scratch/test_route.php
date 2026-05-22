<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Http\Request;

$request = Request::create('/api/product/exclusive-panjabi-08-WRtJG', 'GET');
$response = $app->handle($request);

echo "Status: " . $response->getStatusCode() . "\n";
echo "Headers: " . json_encode($response->headers->all()) . "\n";
echo "Content: " . substr($response->getContent(), 0, 1000) . "\n";
