<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionSetup extends Model
{
    protected $table = 'commission_setups';

    protected $fillable = [
        'withdraw_commission_percent',
        'min_withdraw_amount',
        'max_withdraw_amount',
        'withdraw_charge',
        'seller_withdraw_rules',
        'daily_limit',
        'verification_required',
    ];

    protected $casts = [
        'withdraw_commission_percent' => 'float',
        'min_withdraw_amount' => 'float',
        'max_withdraw_amount' => 'float',
        'withdraw_charge' => 'float',
        'seller_withdraw_rules' => 'array',
        'daily_limit' => 'float',
        'verification_required' => 'boolean',
    ];
}
