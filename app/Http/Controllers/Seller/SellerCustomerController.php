<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SellerCustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::with('user')->latest()->paginate(15);
        return view('seller.customer.index', compact('customers'));
    }

    public function create()
    {
        return view('seller.customer.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'nullable|string|max:100',
            'phone'      => 'required|string|max:20|unique:users,phone',
            'email'      => 'required|email|max:191|unique:users,email',
            'password'   => 'required|string|min:6|confirmed',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name'      => $request->first_name . ' ' . $request->last_name,
                'last_name' => $request->last_name,
                'email'     => $request->email,
                'phone'     => $request->phone,
                'password'  => Hash::make($request->password),
                'role'      => 'customer',
                'status'    => 1,
            ]);

            Customer::create([
                'user_id'    => $user->id,
                'first_name' => $request->first_name,
                'last_name'  => $request->last_name,
            ]);

            DB::commit();
            return redirect()->route('seller.customers.index')->with('success', 'Customer Created Successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $customer = Customer::with('user')->findOrFail($id);
        return view('seller.customer.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
        $user = $customer->user;

        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'nullable|string|max:100',
            'phone'      => 'required|string|max:20|unique:users,phone,' . $user->id,
            'email'      => 'required|email|max:191|unique:users,email,' . $user->id,
            'password'   => 'nullable|string|min:6|confirmed',
        ]);

        DB::beginTransaction();
        try {
            $user->update([
                'name'      => $request->first_name . ' ' . $request->last_name,
                'last_name' => $request->last_name,
                'email'     => $request->email,
                'phone'     => $request->phone,
            ]);

            if ($request->filled('password')) {
                $user->update(['password' => Hash::make($request->password)]);
            }

            $customer->update([
                'first_name' => $request->first_name,
                'last_name'  => $request->last_name,
            ]);

            DB::commit();
            return redirect()->route('seller.customers.index')->with('success', 'Customer Updated Successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $user = $customer->user;
        
        DB::beginTransaction();
        try {
            $customer->delete();
            if ($user) $user->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Customer Deleted Successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
