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
        'top_rated_shops_status',
        'primary_color',
        'secondary_color',
        'top_header_color',
        'header_color',
        'footer_color',
        'font_family',
        'font_size',
        'product_title_size_desktop',
        'product_title_size_mobile',
        'product_price_size',
        'product_old_price_size',
        'product_card_height',
        'product_card_width',
        'product_img_height_desktop',
        'product_img_height_mobile',
        'products_per_row_mobile',
        'products_per_row_desktop',
        'show_product_stats',
        'layout_style',
        'marquee_text',
        'show_marquee',
        'slider_height',
        'slider_height_mobile',
        'category_img_height',
        'category_img_width',
        'category_shape',
        'category_behavior',
        'sidebar_behavior',
        'footer_text_color',
        'button_color',
        'button_hover_color',
        'loader_status',
        'membership_logo_1',
        'membership_logo_2',
        'membership_logo_3',
        'show_membership_section',
        'payment_methods_logo',
    ];

    protected $casts = [
        'show_download_app'   => 'boolean',
        'show_footer_section' => 'boolean',
        'top_rated_shops_status' => 'boolean',
        'show_product_stats' => 'boolean',
        'show_marquee' => 'boolean',
        'loader_status' => 'boolean',
        'show_membership_section' => 'boolean',
    ];
}
