<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Verificatiootpsettings extends Model
{
    protected $table = 'verificatiootpsettings';

    protected $fillable = [
        'customer_registration_otp_verify',
        'must_verify_account_on_order_placement',
        'register_otp_send_method',
        'forget_password_otp_send_method',
        'registration_phone_required',
        'min_phone_length',
        'max_phone_length',
    ];

    protected $casts = [
        'customer_registration_otp_verify'       => 'boolean',
        'must_verify_account_on_order_placement'  => 'boolean',
        'registration_phone_required'             => 'boolean',
    ];
}
