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
        $totalProducts = Product::where('shop_id', $shop->id)->count();
        $totalOrders   = \App\Models\PosInvoice::where('seller_id', $user->id)->count();
        $totalCategories = Category::count();
        $totalBrands = Brand::count();

        return view('seller.index', compact(
            'totalProducts', 
            'totalOrders', 
            'totalCategories', 
            'totalBrands'
        ));
    }
}
