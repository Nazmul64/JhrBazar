<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Category;
use App\Models\Product;
use App\Models\SellerProduct;
use App\Models\Banner;
use App\Models\DigitalProduct;
use App\Models\SellerDigitalProduct;
use App\Models\Shop;
use App\Models\GenaralSetting;
use App\Models\OurBrand;
use App\Models\SociallinkList;
use Illuminate\Support\Facades\DB;

function measure($name, $callback) {
    $start = microtime(true);
    $result = $callback();
    $end = microtime(true);
    $time = ($end - $start) * 1000;
    echo sprintf("%-40s: %.2f ms\n", $name, $time);
    return $result;
}

measure('DB Connection', function() {
    return DB::connection()->getPdo();
});

measure('GeneralSetting::first()', function() {
    return GenaralSetting::first();
});

measure('Active Banners', function() {
    return Banner::where('is_active', 1)->latest()->get();
});

measure('Categories with Sub', function() {
    return Category::with(['subCategories' => fn($q) => $q->where('is_active', 1)->orderBy('name', 'asc')])
        ->where('is_active', 1)
        ->orderBy('name', 'asc')
        ->get();
});

$productColumns = ['id', 'name', 'slug', 'thumbnail', 'selling_price', 'discount_price', 'is_active', 'created_at', 'seller_id', 'cash_on_delivery', 'online_payment', 'frontend_sections', 'stock_quantity'];

measure('Admin Products Query (is_popular)', function() use ($productColumns) {
    return Product::where('is_active', 1)->select(array_filter($productColumns, fn($c) => $c !== 'seller_id'))->withCount('reviews')->withAvg('reviews', 'rating')->where('is_popular', 1)->latest()->limit(10)->get();
});

measure('Seller Products Query (is_popular)', function() use ($productColumns) {
    return SellerProduct::where('is_active', 1)->select($productColumns)->withCount('reviews')->withAvg('reviews', 'rating')->where('is_popular', 1)->latest()->limit(10)->get();
});

measure('OurBrand', function() {
    return OurBrand::where('is_active', 1)->orderBy('sort_order')->get();
});

measure('All sections check', function() {
    return Product::where('is_active', 1)
        ->whereNotNull('frontend_sections')
        ->select(['id', 'name', 'slug', 'thumbnail', 'selling_price', 'discount_price', 'is_active', 'created_at', 'cash_on_delivery', 'online_payment', 'frontend_sections', 'stock_quantity'])
        ->withCount('reviews')->withAvg('reviews', 'rating')
        ->latest()
        ->get();
});
