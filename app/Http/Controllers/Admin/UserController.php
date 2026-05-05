<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * List all users with their roles
     */
    public function index()
    {
        $users = User::latest()->paginate(20);
        return view('admin.user.index', compact('users'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $userTypes = Role::userTypes();
        $roles     = Role::all();
        return view('admin.user.create', compact('userTypes', 'roles'));
    }

    /**
     * Store new user
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'phone'         => 'required|string|max:20',
            'password'      => ['required', 'confirmed', Password::min(6)],
            'user_type'     => 'required|string|in:' . implode(',', Role::userTypes()),
            'role_id'       => 'required|exists:roles,id',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
        ]);

        $imagePath = null;
        if ($request->hasFile('profile_image')) {
            $imagePath = $this->uploadImage($request->file('profile_image'));
        }

        $role = Role::find($request->role_id);

        User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'phone'         => $request->phone,
            'password'      => Hash::make($request->password),
            'role'          => $role->name, // string representation
            'role_id'       => $role->id,
            'profile_image' => $imagePath,
            // You can add a 'user_type' column to users table too if needed, 
            // but usually 'role' determines the dashboard.
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    /**
     * Show edit form
     */
    public function edit(User $user)
    {
        $userTypes = Role::userTypes();
        $roles     = Role::all();
        return view('admin.user.edit', compact('user', 'userTypes', 'roles'));
    }

    /**
     * Update user
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email,' . $user->id,
            'phone'         => 'required|string|max:20',
            'password'      => ['nullable', 'confirmed', Password::min(6)],
            'user_type'     => 'required|string|in:' . implode(',', Role::userTypes()),
            'role_id'       => 'required|exists:roles,id',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
        ]);

        if ($request->hasFile('profile_image')) {
            $this->deleteImage($user->profile_image);
            $user->profile_image = $this->uploadImage($request->file('profile_image'));
        }

        $role = Role::find($request->role_id);

        $user->update([
            'name'    => $request->name,
            'email'   => $request->email,
            'phone'   => $request->phone,
            'role'    => $role->name,
            'role_id' => $role->id,
        ]);

        if ($request->password) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Delete user
     */
    public function destroy(User $user)
    {
        $this->deleteImage($user->profile_image);
        $user->delete();
        return back()->with('success', 'User deleted successfully.');
    }

    /**
     * Upload image to public/uploads/rolephoto/
     */
    private function uploadImage($file): string
    {
        $uploadPath = public_path('uploads/rolephoto');
        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }
        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($uploadPath, $fileName);
        return 'uploads/rolephoto/' . $fileName;
    }

    /**
     * Delete image
     */
    private function deleteImage(?string $path): void
    {
        if ($path && File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }
}
