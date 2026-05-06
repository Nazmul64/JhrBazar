<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeSeller extends Model
{
    protected $fillable = [
        'seller_id',
        'first_name',
        'last_name',
        'phone',
        'gender',
        'email',
        'role',
        'password',
        'address',
        'profile_image',
    ];
}
