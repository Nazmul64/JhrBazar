<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BkashGateway extends Model
{
    protected $table = 'bkash_gateways';

    protected $fillable = [
        'mode',
        'app_key',
        'password',
        'username',
        'app_secret_key',
        'title',
        'logo',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
