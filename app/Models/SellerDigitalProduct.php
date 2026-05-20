<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerDigitalProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id', 'name', 'slug', 'short_description', 'description', 
        'category_id', 'sub_category_id', 'brand_id', 'sku', 
        'buying_price', 'selling_price', 'discount_price', 'stock_quantity', 
        'thumbnail', 'additional_thumbnails', 'digital_file', 'license_keys',
        'video_type', 'video', 'meta_title', 'meta_description', 'meta_keywords', 'is_active',
        'cash_on_delivery', 'online_payment', 'is_shipping_charge'
    ];

    protected $casts = [
        'additional_thumbnails' => 'array',
        'license_keys'          => 'array',
        'is_active'             => 'boolean',
        'cash_on_delivery'      => 'boolean',
        'online_payment'        => 'boolean',
        'is_shipping_charge'    => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(\App\Models\Brand::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_id')->where('product_type', 'digital_seller')->where('status', 1);
    }

    protected static function booted(): void
    {
        static::saving(function (SellerDigitalProduct $product) {
            if (empty($product->slug)) {
                $product->slug = \Illuminate\Support\Str::slug($product->name) . '-' . strtolower(\Illuminate\Support\Str::random(5));
            }
        });
    }
}
