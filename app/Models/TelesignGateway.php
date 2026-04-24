<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelesignGateway extends Model
{
    protected $table = 'telesign_gateways';
    protected $fillable = ['customer_id', 'api_key', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];
}
