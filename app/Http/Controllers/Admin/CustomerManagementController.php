<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerManagementController extends Controller
{
    /**
     * Display a listing of customers.
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'customer')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'blocked') {
                $query->where('is_blocked', true);
            } elseif ($request->status === 'active') {
                $query->where('is_blocked', false);
            }
        }

        $customers = $query->paginate(20)->withQueryString();
        
        // Count logged in users (approximate based on last_activity if implemented, 
        // but for now let's just show total customers)
        $totalActive = User::where('role', 'customer')->where('is_blocked', false)->count();
        $totalBlocked = User::where('role', 'customer')->where('is_blocked', true)->count();

        return view('admin.customers.index', compact('customers', 'totalActive', 'totalBlocked'));
    }

    /**
     * Toggle block status for a customer.
     */
    public function toggleBlock($id)
    {
        $user = User::findOrFail($id);
        $user->is_blocked = !$user->is_blocked;
        $user->save();

        $message = $user->is_blocked ? 'Customer blocked successfully.' : 'Customer unblocked successfully.';
        return back()->with('success', $message);
    }
}
