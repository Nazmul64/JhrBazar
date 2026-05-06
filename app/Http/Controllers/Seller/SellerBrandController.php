<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class SellerBrandController extends Controller
{
    public function index()
    {
        $brands = Brand::latest()->get();
        return view('seller.variants.brand_index', compact('brands'));
    }
}
