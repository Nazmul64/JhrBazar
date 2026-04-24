<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsGateway extends Model
{
    protected $table = 'sms_gateways';

    protected $fillable = [
        'url',
        'api_key',
        'sender_id',
        'status',
        'order_confirm',
        'forgot_password',
        'password_generator',
    ];

    protected $casts = [
        'status' => 'boolean',
        'order_confirm' => 'boolean',
        'forgot_password' => 'boolean',
        'password_generator' => 'boolean',
    ];
}
