<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SociallinkList extends Model
{
    protected $fillable = [
        'name',
        'platform',
        'link',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
