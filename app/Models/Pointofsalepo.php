<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;

class Pointofsalepo extends Model
{
    protected $table = 'pointofsalepos';

    protected $fillable = [
        'customer_id',
        'items',
        'sub_total',
        'discount',
        'tax_amount',
        'grand_total',
        'payment_method',
        'coupon_code',
        'note',
        'status',
    ];

    protected $casts = [
        'items'        => 'array',
        'sub_total'    => 'decimal:2',
        'discount'     => 'decimal:2',
        'tax_amount'   => 'decimal:2',
        'grand_total'  => 'decimal:2',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(PosInvoice::class, 'pointofsalepo_id');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', 'draft');
    }

    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', 'cancelled');
    }

    // ── Accessors ──────────────────────────────────────────────────────────

    /** Total number of line items in the order */
    public function getItemsCountAttribute(): int
    {
        return is_array($this->items) ? count($this->items) : 0;
    }

    /** Total units across all line items */
    public function getTotalQtyAttribute(): int
    {
        if (!is_array($this->items)) return 0;
        return array_sum(array_column($this->items, 'qty'));
    }

    /** Human-readable status label */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'completed' => 'Completed',
            'draft'     => 'Draft',
            'cancelled' => 'Cancelled',
            default     => ucfirst($this->status),
        };
    }

    /** Bootstrap badge colour for status */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'completed' => 'success',
            'draft'     => 'warning',
            'cancelled' => 'danger',
            default     => 'secondary',
        };
    }

    /** Human-readable payment method */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'card'   => 'Card Payment',
            'mobile' => 'Mobile Payment',
            default  => 'Cash Payment',
        };
    }
}
