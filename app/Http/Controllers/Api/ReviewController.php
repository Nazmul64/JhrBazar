<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Pointofsalepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    /**
     * Store a new review.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id'   => 'required',
            'product_type' => 'required|string',
            'rating'       => 'required|integer|min:1|max:5',
            'comment'      => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        // Check if user has already reviewed this product
        $existing = Review::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->where('product_type', $request->product_type)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'আপনি ইতিমধ্যে এই প্রোডাক্টটিতে রিভিউ দিয়েছেন।'
            ], 422);
        }

        $shopId = null;
        if ($request->product_type === 'product' || $request->product_type === 'admin' || $request->product_type === 'digital_admin') {
            $productTable = ($request->product_type === 'digital_admin') ? 'digital_products' : 'products';
            $product = DB::table($productTable)->where('id', $request->product_id)->first();
            $shopId = $product->shop_id ?? null;
        } elseif ($request->product_type === 'seller' || $request->product_type === 'seller_product' || $request->product_type === 'digital_seller') {
            $productTable = ($request->product_type === 'digital_seller') ? 'seller_digital_products' : 'seller_products';
            $product = DB::table($productTable)->where('id', $request->product_id)->first();
            if ($product) {
                $shop = DB::table('shops')->where('user_id', $product->seller_id)->first();
                $shopId = $shop->id ?? null;
            }
        }

        $review = Review::create([
            'user_id'      => auth()->id(),
            'shop_id'      => $shopId,
            'product_id'   => $request->product_id,
            'product_type' => $request->product_type,
            'rating'       => $request->rating,
            'comment'      => $request->comment,
            'status'       => 1, // Auto-approve
        ]);

        return response()->json([
            'success' => true,
            'message' => 'আপনার রিভিউটি সফলভাবে জমা দেওয়া হয়েছে।',
            'data'    => $review
        ]);
    }

    /**
     * Get reviews for a product.
     */
    public function getProductReviews($type, $id)
    {
        $reviews = Review::with('user:id,name')
            ->where('product_id', $id)
            ->where('product_type', $type)
            ->where('status', 1)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $reviews
        ]);
    }

    /**
     * Get recent reviews for home page.
     */
    public function getRecentReviews()
    {
        $reviews = Review::with('user:id,name')
            ->where('status', 1)
            ->latest()
            ->take(10)
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $reviews
        ]);
    }

    /**
     * Fetch reviews for a specific product ID and product type.
     */
    public function fetch($product_id, $product_type)
    {
        return $this->getProductReviews($product_type, $product_id);
    }
}
