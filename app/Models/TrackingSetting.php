<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackingSetting extends Model
{
    protected $table = 'tracking_settings';

    protected $fillable = [
        'is_active',
        'master_tracking_status',
        'gtm_id',
        'gtm_enabled',
        'gtm_status',
        'ga4_measurement_id',
        'ga4_id',
        'ga4_enabled',
        'ga4_status',
        'fb_pixel_id',
        'fb_pixel_enabled',
        'fb_access_token',
        'facebook_pixel_id',
        'facebook_pixel_status',
        'tiktok_pixel_id',
        'tiktok_pixel_enabled',
        'tiktok_pixel_status',
        'datalayer_events',
        'cookie_lifetime_days',
        'track_utm',
        'track_referrer',
        'track_click_ids',
        'track_direct',
        'custom_head_script',
        'custom_body_script',
        'enable_visitor_cookies',
        'enable_utm_tracking',
        'enable_referrer_tracking',
        'enable_device_tracking',
        'enable_country_detection',
        'enable_custom_events_logging',
        'enable_admin_reports_dashboard',
    ];

    protected $casts = [
        'is_active'                       => 'boolean',
        'master_tracking_status'          => 'boolean',
        'gtm_enabled'                     => 'boolean',
        'gtm_status'                      => 'boolean',
        'ga4_enabled'                     => 'boolean',
        'ga4_status'                      => 'boolean',
        'fb_pixel_enabled'                => 'boolean',
        'facebook_pixel_status'           => 'boolean',
        'tiktok_pixel_enabled'            => 'boolean',
        'tiktok_pixel_status'             => 'boolean',
        'datalayer_events'                => 'array',
        'cookie_lifetime_days'            => 'integer',
        'track_utm'                       => 'boolean',
        'track_referrer'                  => 'boolean',
        'track_click_ids'                 => 'boolean',
        'track_direct'                    => 'boolean',
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
