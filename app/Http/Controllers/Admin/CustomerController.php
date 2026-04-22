<?php
// app/Http/Controllers/Admin/CustomerController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    // ── List all customers ──────────────────────────────
    public function index()
    {
        $customers = Customer::with('user')
                        ->latest()
                        ->paginate(15);

        return view('admin.customer.index', compact('customers'));
    }

    // ── Show create form ────────────────────────────────
    public function create()
    {
        return view('admin.customer.create');
    }

    // ── Store new customer ──────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'nullable|string|max:100',
            'phone'         => 'required|string|max:20',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|min:6|confirmed',
            'gender'        => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // 1. Handle profile image — save to public/uploads/customers
        $imagePath = null;
        if ($request->hasFile('profile_image')) {
            $file      = $request->file('profile_image');
            $filename  = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/customers'), $filename);
            $imagePath = 'uploads/customers/' . $filename;
        }

        // 2. Create user account
        $user = User::create([
            'name'     => $request->first_name . ' ' . $request->last_name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'role'     => 'customer',
        ]);

        // 3. Create customer profile
        Customer::create([
            'user_id'       => $user->id,
            'first_name'    => $request->first_name,
            'last_name'     => $request->last_name,
            'profile_image' => $imagePath,
            'gender'        => $request->gender,
            'date_of_birth' => $request->date_of_birth,
        ]);

        return redirect()->route('admin.customers.index')
                         ->with('success', 'Customer created successfully.');
    }

    // ── Show edit form ──────────────────────────────────
    public function edit(string $id)
    {
        $customer = Customer::with('user')->findOrFail($id);
        return view('admin.customer.edit', compact('customer'));
    }

    // ── Update customer ─────────────────────────────────
    public function update(Request $request, string $id)
    {
        $customer = Customer::with('user')->findOrFail($id);

        $request->validate([
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'nullable|string|max:100',
            'phone'         => 'required|string|max:20',
            'email'         => 'required|email|unique:users,email,' . $customer->user_id,
            'gender'        => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Handle new profile image
        $imagePath = $customer->profile_image; // keep old image by default
        if ($request->hasFile('profile_image')) {

            // Delete old image file if exists
            if ($imagePath && file_exists(public_path($imagePath))) {
                unlink(public_path($imagePath));
            }

            // Save new image
            $file      = $request->file('profile_image');
            $filename  = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/customers'), $filename);
            $imagePath = 'uploads/customers/' . $filename;
        }

        // Update users table
        $customer->user->update([
            'name'  => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        // Update customers table
        $customer->update([
            'first_name'    => $request->first_name,
            'last_name'     => $request->last_name,
            'profile_image' => $imagePath,
            'gender'        => $request->gender,
            'date_of_birth' => $request->date_of_birth,
        ]);

        return redirect()->route('admin.customers.index')
                         ->with('success', 'Customer updated successfully.');
    }

    // ── Delete customer ─────────────────────────────────
    public function destroy(string $id)
    {
        $customer = Customer::with('user')->findOrFail($id);

        // Delete image file
        if ($customer->profile_image && file_exists(public_path($customer->profile_image))) {
            unlink(public_path($customer->profile_image));
        }

        $customer->user->delete(); // cascade deletes customer row too

        return back()->with('success', 'Customer deleted successfully.');
    }

    // ── Reset Password ──────────────────────────────────
    public function resetPassword(Request $request, string $id)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed',
        ]);

        $customer = Customer::with('user')->findOrFail($id);
        $customer->user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password reset successfully.');
    }
}
