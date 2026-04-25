<?php
// app/Models/Ipblockmanage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ipblockmanage extends Model
{
    protected $table = 'ipblockmanages';

    protected $fillable = [
        'ip_address',
        'reason',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
