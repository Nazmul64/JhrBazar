<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PosInvoice;
use App\Models\Product;
use App\Models\Customer;
use App\Models\User;

class Admincontroller extends Controller
{
    public function dashboard()
    {
        if (auth()->user()->role === 'employee') {
            return redirect()->route('employee.dashboard');
        }
        if (auth()->user()->role === 'manager') {
            return redirect()->route('manager.dashboard');
        }
        $totalOrders    = PosInvoice::count();
        $totalProducts  = Product::count();
        $totalCustomers = Customer::count();
        $totalSellers   = User::where('role', 'seller')->count();

        // Status counts
        $pendingCount    = PosInvoice::whereHas('order', fn($q) => $q->where('status', 'pending'))->count();
        $confirmedCount  = PosInvoice::whereHas('order', fn($q) => $q->where('status', 'confirmed'))->count();
        $processingCount = PosInvoice::whereHas('order', fn($q) => $q->where('status', 'processing'))->count();
        $pickupCount     = PosInvoice::whereHas('order', fn($q) => $q->where('status', 'pickup'))->count();
        $onthewayCount   = PosInvoice::whereHas('order', fn($q) => $q->where('status', 'shipped'))->count();
        $deliveredCount  = PosInvoice::whereHas('order', fn($q) => $q->where('status', 'delivered'))->count();
        $cancelledCount  = PosInvoice::whereHas('order', fn($q) => $q->where('status', 'cancelled'))->count();

        return view('admin.index', compact(
            'totalOrders', 'totalProducts', 'totalCustomers', 'totalSellers',
            'pendingCount', 'confirmedCount', 'processingCount', 'pickupCount',
            'onthewayCount', 'deliveredCount', 'cancelledCount'
        ));
    }
}
