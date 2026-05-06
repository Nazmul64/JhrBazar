<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerVoucher extends Model
{
    protected $fillable = [
        'seller_id',
        'voucher_code',
        'discount_type',
        'discount',
        'minimum_order_amount',
        'limit_for_single_user',
        'maximum_discount_amount',
        'start_date',
        'start_time',
        'expired_date',
        'expired_time',
        'status',
    ];

    protected $casts = [
        'status'       => 'boolean',
        'start_date'   => 'date',
        'expired_date' => 'date',
    ];

    public function getFormattedDiscountAttribute(): string
    {
        if ($this->discount_type === 'percentage') {
            return $this->discount . '%';
        }
        return '$' . number_format($this->discount, 0);
    }

    public function getStartedAtAttribute(): string
    {
        return $this->start_date->format('M d, Y') . ' ' . date('h:i a', strtotime($this->start_time));
    }

    public function getExpiredAtAttribute(): string
    {
        return $this->expired_date->format('M d, Y') . ' ' . date('h:i a', strtotime($this->expired_time));
    }
}
