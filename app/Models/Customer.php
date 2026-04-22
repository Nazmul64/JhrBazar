<?php
// app/Models/Customer.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'profile_image',
        'gender',
        'date_of_birth',
    ];

    // Customer belongs to a User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
