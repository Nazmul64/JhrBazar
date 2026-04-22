<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GenaralSetting extends Model
{
    protected $table = 'genaral_settings';

    protected $fillable = [
        'website_name',
        'website_title',
        'default_currency',
        'currency_position',
        'logo',
        'favicon',
        'app_logo',
        'mobile_number',
        'email_address',
        'address',
        'google_playstore_link',
        'apple_store_link',
        'show_download_app',
        'hotline_number',
        'footer_text',
        'footer_logo',
        'footer_qr',
        'show_footer_section',
    ];

    protected $casts = [
        'show_download_app'   => 'boolean',
        'show_footer_section' => 'boolean',
    ];
}
