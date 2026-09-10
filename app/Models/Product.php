<?php
// app/Models/Product.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'name', 'slug', 'short_description', 'description',
        'category_id', 'sub_category_id', 'brand_id',
        'color', 'unit', 'size', 'sku', 'barcode',
        'buying_price', 'selling_price', 'discount_price',
        'stock_quantity', 'is_unlimited', 'low_stock_threshold', 'total_in', 'total_sold',
        'thumbnail', 'gallery_images',
        'video_type', 'video',
        'meta_title', 'meta_description', 'meta_keywords',
        'is_active', 'rating',
        'is_new_arrival', 'is_best_seller', 'is_hot_product', 'is_flash_sale', 'is_just_for_you', 'is_popular',
        'cash_on_delivery', 'online_payment', 'is_shipping_charge',
        'frontend_sections',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_unlimited' => 'boolean',
        'low_stock_threshold' => 'integer',
        'total_in' => 'integer',
        'total_sold' => 'integer',
        'stock_quantity' => 'integer',
        'is_new_arrival' => 'boolean',
        'is_best_seller' => 'boolean',
        'is_hot_product' => 'boolean',
        'is_flash_sale' => 'boolean',
        'is_just_for_you' => 'boolean',
        'is_popular' => 'boolean',
        'cash_on_delivery' => 'boolean',
        'online_payment' => 'boolean',
        'is_shipping_charge' => 'boolean',
        'gallery_images' => 'array',
        'frontend_sections' => 'array',
        'buying_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
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

    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_id')->where('product_type', 'admin')->where('status', 1);
    }

    public function inventoryLedgers()
    {
        return $this->hasMany(InventoryLedger::class, 'product_id')->latest();
    }

    // ── Stock Status Helpers ───────────────────────────────
    public function getStockStatusAttribute(): string
    {
        if ($this->is_unlimited) {
            return 'unlimited';
        }
        if ($this->stock_quantity <= 0) {
            return 'out_of_stock';
        }
        $threshold = $this->low_stock_threshold > 0 ? $this->low_stock_threshold : 3;
        if ($this->stock_quantity <= $threshold) {
            return 'low_stock';
        }
        return 'in_stock';
    }

    public function isLowStock(): bool
    {
        if ($this->is_unlimited) return false;
        $threshold = $this->low_stock_threshold > 0 ? $this->low_stock_threshold : 3;
        return $this->stock_quantity > 0 && $this->stock_quantity <= $threshold;
    }

    public function isOutOfStock(): bool
    {
        if ($this->is_unlimited) return false;
        return $this->stock_quantity <= 0;
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
        static::saving(function (Product $product) {
            if (empty($product->slug)) {
                $product->slug = \Illuminate\Support\Str::slug($product->name) . '-' . strtolower(\Illuminate\Support\Str::random(5));
            }
        });

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
