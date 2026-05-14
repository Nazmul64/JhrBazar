<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatusLog extends Model
{
    protected $fillable = [
        'order_id',
        'previous_status',
        'current_status',
        'changed_by',
        'note',
    ];

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function order()
    {
        return $this->belongsTo(Pointofsalepo::class, 'order_id');
    }
}
