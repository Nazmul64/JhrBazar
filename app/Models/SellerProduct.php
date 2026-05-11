<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerProduct extends Model
{
    protected $fillable = [
        'seller_id', 'name', 'short_description', 'description',
        'category_id', 'sub_category_id', 'brand_id',
        'color', 'unit', 'size', 'sku', 'barcode',
        'buying_price', 'selling_price', 'discount_price',
        'stock_quantity', 'thumbnail', 'gallery_images',
        'video_type', 'video',
        'meta_title', 'meta_description', 'meta_keywords',
        'is_active',
        'is_new_arrival', 'is_best_seller', 'is_hot_product', 'is_flash_sale', 'is_just_for_you', 'is_popular',
        'cash_on_delivery', 'online_payment',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'is_new_arrival' => 'boolean',
        'is_best_seller' => 'boolean',
        'is_hot_product' => 'boolean',
        'is_flash_sale'  => 'boolean',
        'is_just_for_you'=> 'boolean',
        'is_popular'     => 'boolean',
        'cash_on_delivery'=> 'boolean',
        'online_payment'  => 'boolean',
        'buying_price'   => 'decimal:2',
        'selling_price'  => 'decimal:2',
        'discount_price' => 'decimal:2',
        'gallery_images' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(\App\Models\SubCategory::class);
    }

    public function brand()
    {
        return $this->belongsTo(\App\Models\Brand::class);
    }

    public function getBarcodeDisplayAttribute(): string
    {
        return $this->barcode ?? $this->sku ?? '';
    }

    protected static function booted(): void
    {
        static::creating(function (SellerProduct $product) {
            if (empty($product->barcode)) {
                do {
                    $code = (string) random_int(10000000, 99999999);
                } while (static::where('barcode', $code)->exists());

                $product->barcode = $code;
            }
        });
    }
}
