<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FraudApi extends Model
{
    protected $fillable = [
        'type',
        'api_url',
        'api_key',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
