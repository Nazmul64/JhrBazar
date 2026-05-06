<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Http\Request;

class SellerColorController extends Controller
{
    public function index()
    {
        $colors = Color::latest()->get();
        return view('seller.variants.color_index', compact('colors'));
    }
}
