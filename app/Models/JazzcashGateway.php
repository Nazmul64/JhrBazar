<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JazzcashGateway extends Model
{
    protected $table = 'jazzcash_gateways';

    protected $fillable = [
        'mode',
        'base_url',
        'password',
        'merchant_id',
        'integrity_salt',
        'title',
        'logo',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
