<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_category_id',
        'name',
        'title',
        'slug',
        'description',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
        'page_category_id' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(PageCategory::class, 'page_category_id');
    }

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
