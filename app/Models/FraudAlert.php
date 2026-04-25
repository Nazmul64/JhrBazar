<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class FraudAlert extends Model
{
    protected $fillable = [
        'alert_id', 'fraud_check_id', 'severity', 'type',
        'title', 'description', 'status', 'assigned_to',
        'resolved_at', 'resolution_note',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    // ─── Relationships ─────────────────────────────────────────────────────────

    public function fraudCheck(): BelongsTo
    {
        return $this->belongsTo(FraudCheck::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // ─── Scopes ────────────────────────────────────────────────────────────────

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', 'open');
    }

    public function scopeBySeverity(Builder $query, string $severity): Builder
    {
        return $query->where('severity', $severity);
    }

    // ─── Static ────────────────────────────────────────────────────────────────

    public static function generateAlertId(): string
    {
        $last   = self::latest('id')->value('alert_id');
        $number = $last ? ((int) substr($last, 4)) + 1 : 1;
        return 'ALT-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    public static function getSeverityOptions(): array
    {
        return [
            'info'     => 'Info',
            'warning'  => 'Warning',
            'critical' => 'Critical',
        ];
    }

    public static function getStatusOptions(): array
    {
        return [
            'open'           => 'Open',
            'investigating'  => 'Investigating',
            'resolved'       => 'Resolved',
            'false_positive' => 'False Positive',
        ];
    }

    public static function getStats(): array
    {
        return [
            'total'    => self::count(),
            'open'     => self::open()->count(),
            'critical' => self::bySeverity('critical')->count(),
            'warning'  => self::bySeverity('warning')->count(),
        ];
    }
}
