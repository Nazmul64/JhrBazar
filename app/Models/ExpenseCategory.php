<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type', // 'expense', 'income', 'both'
        'color',
        'is_active',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function expenses()
    {
        return $this->hasMany(OfficeExpense::class, 'category_id');
    }

    public function accountsLedgers()
    {
        return $this->hasMany(AccountsLedger::class, 'category_id');
    }
}
