<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CyberAlert extends Model
{
    protected $table = 'cyber_alerts';

    protected $fillable = [
        'ip_address',
        'wifi_provider',
        'location',
        'lat',
        'lon',
        'device_agent',
        'device_type',
        'attempted_at',
    ];

    protected $casts = [
        'attempted_at' => 'datetime',
        'lat'          => 'double',
        'lon'          => 'double',
    ];
}
