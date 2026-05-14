<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminSupport extends Model
{
    protected $fillable = [
        'messenger_url',
        'whatsapp_number',
        'phone_number',
        'is_active',
    ];
}
