<?php
// app/Models/Alltaxe.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alltaxe extends Model
{
    protected $table = 'alltaxes';

    protected $fillable = [
        'name',
        'percentage',
        'status',
    ];

    protected $casts = [
        'status'     => 'boolean',
        'percentage' => 'decimal:2',
    ];
}
