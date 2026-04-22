<?php
// app/Models/BusinessSetting.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessSetting extends Model
{
    protected $fillable = [
        // Basic Info
        'company_name',
        'company_email',
        'company_phone',
        'business_model',
        'currency_position',
        'timezone',
        'return_order_within_days',
        'cash_on_delivery',
        'online_payment',

        // Shops
        'commission_enabled',
        'subscription_enabled',
        'commission',
        'commission_type',
        'commission_charge',
        'pos_in_shop_panel',
        'shop_registration',
        'need_product_approval',
        'update_product_approval',

        // Withdraw
        'min_withdraw_amount',
        'max_withdraw_amount',
        'min_day_withdraw_request',
    ];

    protected $casts = [
        'cash_on_delivery'        => 'boolean',
        'online_payment'          => 'boolean',
        'commission_enabled'      => 'boolean',
        'subscription_enabled'    => 'boolean',
        'pos_in_shop_panel'       => 'boolean',
        'shop_registration'       => 'boolean',
        'need_product_approval'   => 'boolean',
        'update_product_approval' => 'boolean',
    ];
}
