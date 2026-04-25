<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FraudBlacklist extends Model
{
    protected $fillable = [
        'type', 'value', 'reason', 'is_active', 'expires_at', 'created_by',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'expires_at' => 'datetime',
    ];

    // ─── Relationships ─────────────────────────────────────────────────────────

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ─── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
                     ->where(fn($q) => $q->whereNull('expires_at')
                                         ->orWhere('expires_at', '>', now()));
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    // ─── Static Helpers ────────────────────────────────────────────────────────

    public static function isBlacklisted(string $type, string $value): bool
    {
        return self::active()->byType($type)->where('value', $value)->exists();
    }

    public static function getTypeOptions(): array
    {
        return [
            'email'   => 'Email Address',
            'phone'   => 'Phone Number',
            'ip'      => 'IP Address',
            'device'  => 'Device Fingerprint',
            'country' => 'Country Code',
        ];
    }

    public static function getStats(): array
    {
        return [
            'total'  => self::count(),
            'active' => self::active()->count(),
            'email'  => self::byType('email')->count(),
            'ip'     => self::byType('ip')->count(),
            'phone'  => self::byType('phone')->count(),
        ];
    }
}
