<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SalaryAdvance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'amount',
        'advance_date',
        'deduction_type',
        'monthly_deduction_amount',
        'status',
        'paid_status',
        'reason'
    ];

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}
