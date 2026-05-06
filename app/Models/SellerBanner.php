<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerBanner extends Model
{
    protected $fillable = [
        'seller_id',
        'title',
        'image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
