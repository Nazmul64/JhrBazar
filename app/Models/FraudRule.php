<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FraudRule extends Model
{
    protected $fillable = [
        'name', 'code', 'description', 'category',
        'condition_field', 'condition_operator', 'condition_value',
        'action', 'score_impact', 'is_active', 'priority',
        'triggered_count', 'created_by',
    ];

    protected $casts = [
        'is_active'       => 'boolean',
        'score_impact'    => 'integer',
        'priority'        => 'integer',
        'triggered_count' => 'integer',
    ];

    // ─── Relationships ─────────────────────────────────────────────────────────

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ─── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    // ─── Static Helpers ────────────────────────────────────────────────────────

    public static function generateCode(): string
    {
        $last   = self::latest('id')->value('code');
        $number = $last ? ((int) substr($last, 5)) + 1 : 1;
        return 'RULE_' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    public static function getCategoryOptions(): array
    {
        return [
            'identity'    => 'Identity',
            'transaction' => 'Transaction',
            'device'      => 'Device',
            'network'     => 'Network',
            'behavioral'  => 'Behavioral',
        ];
    }

    public static function getOperatorOptions(): array
    {
        return [
            'equals'       => 'Equals (==)',
            'not_equals'   => 'Not Equals (!=)',
            'contains'     => 'Contains',
            'greater_than' => 'Greater Than (>)',
            'less_than'    => 'Less Than (<)',
            'in'           => 'In List (comma separated)',
            'regex'        => 'Regex Match',
            'is_true'      => 'Is True',
            'is_false'     => 'Is False',
        ];
    }

    public static function getActionOptions(): array
    {
        return [
            'flag'    => 'Flag for Review',
            'review'  => 'Set to Review',
            'decline' => 'Auto Decline',
            'approve' => 'Auto Approve',
        ];
    }

    public static function getStats(): array
    {
        return [
            'total'  => self::count(),
            'active' => self::active()->count(),
        ];
    }
}
