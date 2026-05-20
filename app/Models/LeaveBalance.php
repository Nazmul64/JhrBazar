<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'casual_leave_quota',
        'casual_leave_used',
        'sick_leave_quota',
        'sick_leave_used',
        'annual_leave_quota',
        'annual_leave_used',
        'year'
    ];

    /**
     * Employee relationship.
     */
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}
