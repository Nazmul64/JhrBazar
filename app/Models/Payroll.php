<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'month',
        'year',
        'basic_salary',
        'allowances',
        'bonuses',
        'advances_deduction',
        'total_deductions',
        'net_salary',
        'payment_status',
        'payment_date',
        'payment_method',
        'note',
        'house_rent_allowance',
        'medical_allowance',
        'conveyance_allowance',
        'provident_fund',
        'professional_tax',
        'extra_incentives'
    ];

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}
