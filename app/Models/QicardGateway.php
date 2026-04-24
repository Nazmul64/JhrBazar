<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QicardGateway extends Model
{
    protected $table = 'qicard_gateways';

    protected $fillable = [
        'mode',
        'currency',
        'password',
        'username',
        'terminal_id',
        'title',
        'logo',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
