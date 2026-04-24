<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AamarpayGateway extends Model
{
    protected $table = 'aamarpay_gateways';

    protected $fillable = [
        'mode',
        'store_id',
        'signature_key',
        'title',
        'logo',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
