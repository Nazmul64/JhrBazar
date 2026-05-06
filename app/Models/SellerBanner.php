<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerBanner extends Model
{
    protected $fillable = [
        'seller_id',
        'title',
        'image',
        'link',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'start_date'  => 'date:Y-m-d',
        'end_date'    => 'date:Y-m-d',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}
