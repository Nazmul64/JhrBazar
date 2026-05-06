<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Size;
use Illuminate\Http\Request;

class SellerSizeController extends Controller
{
    public function index()
    {
        $sizes = Size::latest()->get();
        return view('seller.variants.size_index', compact('sizes'));
    }
}
