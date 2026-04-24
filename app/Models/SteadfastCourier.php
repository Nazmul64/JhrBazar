<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SteadfastCourier extends Model
{
    protected $table = 'steadfast_couriers';

    protected $fillable = [
        'api_key',
        'secret_key',
        'url',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
