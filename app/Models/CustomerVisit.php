<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerVisit extends Model
{
    protected $table = 'customer_visits';

    protected $fillable = [
        'customer_name',
        'phone_number',
        'ip_address',
        'page_visited',
        'user_agent',
        'visited_at',
        'is_read',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
        'is_read'    => 'boolean',
    ];
}
