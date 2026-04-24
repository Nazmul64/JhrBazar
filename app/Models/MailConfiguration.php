<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailConfiguration extends Model
{
    protected $table = 'mail_configurations';

    protected $fillable = [
        'mail_mailer',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'mail_from_address',
        'mail_from_name',
    ];

    protected $hidden = [
        'mail_password',
    ];

    protected $casts = [
        'mail_port' => 'integer',
    ];
}
