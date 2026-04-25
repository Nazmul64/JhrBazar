<?php
// app/Models/Duplicateordersetting.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Duplicateordersetting extends Model
{
    protected $table = 'duplicateordersettings';

    protected $fillable = [
        'allow_duplicate_orders',
        'duplicate_check_type',
        'duplicate_time_limit',
        'duplicate_check_message',
    ];

    protected $casts = [
        'allow_duplicate_orders' => 'boolean',
        'duplicate_time_limit'   => 'integer',
    ];

    /**
     * Always return the single settings row.
     * Creates it automatically if not exists.
     */
    public static function instance(): static
    {
        return static::firstOrCreate([], [
            'allow_duplicate_orders'  => false,
            'duplicate_check_type'    => 'Product + IP + Phone',
            'duplicate_time_limit'    => 1,
            'duplicate_check_message' => 'Duplicate order detected.',
        ]);
    }
}
