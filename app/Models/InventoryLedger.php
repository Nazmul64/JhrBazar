<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryLedger extends Model
{
    protected $table = 'inventory_ledgers';

    protected $fillable = [
        'product_id',
        'order_id',
        'type',
        'quantity',
        'previous_stock',
        'current_stock',
        'note',
        'created_by',
    ];

    protected $casts = [
        'quantity'       => 'integer',
        'previous_stock' => 'integer',
        'current_stock'  => 'integer',
    ];

    // ── Relationships ──────────────────────────────────────

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Pointofsalepo::class, 'order_id');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(PosInvoice::class, 'order_id', 'pointofsalepo_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Accessors & Helpers ────────────────────────────────

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'stock_in'          => 'Stock In / Purchase',
            'sale_deduction'    => 'Order Delivered',
            'order_cancelled'   => 'Order Cancelled (Restocked)',
            'order_returned'    => 'Order Returned (Restocked)',
            'manual_adjustment' => 'Manual Adjustment',
            default             => ucfirst(str_replace('_', ' ', $this->type)),
        };
    }

    public function getTypeBadgeClassAttribute(): string
    {
        return match ($this->type) {
            'stock_in'          => 'bg-success text-white',
            'sale_deduction'    => 'bg-danger text-white',
            'order_cancelled'   => 'bg-warning text-dark',
            'order_returned'    => 'bg-info text-dark',
            'manual_adjustment' => 'bg-secondary text-white',
            default             => 'bg-light text-dark',
        };
    }

    public static function log(
        int $productId,
        string $type,
        int $quantity,
        int $currentStock,
        ?int $orderId = null,
        ?string $note = null,
        ?int $createdBy = null
    ): self {
        $prev = match ($type) {
            'stock_in', 'order_cancelled', 'order_returned' => max(0, $currentStock - $quantity),
            'sale_deduction' => $currentStock + $quantity,
            default => $currentStock,
        };

        return self::create([
            'product_id'     => $productId,
            'order_id'       => $orderId,
            'type'           => $type,
            'quantity'       => $quantity,
            'previous_stock' => $prev,
            'current_stock'  => $currentStock,
            'note'           => $note,
            'created_by'     => $createdBy,
        ]);
    }
}
