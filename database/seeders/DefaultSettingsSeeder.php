<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShippingCharge;
use App\Models\SteadfastCourier;
use App\Models\TrackingSetting;

class DefaultSettingsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Default Shipping Charges
        if (ShippingCharge::count() === 0) {
            ShippingCharge::create([
                'area_name' => 'ঢাকার ভেতরে (Inside Dhaka)',
                'charge'    => 70,
                'status'    => 1,
            ]);
            ShippingCharge::create([
                'area_name' => 'ঢাকার বাইরে (Outside Dhaka)',
                'charge'    => 130,
                'status'    => 1,
            ]);
        }

        // 2. Default Steadfast Courier record
        SteadfastCourier::firstOrCreate(
            ['id' => 1],
            [
                'api_key'    => '3q3p9dhktyoyggpgdpb4ym2stzajg7rf',
                'secret_key' => 'naurovhhgpcggssy3tqj67ui',
                'url'        => 'https://portal.packzy.com/api/v1',
                'status'     => 1,
            ]
        );

        // 3. Default Tracking Settings
        TrackingSetting::firstOrCreate(
            ['id' => 1],
            [
                'is_active' => true,
                'track_direct' => true,
                'track_utm' => true,
            ]
        );
    }
}
