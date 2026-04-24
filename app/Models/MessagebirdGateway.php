<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessagebirdGateway extends Model
{
    protected $table = 'messagebird_gateways';
    protected $fillable = ['api_key', 'from', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];
}
