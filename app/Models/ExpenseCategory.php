<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    protected $fillable = ['name', 'color', 'is_active'];

    public function expenses()
    {
        return $this->hasMany(OfficeExpense::class, 'category_id');
    }
}
