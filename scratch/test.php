<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$slug = 'exclusive-panjabi-08-WRtJG';
$product = \App\Models\SellerProduct::where('slug', $slug)->first();
if ($product) {
    echo "Found Seller Product:\n";
    echo "ID: " . $product->id . "\n";
    echo "Name: " . $product->name . "\n";
    echo "Slug: " . $product->slug . "\n";
    echo "Is Active: " . $product->is_active . "\n";
    
    // Check if we can get it via FrontendApiController
    $controller = new \App\Http\Controllers\Api\FrontendApiController();
    $response = $controller->getProductBySlug($slug);
    echo "Controller Status Code: " . $response->getStatusCode() . "\n";
    echo "Controller Content:\n";
    echo substr($response->getContent(), 0, 1000) . "\n";
} else {
    echo "Seller Product Not Found in DB!\n";
}
