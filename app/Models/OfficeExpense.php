<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficeExpense extends Model
{
     protected $fillable = [
        'category_id', 'title', 'amount', 'expense_date',
        'paid_by', 'payment_method', 'reference', 'note',
        'attachment', 'added_by'
    ];

    protected $casts = ['expense_date' => 'date'];

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
