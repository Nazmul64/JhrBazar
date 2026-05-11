<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipLogo extends Model
{
    protected $fillable = ['image', 'name', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
