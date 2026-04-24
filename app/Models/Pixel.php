<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pixel extends Model
{
    use HasFactory;

    protected $fillable = [
        'pixels_id',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
