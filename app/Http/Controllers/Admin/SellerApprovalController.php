<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class SellerApprovalController extends Controller
{
    /**
     * List all pending sellers
     */
    public function index()
    {
        $pendingSellers = User::with('shop')
            ->where('role', 'seller')
            ->where('status', 'pending')
            ->latest()
            ->get();
            
        $activeSellers  = User::with('shop')
            ->where('role', 'seller')
            ->where('status', 'active')
            ->latest()
            ->get();

        $suspendedSellers = User::with('shop')
            ->where('role', 'seller')
            ->where('status', 'suspended')
            ->latest()
            ->get();
        
        return view('admin.sellers.approval', compact('pendingSellers', 'activeSellers', 'suspendedSellers'));
    }

    /**
     * Activate a seller (from suspended)
     */
    public function activate($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'active';
        $user->save();

        return back()->with('success', "Seller '{$user->name}' has been activated successfully!");
    }

    /**
     * Approve a seller
     */
    public function approve($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'active';
        $user->save();

        return back()->with('success', "Seller '{$user->name}' has been approved successfully!");
    }

    /**
     * Reject/Suspend a seller
     */
    public function reject($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'suspended';
        $user->save();

        return back()->with('error', "Seller '{$user->name}' has been suspended.");
    }

    /**
     * Edit a seller
     */
    public function edit($id)
    {
        $seller = User::with('shop')->findOrFail($id);
        return view('admin.sellers.edit', compact('seller'));
    }

    /**
     * Update a seller
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'name'      => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $id,
            'phone'     => 'nullable|string|max:20',
            'password'  => 'nullable|min:6|confirmed',
        ]);

        $userData = $request->only('name', 'last_name', 'email', 'phone');
        if ($request->filled('password')) {
            $userData['password'] = bcrypt($request->password);
        }
        
        $user->update($userData);

        if ($user->shop) {
            $user->shop->update([
                'name'            => $request->shop_name ?? $user->shop->name,
                'business_name'   => $request->business_name ?? $user->shop->business_name,
                'business_type'   => $request->business_type ?? $user->shop->business_type,
                'address'         => $request->address ?? $user->shop->address,
                'city'            => $request->city ?? $user->shop->city,
                'postal_code'     => $request->postal_code ?? $user->shop->postal_code,
                'url'             => $request->store_url ?? $user->shop->url,
                'description'     => $request->description ?? $user->shop->description,
                'bank_name'       => $request->bank_name ?? $user->bank_name,
                'bank_branch'     => $request->bank_branch ?? $user->bank_branch,
                'bank_account_number' => $request->bank_account_number ?? $user->bank_account_number,
                'bank_account_holder' => $request->bank_account_holder ?? $user->bank_account_holder,
            ]);
        }

        return redirect()->route('admin.sellers.approvals')->with('success', 'Seller profile updated successfully.');
    }

    /**
     * Delete a seller
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        // Delete shop and products if necessary, or just soft delete
        if ($user->shop) $user->shop->delete();
        $user->delete();

        return back()->with('success', 'Seller deleted successfully.');
    }
}
