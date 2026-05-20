<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'date',
        'status',
        'note',
        'clock_in',
        'clock_out',
        'working_hours',
        'late_minutes',
        'device_ip',
        'location_coordinates',
        'shift_name'
    ];

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}
