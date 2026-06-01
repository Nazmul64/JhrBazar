<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Landingpage extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'product_id',
        'additional_product_ids',
        'style_template',
        'media_type',
        'image',
        'feature_image',
        'video_url',
        'checkout_image',
        'bg_color',
        'button_color',
        'reviews',
        'short_description',
        'description',
        'sections',
        'status',
        'is_template',
    ];

    protected $casts = [
        'reviews'  => 'array',
        'sections' => 'array',
        'additional_product_ids' => 'array',
        'status'   => 'boolean',
        'is_template' => 'boolean',
    ];

    // ── Auto-generate slug ───────────────────────────────────
    protected static function booted(): void
    {
        static::creating(function ($page) {
            if (empty($page->slug)) {
                $page->slug = Str::slug($page->title) . '-' . Str::random(6);
            }
        });
    }

    // ── Relationships ───────────────────────────────────────────
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
