<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TwilioGateway extends Model
{
    protected $table = 'twilio_gateways';
    protected $fillable = ['twilio_sid', 'twilio_token', 'twilio_from', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];
}
