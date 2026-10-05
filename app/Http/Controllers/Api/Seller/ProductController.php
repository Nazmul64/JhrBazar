<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Models\SellerProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'selling_price'  => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'description'    => 'nullable|string',
            'category_id'    => 'nullable|integer',
            'brand_id'       => 'nullable|integer',
        ]);

        $product = SellerProduct::create(array_merge($validated, [
            'seller_id'    => Auth::id() ?? auth()->id(),
            'slug'         => Str::slug($validated['name']) . '-' . Str::random(5),
            'admin_status' => 'pending', // Waiting for admin approval
            'is_active'    => false,
        ]));

        return response()->json([
            'message' => 'Product submitted successfully. It will be live after Admin approval.',
            'product' => $product
        ], 201);
    }

    public function index(Request $request)
    {
        $products = SellerProduct::where('seller_id', Auth::id() ?? auth()->id())
            ->with(['category', 'brand'])
            ->latest()
            ->paginate(15);

        return response()->json($products);
    }
}
