<?php
// app/Models/Role.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name', 'applicable_for_shop'];

    protected $casts = [
        'applicable_for_shop' => 'boolean',
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }

    public function employees()
    {
        return $this->hasMany(Employeecreate::class, 'role_id');
    }
}
