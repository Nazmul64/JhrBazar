<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'title',
        'image',
        'for_own_shop',
        'is_active',
    ];

    protected $casts = [
        'for_own_shop' => 'boolean',
        'is_active'    => 'boolean',
    ];
}
