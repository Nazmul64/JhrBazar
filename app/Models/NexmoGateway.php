<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NexmoGateway extends Model
{
    protected $table = 'nexmo_gateways';
    protected $fillable = ['nexmo_key', 'nexmo_secret', 'nexmo_from', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];
}
