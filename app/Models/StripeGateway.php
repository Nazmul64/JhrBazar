<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StripeGateway extends Model
{
    protected $table = 'stripe_gateways';

    protected $fillable = [
        'mode',
        'secret_key',
        'published_key',
        'title',
        'logo',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
