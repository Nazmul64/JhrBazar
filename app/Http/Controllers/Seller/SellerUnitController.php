<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;

class SellerUnitController extends Controller
{
    public function index()
    {
        $units = Unit::latest()->get();
        return view('seller.variants.unit_index', compact('units'));
    }
}
