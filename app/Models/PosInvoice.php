<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosInvoice extends Model
{
    protected $table = 'pos_invoices';

    protected $fillable = [
        'invoice_number',
        'seller_id',
        'pointofsalepo_id',
        'customer_id',
        'items',
        'tax_breakdown',
        'sub_total',
        'discount',
        'tax_amount',
        'delivery_charge',
        'grand_total',
        'received_amount',
        'change_amount',
        'payment_method',
        'coupon_code',
        'note',
    ];

    protected $casts = [
        'items'           => 'array',
        'tax_breakdown'   => 'array',
        'sub_total'       => 'decimal:2',
        'discount'        => 'decimal:2',
        'tax_amount'      => 'decimal:2',
        'delivery_charge' => 'decimal:2',
        'grand_total'     => 'decimal:2',
        'received_amount' => 'decimal:2',
        'change_amount'   => 'decimal:2',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Pointofsalepo::class, 'pointofsalepo_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    // ── Static Helpers ─────────────────────────────────────────────────────

    public static function generateInvoiceNumber(): string
    {
        do {
            $number = str_pad(mt_rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
        } while (static::where('invoice_number', $number)->exists());

        return $number;
    }

    // ── Accessors ──────────────────────────────────────────────────────────

    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'card'   => 'Card Payment',
            'mobile' => 'Mobile Payment',
            default  => 'Cash Payment',
        };
    }

    /** Total number of distinct product lines on the invoice */
    public function getItemsCountAttribute(): int
    {
        return is_array($this->items) ? count($this->items) : 0;
    }

    /** Total units ordered across all lines */
    public function getTotalQtyAttribute(): int
    {
        if (!is_array($this->items)) return 0;
        return array_sum(array_column($this->items, 'qty'));
    }
}
