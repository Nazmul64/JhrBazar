<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackingSetting extends Model
{
    protected $table = 'tracking_settings';

    protected $fillable = [
        'master_tracking_status',
        'gtm_id',
        'gtm_status',
        'ga4_id',
        'ga4_status',
        'facebook_pixel_id',
        'facebook_pixel_status',
        'tiktok_pixel_id',
        'tiktok_pixel_status',
        'datalayer_events',
        'cookie_lifetime_days',
        'enable_visitor_cookies',
        'enable_utm_tracking',
        'enable_referrer_tracking',
        'enable_device_tracking',
        'enable_country_detection',
        'enable_custom_events_logging',
        'enable_admin_reports_dashboard',
    ];

    protected $casts = [
        'master_tracking_status'          => 'boolean',
        'gtm_status'                      => 'boolean',
        'ga4_status'                      => 'boolean',
        'facebook_pixel_status'           => 'boolean',
        'tiktok_pixel_status'             => 'boolean',
        'datalayer_events'                => 'array',
        'cookie_lifetime_days'            => 'integer',
        'enable_visitor_cookies'          => 'boolean',
        'enable_utm_tracking'             => 'boolean',
        'enable_referrer_tracking'        => 'boolean',
        'enable_device_tracking'          => 'boolean',
        'enable_country_detection'        => 'boolean',
        'enable_custom_events_logging'    => 'boolean',
        'enable_admin_reports_dashboard'  => 'boolean',
    ];

    /**
     * Default datalayer events list.
     */
    public static function defaultDataLayerEvents(): array
    {
        return [
            'view_item_list_home'      => true,
            'select_item_home'         => true,
            'add_to_cart_home'         => true,
            'view_item_list_category'  => true,
            'select_item_category'     => true,
            'add_to_cart_category'     => true,
            'view_item_detail'         => true,
            'add_to_cart_detail'       => true,
            'begin_checkout_detail'    => true,
            'view_cart'                => true,
            'begin_checkout_cart'      => true,
            'purchase'                 => true,
            'order_landing'            => true,
        ];
    }
}
