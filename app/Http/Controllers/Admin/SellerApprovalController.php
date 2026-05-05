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
        
        return view('admin.sellers.approval', compact('pendingSellers', 'activeSellers'));
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
}
