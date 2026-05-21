<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'status', // e.g., 'active', 'inactive'
    ];

    /**
     * Scope a query to only include active couriers.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
