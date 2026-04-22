<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aiprompt extends Model
{
    protected $table = 'aiprompts';

    protected $fillable = [
        'product_description',
        'page_description',
        'blog_description',
    ];
}
