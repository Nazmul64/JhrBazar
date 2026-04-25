<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Landingpage extends Model
{
    protected $fillable = [
        'title',
        'product_id',
        'media_type',
        'image',
        'reviews',
        'short_description',
        'description',
        'status',
    ];

    protected $casts = [
        'reviews' => 'array',
        'status'  => 'boolean',
    ];

    // ── Relationships ───────────────────────────────────────────
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
