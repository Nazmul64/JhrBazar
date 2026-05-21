<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $shop = Shop::where('user_id', $user->id)->first();

        if (!$shop) {
            return redirect()->route('admin.profile.index')->with('error', 'Please complete your shop setup.');
        }

        // Isolated Statistics
        $totalProducts = \App\Models\SellerProduct::where('seller_id', $user->id)->count() + \App\Models\SellerDigitalProduct::where('seller_id', $user->id)->count();
        $totalOrders   = \App\Models\Pointofsalepo::where('seller_id', $user->id)->count();
        
        $totalCategories = Category::count();
        $totalBrands     = Brand::count();
        $totalReviews    = \App\Models\Review::where('shop_id', $shop->id ?? 0)->count();
        $totalPosSales   = \App\Models\PosInvoice::where('seller_id', $user->id)->sum('grand_total');
        
        // Detailed Stats
        $totalEarnings = $user->balance;
        
        $orderCounts = \App\Models\PosInvoice::where('pos_invoices.seller_id', $user->id)
            ->join('pointofsalepos', 'pos_invoices.pointofsalepo_id', '=', 'pointofsalepos.id')
            ->select('pointofsalepos.status', \DB::raw('count(*) as total'))
            ->groupBy('pointofsalepos.status')
            ->pluck('total', 'status')
            ->toArray();

        $deliveredOrders = $orderCounts['delivered'] ?? 0;
        $rejectedOrders = ($orderCounts['cancelled'] ?? 0) + ($orderCounts['rejected'] ?? 0);
        $pendingOrders = $orderCounts['pending'] ?? 0;
        $processingOrders = $orderCounts['processing'] ?? 0;
        $shippedOrders = $orderCounts['shipped'] ?? 0;
        $confirmedOrders = $orderCounts['confirmed'] ?? 0;

        // Withdrawal Stats
        $pendingWithdraw = \App\Models\SellerTransaction::where('seller_id', $user->id)
            ->where('type', 'withdrawal')
            ->where('status', 'pending')
            ->sum('amount');
        $alreadyWithdraw = \App\Models\SellerTransaction::where('seller_id', $user->id)
            ->where('type', 'withdrawal')
            ->where('status', 'completed')
            ->sum('amount');
        $rejectedWithdraw = \App\Models\SellerTransaction::where('seller_id', $user->id)
            ->where('type', 'withdrawal')
            ->where('status', 'rejected')
            ->sum('amount');
        $totalWithdraw = $pendingWithdraw + $alreadyWithdraw;

        $recentOrders = \App\Models\PosInvoice::where('seller_id', $user->id)
            ->with('order')
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        // Top Selling Products (Simplified logic for now)
        $topProducts = \App\Models\Product::where('shop_id', $shop->id)
            ->orderBy('id', 'desc')
            ->take(3)
            ->get();

        return view('seller.index', compact(
            'totalProducts', 
            'totalOrders', 
            'totalEarnings',
            'deliveredOrders',
            'rejectedOrders',
            'pendingOrders',
            'processingOrders',
            'shippedOrders',
            'confirmedOrders',
            'pendingWithdraw',
            'alreadyWithdraw',
            'rejectedWithdraw',
            'totalWithdraw',
            'recentOrders',
            'topProducts',
            'totalCategories',
            'totalBrands',
            'totalReviews',
            'totalPosSales'
        ));
    }
}
