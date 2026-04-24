<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'phone_number',
        'whatsapp_number',
        'messenger_link',
        'email_address',
    ];
}
