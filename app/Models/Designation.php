<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    use HasFactory;

    protected $fillable = ['department_id', 'name', 'grade'];

    /**
     * Department of this designation.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Employees holding this designation.
     */
    public function employees()
    {
        return $this->hasMany(User::class, 'designation_id');
    }
}
