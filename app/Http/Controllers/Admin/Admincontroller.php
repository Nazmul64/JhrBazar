<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PosInvoice;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Customer;
use App\Models\User;
use App\Models\Withdraw;

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

        // New Statistics
        $lastMonthOrders = PosInvoice::where('created_at', '>=', now()->subMonth())->count();
        $thisYearOrders  = PosInvoice::whereYear('created_at', now()->year)->count();

        // Dynamic Trending Shops (based on order count)
        $trendingShops = Shop::take(5)->get();
        foreach($trendingShops as $shop) {
            $shop->order_count = PosInvoice::where('seller_id', $shop->user_id)->count();
            $shop->rating      = 5.0; // Default rating for now
        }
        $trendingShops = $trendingShops->sortByDesc('order_count');

        // Dynamic Products
        $favoriteProducts = Product::where('is_popular', true)->latest()->take(3)->get();
        if ($favoriteProducts->isEmpty()) {
            $favoriteProducts = Product::latest()->take(3)->get();
        }

        $topSellingProducts = Product::where('is_best_seller', true)->latest()->take(3)->get();
        if ($topSellingProducts->isEmpty()) {
            $topSellingProducts = Product::orderBy('rating', 'desc')->take(3)->get();
        }

        $recentOrders  = PosInvoice::with(['seller.shop', 'order'])->latest()->take(5)->get();

        // Status counts
        $pendingCount    = PosInvoice::whereHas('order', fn($q) => $q->where('status', 'pending'))->count();
        $confirmedCount  = PosInvoice::whereHas('order', fn($q) => $q->where('status', 'confirmed'))->count();
        $processingCount = PosInvoice::whereHas('order', fn($q) => $q->where('status', 'processing'))->count();
        $pickupCount     = PosInvoice::whereHas('order', fn($q) => $q->where('status', 'pickup'))->count();
        $onthewayCount   = PosInvoice::whereHas('order', fn($q) => $q->where('status', 'shipped'))->count();
        $deliveredCount  = PosInvoice::whereHas('order', fn($q) => $q->where('status', 'delivered'))->count();
        $cancelledCount  = PosInvoice::whereHas('order', fn($q) => $q->where('status', 'cancelled'))->count();

        // Total Admin Commission & Withdrawals
        $totalCommission = \App\Models\SellerTransaction::whereIn('type', ['earning', 'adjustment'])->sum('commission');
        $alreadyWithdraw = Withdraw::where('status', 'approved')->sum('amount');
        $pendingWithdraw = Withdraw::where('status', 'pending')->sum('amount');

        // Dynamic Chart Data (Last 6 Months Orders)
        $chartMonths = [];
        $chartOrderData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $chartMonths[] = $month->format('M');
            $chartOrderData[] = PosInvoice::whereMonth('created_at', $month->month)
                                         ->whereYear('created_at', $month->year)
                                         ->count();
        }

        // User Overview Chart (Customers vs Sellers)
        $userOverviewData = [$totalCustomers, $totalSellers, User::where('role', 'manager')->count()];

        return view('admin.index', compact(
            'totalOrders', 'totalProducts', 'totalCustomers', 'totalSellers',
            'pendingCount', 'confirmedCount', 'processingCount', 'pickupCount',
            'onthewayCount', 'deliveredCount', 'cancelledCount', 'trendingShops',
            'recentOrders', 'totalCommission', 'alreadyWithdraw', 'pendingWithdraw', 'favoriteProducts', 'topSellingProducts',
            'lastMonthOrders', 'thisYearOrders', 'chartMonths', 'chartOrderData', 'userOverviewData'
        ));
    }
}
