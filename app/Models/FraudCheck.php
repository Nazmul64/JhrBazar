<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class FraudCheck extends Model
{
    protected $fillable = [
        'check_id', 'type', 'input_value', 'status', 'risk_score', 'risk_level',
        'customer_name', 'customer_email', 'customer_phone', 'ip_address',
        'country', 'city', 'vpn_detected', 'proxy_detected', 'tor_detected',
        'email_valid', 'email_disposable', 'email_domain', 'email_domain_age',
        'social_profiles', 'phone_valid', 'phone_carrier', 'phone_type', 'phone_country',
        'transaction_amount', 'transaction_currency', 'device_type', 'browser', 'os',
        'device_fingerprint', 'triggered_rules', 'flags', 'notes',
        'reviewed_by', 'reviewed_at', 'created_by',
    ];

    protected $casts = [
        'vpn_detected'     => 'boolean',
        'proxy_detected'   => 'boolean',
        'tor_detected'     => 'boolean',
        'email_valid'      => 'boolean',
        'email_disposable' => 'boolean',
        'phone_valid'      => 'boolean',
        'social_profiles'  => 'array',
        'triggered_rules'  => 'array',
        'flags'            => 'array',
        'risk_score'       => 'float',
        'reviewed_at'      => 'datetime',
    ];

    // ─── Relationships ─────────────────────────────────────────────────────────

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(FraudAlert::class);
    }

    // ─── Scopes ────────────────────────────────────────────────────────────────

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeByRiskLevel(Builder $query, string $level): Builder
    {
        return $query->where('risk_level', $level);
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeHighRisk(Builder $query): Builder
    {
        return $query->whereIn('risk_level', ['high', 'critical']);
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function (Builder $q) use ($term) {
            $q->where('check_id', 'like', "%{$term}%")
              ->orWhere('customer_name', 'like', "%{$term}%")
              ->orWhere('customer_email', 'like', "%{$term}%")
              ->orWhere('input_value', 'like', "%{$term}%")
              ->orWhere('ip_address', 'like', "%{$term}%");
        });
    }

    // ─── Accessors ─────────────────────────────────────────────────────────────

    public function getRiskBadgeColorAttribute(): string
    {
        return match ($this->risk_level) {
            'low'      => 'success',
            'medium'   => 'warning',
            'high'     => 'danger',
            'critical' => 'dark-red',
            default    => 'secondary',
        };
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'approved' => 'success',
            'review'   => 'warning',
            'declined' => 'danger',
            'pending'  => 'info',
            default    => 'secondary',
        };
    }

    // ─── Static Helpers ────────────────────────────────────────────────────────

    public static function generateCheckId(): string
    {
        $last   = self::latest('id')->value('check_id');
        $number = $last ? ((int) substr($last, 3)) + 1 : 1;
        return 'FC-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    public static function getRiskLevel(float $score): string
    {
        return match (true) {
            $score >= 80 => 'critical',
            $score >= 60 => 'high',
            $score >= 40 => 'medium',
            default      => 'low',
        };
    }

    public static function getTypeOptions(): array
    {
        return [
            'identity'    => 'Identity',
            'email'       => 'Email',
            'phone'       => 'Phone',
            'ip'          => 'IP Address',
            'transaction' => 'Transaction',
        ];
    }

    // ─── Stats ─────────────────────────────────────────────────────────────────

    public static function getStats(): array
    {
        return [
            'total'     => self::count(),
            'approved'  => self::byStatus('approved')->count(),
            'review'    => self::byStatus('review')->count(),
            'declined'  => self::byStatus('declined')->count(),
            'pending'   => self::byStatus('pending')->count(),
            'high_risk' => self::highRisk()->count(),
            'today'     => self::whereDate('created_at', today())->count(),
            'avg_score' => round((float) self::avg('risk_score'), 1),
        ];
    }
}
