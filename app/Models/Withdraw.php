<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdraw extends Model
{
    protected $fillable = [
        'seller_id',
        'bank_id',
        'account_name',
        'account_number',
        'contact_number',
        'amount',
        'status',
        'seller_note',
        'admin_note',
        'paid_at',  
    ];

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }
}
