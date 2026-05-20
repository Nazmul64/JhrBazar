<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    /**
     * Designations in this department.
     */
    public function designations()
    {
        return $this->hasMany(Designation::class);
    }

    /**
     * Employees in this department.
     */
    public function employees()
    {
        return $this->hasMany(User::class, 'department_id');
    }
}
