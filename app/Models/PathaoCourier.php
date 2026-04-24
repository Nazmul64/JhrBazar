<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PathaoCourier extends Model
{
    protected $table = 'pathao_couriers';

    protected $fillable = [
        'base_url',
        'client_id',
        'client_secret',
        'username',
        'password',
        'grant_type',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
