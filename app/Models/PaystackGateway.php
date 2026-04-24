<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaystackGateway extends Model
{
    protected $table = 'paystack_gateways';

    protected $fillable = [
        'mode',
        'public_key',
        'secret_key',
        'merchant_email',
        'title',
        'logo',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
