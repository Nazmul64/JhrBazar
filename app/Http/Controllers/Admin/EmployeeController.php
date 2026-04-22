<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employeecreate;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees.
     */
    public function index()
    {
        $employees = Employeecreate::with('roleModel')->latest()->get();
        return view('admin.employee.index', compact('employees'));
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.employee.create', compact('roles'));
    }

    /**
     * Store a newly created employee.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'nullable|string|max:255',
            'phone'         => 'required|string|max:20',
            'gender'        => 'required|in:Male,Female,Other',
            'email'         => 'required|email|unique:employees,email',
            'password'      => ['required', 'confirmed', Password::min(6)],
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'role'          => 'required|string|max:100',
            'role_id'       => 'nullable|exists:roles,id',
        ]);

        // Handle profile image upload → public/uploads/employee/
        if ($request->hasFile('profile_image')) {
            $validated['profile_image'] = $this->uploadImage($request->file('profile_image'));
        }

        $validated['password'] = Hash::make($validated['password']);

        $employee = Employeecreate::create($validated);

        // Auto-assign role's default permissions
        if ($employee->role_id) {
            $role = Role::with('permissions')->find($employee->role_id);
            if ($role) {
                $permissionIds = $role->permissions->pluck('id')->toArray();
                $employee->permissions()->sync($permissionIds);
            }
        }

        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee created successfully.');
    }

    /**
     * Show the form for editing employee details.
     */
public function edit(Employeecreate $employee)
{
    $roles              = Role::all();
    $groupedPermissions = Permission::groupedPermissions();

    return view('admin.employee.edit', compact('employee', 'roles', 'groupedPermissions'));
}

    /**
     * Update employee details.
     */
    public function update(Request $request, Employeecreate $employee)
    {
        $validated = $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'nullable|string|max:255',
            'phone'         => 'required|string|max:20',
            'gender'        => 'required|in:Male,Female,Other',
            'email'         => 'required|email|unique:employees,email,' . $employee->id,
            'password'      => ['nullable', 'confirmed', Password::min(6)],
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'role'          => 'required|string|max:100',
            'role_id'       => 'nullable|exists:roles,id',
        ]);

        // Handle new profile image upload
        if ($request->hasFile('profile_image')) {
            // Delete old image from public/uploads/employee/
            $this->deleteImage($employee->profile_image);
            $validated['profile_image'] = $this->uploadImage($request->file('profile_image'));
        }

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $employee->update($validated);

        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    /**
     * Delete an employee.
     */
    public function destroy(Employeecreate $employee)
    {
        $this->deleteImage($employee->profile_image);
        $employee->delete();

        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee deleted successfully.');
    }

    // ─── Permission Management ─────────────────────────────────────────────

    /**
     * Show permission assignment form for an employee.
     */
    public function permission(Employeecreate $employee)
    {
        $groupedPermissions  = Permission::groupedPermissions();
        $employeePermissions = $employee->permissions->pluck('id')->toArray();

        return view('admin.employee.permission', compact(
            'employee',
            'groupedPermissions',
            'employeePermissions'
        ));
    }

    /**
     * Update employee permissions.
     */
    public function updatePermission(Request $request, Employeecreate $employee)
    {
        $request->validate([
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $employee->permissions()->sync($request->permissions ?? []);

        return redirect()->back()
            ->with('success', 'Permissions updated successfully.');
    }

    /**
     * Toggle employee active/inactive status.
     */
    public function toggleStatus(Employeecreate $employee)
    {
        $employee->update(['is_active' => !$employee->is_active]);

        return redirect()->back()
            ->with('success', 'Employee status updated.');
    }

    /**
     * Reset employee password.
     */
    public function resetPassword(Request $request, Employeecreate $employee)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Password::min(6)],
        ]);

        $employee->update(['password' => Hash::make($request->password)]);

        return redirect()->back()
            ->with('success', 'Password reset successfully.');
    }

    // ─── Private Helpers ──────────────────────────────────────────────────

    /**
     * Upload image to public/uploads/employee/ and return relative path.
     */
    private function uploadImage($file): string
    {
        $uploadPath = public_path('uploads/employee');

        // Create folder if it doesn't exist
        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($uploadPath, $fileName);

        return 'uploads/employee/' . $fileName;
    }

    /**
     * Delete image from public/ if it exists.
     */
    private function deleteImage(?string $path): void
    {
        if ($path && File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }
}
