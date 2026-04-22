<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Purchase extends Model
{
    protected $fillable = [
        'supplier_id',
        'invoice_no',
        'total_amount',
        'paid_amount',
        'due_amount',
        'payment_status',
        'status',
        'note',
    ];

    // ── Relations ──────────────────────────────

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
