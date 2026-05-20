<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DigitalProduct extends Model
{
    use HasFactory;

    protected $guarded = [];

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
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_id')->where('product_type', 'digital_admin')->where('status', 1);
    }

    protected static function booted(): void
    {
        static::saving(function (DigitalProduct $product) {
            if (empty($product->slug)) {
                $product->slug = \Illuminate\Support\Str::slug($product->name) . '-' . strtolower(\Illuminate\Support\Str::random(5));
            }
        });
    }
}
