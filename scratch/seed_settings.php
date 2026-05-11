<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\GenaralSetting;

if (GenaralSetting::count() === 0) {
    GenaralSetting::create([
        'website_name' => 'JHR Bazar',
        'website_title' => 'JHR Bazar | Ultimate Shopping Destination',
        'default_currency' => 'BDT',
        'currency_position' => 'left',
        'mobile_number' => '+880 1711 257498',
        'email_address' => 'support@jhrbazar.com',
        'address' => 'Dhaka, Bangladesh',
        'footer_text' => 'JHR Bazar is your ultimate destination for premium quality products. We ensure the best shopping experience with fast delivery and secure payments.',
        'show_download_app' => true,
        'show_footer_section' => true,
    ]);
    echo "Seed data for GenaralSetting created successfully." . PHP_EOL;
} else {
    echo "GenaralSetting already has data." . PHP_EOL;
}
