<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BkashPayment extends Model
{
    protected $table = 'bkash_payments';

    protected $fillable = [
        'username',
        'app_key',
        'app_secret',
        'base_url',
        'password',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
