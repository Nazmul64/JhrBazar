<?php
// app/Models/Role.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name', 'user_type', 'applicable_for_shop'];

    const TYPE_ADMIN    = 'admin';
    const TYPE_MANAGER  = 'manager';
    const TYPE_EMPLOYEE = 'employee';
    const TYPE_CUSTOMER = 'customer';
    const TYPE_SELLER   = 'seller';

    public static function userTypes(): array
    {
        return [
            self::TYPE_ADMIN,
            self::TYPE_MANAGER,
            self::TYPE_EMPLOYEE,
            self::TYPE_CUSTOMER,
            self::TYPE_SELLER,
        ];
    }

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
