<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SubCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'thumbnail',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (SubCategory $subCategory) {
            if (empty($subCategory->slug)) {
                $subCategory->slug = \Illuminate\Support\Str::slug($subCategory->name);
            }
        });
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            Category::class,
            'category_sub_category',
            'sub_category_id',
            'category_id'
        );
    }
}
