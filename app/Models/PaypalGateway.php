<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaypalGateway extends Model
{
    protected $table = 'paypal_gateways';

    protected $fillable = [
        'mode',
        'client_id',
        'client_secret',
        'title',
        'logo',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
