<?php
// app/Http/Controllers/Admin/RoleController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    // ── List roles + all permissions ────────────────────
    public function index()
    {
        $roles       = Role::withCount('permissions')->get();
        $permissions = Permission::all()->groupBy('group');

        return view('admin.role.index', compact('roles', 'permissions'));
    }

    // ── Create new role ──────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:roles,name',
        ]);

        Role::create([
            'name'                => $request->name,
            'applicable_for_shop' => $request->boolean('applicable_for_shop', true),
        ]);

        return back()->with('success', 'Role created successfully.');
    }

    // ── Update role name (Edit modal) ────────────────────
    public function updateName(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:roles,name,' . $id,
        ]);

        $role = Role::findOrFail($id);
        $role->update([
            'name'                => $request->name,
            'applicable_for_shop' => $request->boolean('applicable_for_shop', true),
        ]);

        return back()->with('success', 'Role updated successfully.');
    }

    // ── Sync permissions for a role (AJAX) ──────────────
    public function syncPermissions(Request $request, string $id)
    {
        $role = Role::findOrFail($id);

        $permissionIds = $request->input('permissions', []);
        $role->permissions()->sync($permissionIds);

        // Return updated count
        return response()->json([
            'success' => true,
            'message' => 'Permissions updated successfully.',
            'count'   => count($permissionIds),
        ]);
    }

    // ── Get role permissions (AJAX) ──────────────────────
    public function getPermissions(string $id)
    {
        $role = Role::with('permissions')->findOrFail($id);

        return response()->json([
            'permission_ids' => $role->permissions->pluck('id')->toArray(),
        ]);
    }

    // ── Delete role ──────────────────────────────────────
    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return back()->with('success', 'Role deleted successfully.');
    }
}
