<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FirebaseSetting extends Model
{
    protected $table = 'firebase_settings';

    protected $fillable = [
        'project_id',
        'api_key',
        'service_account_json',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
