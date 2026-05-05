<?php

use App\Models\Role;
use App\Models\Permission;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$pList = Permission::whereIn('key', ['profile.list', 'profile.edit', 'profile.change_password'])->pluck('id');

Role::whereIn('name', ['admin', 'manager', 'employee'])->get()->each(function($role) use ($pList) {
    $role->permissions()->syncWithoutDetaching($pList);
});

echo "Permissions synced successfully.\n";
