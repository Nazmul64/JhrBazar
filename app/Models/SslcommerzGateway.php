<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SslcommerzGateway extends Model
{
    protected $table = 'sslcommerz_gateways';

    protected $fillable = [
        'mode',
        'store_id',
        'store_password',
        'title',
        'logo',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
