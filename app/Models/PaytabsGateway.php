<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaytabsGateway extends Model
{
    protected $table = 'paytabs_gateways';

    protected $fillable = [
        'mode',
        'base_url',
        'currency',
        'profile_id',
        'server_key',
        'title',
        'logo',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
