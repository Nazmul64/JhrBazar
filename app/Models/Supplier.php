<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'profile_image',
        'status',
        'is_active',
        'seller_id',
        'user_id',
    ];

    protected $casts = [
        'status'    => 'boolean',
        'is_active' => 'boolean',
    ];

    // ── Relationships ─────────────────────────

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    // ── Accessors — name/phone/email ──────────

    public function getNameAttribute(): string
    {
        return $this->attributes['name'] ?? ($this->user?->name ?? '—');
    }

    public function getEmailAttribute(): string
    {
        return $this->attributes['email'] ?? ($this->user?->email ?? '—');
    }

    public function getPhoneAttribute(): string
    {
        return $this->attributes['phone'] ?? ($this->user?->phone ?? '—');
    }
}
