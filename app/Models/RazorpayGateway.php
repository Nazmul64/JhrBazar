<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RazorpayGateway extends Model
{
    protected $table = 'razorpay_gateways';

    protected $fillable = [
        'mode',
        'key',
        'secret',
        'title',
        'logo',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
