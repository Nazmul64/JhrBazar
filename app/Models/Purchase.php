<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Purchase extends Model
{
    protected $fillable = [
        'seller_id',
        'supplier_id',
        'purchase_name',
        'invoice_no',
        'purchase_date',
        'total_amount',
        'paid_amount',
        'due_amount',
        'payment_method',
        'payment_status',
        'status',
        'note',
        'purchase_slip',
        'created_by',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'total_amount'  => 'decimal:2',
        'paid_amount'   => 'decimal:2',
        'due_amount'    => 'decimal:2',
    ];

    // ── Relations ──────────────────────────────

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Helper: Auto Invoice Number ────────────
    public static function generateInvoiceNumber(): string
    {
        $today = date('Ymd');
        $last  = static::where('invoice_no', 'like', "PUR-{$today}-%")->latest('id')->first();
        $seq   = 1;

        if ($last && preg_match('/PUR-\d{8}-(\d+)/', $last->invoice_no, $matches)) {
            $seq = (int) $matches[1] + 1;
        }

        return 'PUR-' . $today . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
