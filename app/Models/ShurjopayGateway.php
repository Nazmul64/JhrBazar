<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShurjopayGateway extends Model
{
    protected $table = 'shurjopay_gateways';

    protected $fillable = [
        'username',
        'prefix',
        'success_url',
        'return_url',
        'base_url',
        'password',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
