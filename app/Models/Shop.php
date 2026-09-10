<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'business_name',
        'business_type',
        'address',
        'city',
        'postal_code',
        'url',
        'categories',
        'logo',
        'banner',
        'description',
        'latitude',
        'longitude',
        'status',
        'opening_time',
        'closing_time',
        'estimated_delivery',
        'order_prefix',
        'min_order_amount',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    // ── Image URL accessors ─────────────────────────────────────────────────
    // Usage: {{ $shop->logo_url }}  {{ $shop->banner_url }}

    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo) return null;
        if (str_starts_with($this->logo, 'http')) {
            return $this->logo;
        }
        return asset(ltrim($this->logo, '/'));
    }

    public function getBannerUrlAttribute(): ?string
    {
        if (!$this->banner) return null;
        if (str_starts_with($this->banner, 'http')) {
            return $this->banner;
        }
        return asset(ltrim($this->banner, '/'));
    }

    // ── Dynamic counts ──────────────────────────────────────────────────────
    public function getProductsCountAttribute(): int
    {
        $physicalCount = \App\Models\SellerProduct::where('seller_id', $this->user_id)->count();
        $digitalCount = \App\Models\SellerDigitalProduct::where('seller_id', $this->user_id)->count();
        return $physicalCount + $digitalCount;
    }

    public function getOrdersCountAttribute(): int
    {
        return \App\Models\Pointofsalepo::where('seller_id', $this->user_id)->count();
    }

    // ── Relations ───────────────────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sellerProducts()
    {
        return $this->hasMany(SellerProduct::class, 'seller_id', 'user_id');
    }
}
