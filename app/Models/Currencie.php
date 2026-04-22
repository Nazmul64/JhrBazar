<?php
// app/Models/Currencie.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currencie extends Model
{
    protected $fillable = [
        'name',
        'symbol',
        'rate',
        'is_default',
        'status',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'status'     => 'boolean',
        'rate'       => 'decimal:6',
    ];

    /**
     * Scope: only active currencies
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Get the default currency
     */
    public static function getDefault()
    {
        return static::where('is_default', true)->first();
    }
}
