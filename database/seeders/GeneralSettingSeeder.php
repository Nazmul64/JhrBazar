<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GenaralSetting;

class GeneralSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        GenaralSetting::updateOrCreate(
            ['id' => 1],
            [
                'website_name' => 'JHR Bazar',
                'website_title' => 'JHR Bazar - Best E-commerce Platform',
                'default_currency' => 'BDT (৳)',
                'currency_position' => 'Prefix',
                'mobile_number' => '01700000000',
                'email_address' => 'info@jhrbazar.com',
                'address' => 'Dhaka, Bangladesh',
                'primary_color' => '#57b500',
                'top_header_color' => '#57b500',
                'header_color' => '#ffffff',
                'font_family' => 'Arial, sans-serif',
                'font_size' => '14px',
                'products_per_row_mobile' => 2,
                'products_per_row_desktop' => 6,
                'layout_style' => 'container',
                'show_marquee' => 1,
                'marquee_text' => 'Welcome to JHR Bazar! Enjoy the best shopping experience.',
                'show_download_app' => 1,
                'show_footer_section' => 1,
            ]
        );
    }
}
