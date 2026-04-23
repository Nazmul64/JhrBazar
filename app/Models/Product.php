<?php
// app/Models/Product.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'name', 'short_description', 'description',
        'category_id', 'sub_category_id', 'brand_id',
        'color', 'unit', 'size', 'sku', 'barcode',
        'buying_price', 'selling_price', 'discount_price',
        'stock_quantity', 'thumbnail', 'gallery_images',
        'video_type', 'video',
        'meta_title', 'meta_description', 'meta_keywords',
        'is_active', 'rating',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'gallery_images' => 'array',
        'buying_price'   => 'decimal:2',
        'selling_price'  => 'decimal:2',
        'discount_price' => 'decimal:2',
    ];

    // ── Relationships ──────────────────────────────────────
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    // ── Barcode display value ──────────────────────────────
    // Returns barcode if set, otherwise falls back to sku
    public function getBarcodeDisplayAttribute(): string
    {
        return $this->barcode ?? $this->sku;
    }

    // ── Auto-generate barcode on creating ─────────────────
    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            if (empty($product->barcode)) {
                // Generate a unique 8-digit numeric barcode
                do {
                    $code = (string) random_int(10000000, 99999999);
                } while (static::where('barcode', $code)->exists());

                $product->barcode = $code;
            }
        });
    }
}
