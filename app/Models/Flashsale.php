<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Flashsale extends Model
{
    protected $fillable = [
        'name',
        'minimum_discount',
        'start_date',
        'start_time',
        'end_date',
        'end_time',
        'description',
        'thumbnail',
        'is_active',
    ];

    protected $casts = [
        'is_active'        => 'boolean',
        'minimum_discount' => 'decimal:2',
        'start_date'       => 'date:Y-m-d',
        'end_date'         => 'date:Y-m-d',
    ];

    // ── Accessors ────────────────────────────────
    /** "2024-11-13 - 01:30:00"  (used in index table) */
    public function getStartDatetimeDisplayAttribute(): string
    {
        return $this->start_date->format('Y-m-d') . ' - ' . $this->start_time;
    }

    /** "2024-11-30 - 20:00:00" */
    public function getEndDatetimeDisplayAttribute(): string
    {
        return $this->end_date->format('Y-m-d') . ' - ' . $this->end_time;
    }

    /** "2024-11-13 01:30:00"  (used in show detail) */
    public function getStartFullAttribute(): string
    {
        return $this->start_date->format('Y-m-d') . ' ' . $this->start_time;
    }

    /** "2024-11-30 20:00:00" */
    public function getEndFullAttribute(): string
    {
        return $this->end_date->format('Y-m-d') . ' ' . $this->end_time;
    }

    // ── Relationships ────────────────────────────
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'flashsale_products')
                    ->withPivot('price', 'quantity')
                    ->withTimestamps();
    }
}
