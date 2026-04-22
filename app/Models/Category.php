<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'thumbnail',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function subCategories(): BelongsToMany
    {
        return $this->belongsToMany(
            SubCategory::class,
            'category_sub_category',
            'category_id',
            'sub_category_id'
        );
    }
}
