<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Flashsale;

class SellerFlashSalesShowController extends Controller
{
    // ── Index: List all flash sales (read-only) ──────────────────────────
    public function index()
    {
        $flashsales = Flashsale::latest()->get();
        return view('seller.flashsales.index', compact('flashsales'));
    }

    // ── Show: Flash Deal Details (read-only) ─────────────────────────────
    public function show($id)
    {
        $flashsale = Flashsale::with('products.category')->findOrFail($id);
        return view('seller.flashsales.show', compact('flashsale'));
    }
}
