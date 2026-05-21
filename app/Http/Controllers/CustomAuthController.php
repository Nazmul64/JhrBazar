<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class CustomAuthController extends Controller
{
    /**
     * Show Customer Registration Form
     */
    public function showCustomerRegister()
    {
        return view('auth.register_customer');
    }

    /**
     * Handle Customer Registration
     */
    public function registerCustomer(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::min(8)],
            'phone'    => 'required|string|max:20|unique:users,phone',
        ]);

        $role = Role::where('name', 'customer')->first();

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'phone'    => $request->phone,
            'role'     => 'customer',
            'role_id'  => $role ? $role->id : null,
        ]);

        // Create associated Customer record
        \App\Models\Customer::create([
            'user_id'    => $user->id,
            'first_name' => $request->name,
        ]);

        Auth::login($user);

        return redirect()->route('customer.dashboard')->with('success', 'Registration successful!');
    }

    /**
     * Show Seller Registration Form
     */
    public function showSellerRegister()
    {
        $banks = \App\Models\Bank::where('status', 'active')->orderBy('name')->get();
        return view('auth.register_seller', compact('banks'));
    }

    /**
     * Handle Seller Registration
     */
    public function registerSeller(Request $request)
    {
        $request->validate([
            'name'                => 'required|string|max:255',
            'last_name'           => 'required|string|max:255',
            'email'               => 'required|string|email|max:255|unique:users',
            'phone'               => 'required|string|max:20',
            'password'            => ['required', 'confirmed', Password::min(8)],
            'business_type'       => 'required',
            'business_name'       => 'required',
            'store_name'          => 'required',
            'national_id_card'    => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
            'logo'                => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $role = Role::where('name', 'seller')->first();

        // Handle File Uploads
        $nidPath = null;
        if ($request->hasFile('national_id_card')) {
            $nidFile = $request->file('national_id_card');
            $nidName = time() . '_nid_' . $nidFile->getClientOriginalName();
            $nidFile->move(public_path('uploads/seller'), $nidName);
            $nidPath = 'uploads/seller/' . $nidName;
        }

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoFile = $request->file('logo');
            $logoName = time() . '_logo_' . $logoFile->getClientOriginalName();
            $logoFile->move(public_path('uploads/seller'), $logoName);
            $logoPath = 'uploads/seller/' . $logoName;
        }

        // Create User
        $user = User::create([
            'name'                => $request->name,
            'last_name'           => $request->last_name,
            'email'               => $request->email,
            'phone'               => $request->phone,
            'password'            => Hash::make($request->password),
            'role'                => 'seller',
            'status'              => 'pending',
            'role_id'             => $role ? $role->id : null,
            'national_id_card'    => $nidPath,
            'bank_name'           => $request->bank_name,
            'bank_branch'         => $request->bank_branch,
            'bank_account_number' => $request->bank_account_number,
            'bank_account_holder' => $request->bank_account_holder,
        ]);

        // Create Shop
        \DB::table('shops')->insert([
            'user_id'          => $user->id,
            'name'             => $request->store_name,
            'business_name'    => $request->business_name,
            'business_type'    => $request->business_type,
            'address'          => $request->business_address,
            'city'             => $request->city,
            'postal_code'      => $request->postal_code,
            'url'              => $request->store_url,
            'description'      => $request->description,
            'categories'       => $request->categories,
            'logo'             => $logoPath,
            'status'           => 0,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        return redirect()->route('seller.login')->with('warning', 'Your registration is pending approval. Please wait for an administrator to activate your account.');
    }
}
