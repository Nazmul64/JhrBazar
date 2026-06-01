<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Refund extends Model
{
    protected $fillable = [
        'order_id',
        'order_item_id',
        'product_id',
        'seller_id',
        'product_name',
        'product_price',
        'quantity',
        'total_amount',
        'courier_id',
        'cancel_reason',
        'cancel_reason_description',
        'refund_status',
        'refund_date',
        'admin_note',
        'seller_note',
    ];

    protected $casts = [
        'refund_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function order(): BelongsTo
    {
        return $this->belongsTo(Pointofsalepo::class, 'order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function courier(): BelongsTo
    {
        return $this->belongsTo(Courier::class, 'courier_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(RefundItem::class, 'refund_id');
    }

    // Helper Methods
    public function isPending(): bool
    {
        return $this->refund_status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->refund_status === 'approved';
    }

    public function isProcessing(): bool
    {
        return $this->refund_status === 'processing';
    }

    public function isCompleted(): bool
    {
        return $this->refund_status === 'completed';
    }

    public function isRejected(): bool
    {
        return $this->refund_status === 'rejected';
    }

    public function getStatusBadge(): string
    {
        return match($this->refund_status) {
            'pending' => '<span class="badge bg-warning text-dark">Pending</span>',
            'approved' => '<span class="badge bg-info">Approved</span>',
            'processing' => '<span class="badge bg-primary">Processing</span>',
            'completed' => '<span class="badge bg-success">Completed</span>',
            'rejected' => '<span class="badge bg-danger">Rejected</span>',
            default => '<span class="badge bg-secondary">Unknown</span>',
        };
    }

    public function getCancelReasonDisplay(): string
    {
        return match($this->cancel_reason) {
            'admin_cancel' => 'Admin Cancelled',
            'seller_fraud' => 'Seller Fraud',
            'payment_issue' => 'Payment Issue',
            'courier_cancel' => 'Courier Cancelled',
            'customer_request' => 'Customer Request',
            'damaged_product' => 'Damaged Product',
            default => 'Other',
        };
    }
}
