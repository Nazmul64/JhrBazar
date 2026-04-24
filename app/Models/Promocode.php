<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promocode extends Model
{
    protected $fillable = [
        'shop_ids',
        'coupon_code',
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

    /**
     * Discount formatted display করে (amount বা percentage)
     */
    public function getFormattedDiscountAttribute(): string
    {
        if ($this->discount_type === 'percentage') {
            return $this->discount . '%';
        }
        return '$' . number_format($this->discount, 0);
    }

    /**
     * Formatted started_at display
     */
    public function getStartedAtAttribute(): string
    {
        return $this->start_date->format('M d, Y') . ' ' . date('h:i a', strtotime($this->start_time));
    }

    /**
     * Formatted expired_at display
     */
    public function getExpiredAtAttribute(): string
    {
        return $this->expired_date->format('M d, Y') . ' ' . date('h:i a', strtotime($this->expired_time));
    }

    /**
     * shop_ids JSON decode করে array হিসেবে return করে
     */
    public function getShopIdsArrayAttribute(): array
    {
        if (empty($this->shop_ids)) {
            return [];
        }
        $decoded = json_decode($this->shop_ids, true);
        return is_array($decoded) ? $decoded : explode(',', $this->shop_ids);
    }
}
