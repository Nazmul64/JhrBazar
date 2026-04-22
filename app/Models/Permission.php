<?php
// app/Models/Permission.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['group', 'name', 'key'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    public function employees()
    {
        return $this->belongsToMany(Employeecreate::class, 'employee_permissions', 'permission_id', 'employee_id');
    }

    /**
     * Return all permissions grouped by group -> name -> [permissions]
     * Used for rendering the permissions UI.
     */
    public static function groupedPermissions(): array
    {
        $all = self::orderBy('group')->orderBy('name')->orderBy('key')->get();
        $grouped = [];
        foreach ($all as $perm) {
            $grouped[$perm->group][$perm->name][] = $perm;
        }
        return $grouped;
    }
}
