<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'address',
        'profile_image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ── Relationships ─────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    // ── Accessors — name/phone/email from user ─

    public function getNameAttribute(): string
    {
        return $this->user?->name ?? '—';
    }

    public function getEmailAttribute(): string
    {
        return $this->user?->email ?? '—';
    }

    public function getPhoneAttribute(): string
    {
        return $this->user?->phone ?? '—';
    }
}
