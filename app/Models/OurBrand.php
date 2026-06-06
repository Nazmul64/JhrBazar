<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OurBrand extends Model
{
    protected $fillable = [
        'title',
        'image',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
