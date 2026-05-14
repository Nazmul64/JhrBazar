<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerTransaction extends Model
{
    protected $fillable = [
        'seller_id',
        'transaction_id',
        'type',
        'amount',
        'commission',
        'net_amount',
        'status',
        'description',
    ];

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}
