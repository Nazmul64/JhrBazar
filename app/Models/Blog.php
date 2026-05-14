<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'blog_category_id',
        'thumbnail',
        'content',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }
}
