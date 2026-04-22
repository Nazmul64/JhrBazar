<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employeecreate extends Model
{
    use HasFactory;

    protected $table = 'employees';

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'gender',
        'email',
        'password',
        'profile_image',
        'role',
        'role_id',
        'is_active',
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'is_active' => 'boolean',
        'password'  => 'hashed',
    ];

    // Full name accessor
    public function getNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    // Belongs to Role
    public function roleModel()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    // Many-to-many permissions
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'employee_permissions', 'employee_id', 'permission_id');
    }

    // Check if employee has a specific permission key
    public function hasPermission(string $key): bool
    {
        return $this->permissions()->where('key', $key)->exists();
    }

    // Profile image URL
    public function getProfileImageUrlAttribute(): string
    {
        if ($this->profile_image) {
            return asset('storage/' . $this->profile_image);
        }
        return asset('admin/images/default-avatar.png');
    }
}
