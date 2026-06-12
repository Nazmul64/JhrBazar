<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FirebaseNotification extends Model
{
    protected $table = 'firebase_notifications';

    protected $fillable = [
        'user_id',
        'title',
        'body',
        'image_url',
        'status',
        'response',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
