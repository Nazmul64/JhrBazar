<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'title',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    // Scope for active pages
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    // Accessor for status label
    public function getStatusLabelAttribute(): string
    {
        return $this->status == 1 ? 'Active' : 'Inactive';
    }
}
