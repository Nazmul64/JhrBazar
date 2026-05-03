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
        'address',
        'logo',
        'banner',
        'description',
        'latitude',
        'longitude',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    // ── Image URL accessors ─────────────────────────────────────────────────
    // Usage: {{ $shop->logo_url }}  {{ $shop->banner_url }}

    public function getLogoUrlAttribute(): ?string
    {
        if ($this->logo && file_exists(public_path($this->logo))) {
            return asset($this->logo);
        }
        return null;
    }

    public function getBannerUrlAttribute(): ?string
    {
        if ($this->banner && file_exists(public_path($this->banner))) {
            return asset($this->banner);
        }
        return null;
    }

    // ── Hard-coded counts (replace later when products/orders tables exist) ─
    public function getProductsCountAttribute(): int
    {
        return 0; // TODO: return $this->products()->count();
    }

    public function getOrdersCountAttribute(): int
    {
        return 0; // TODO: return orders count via product->shop
    }

    // ── Relations ───────────────────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
