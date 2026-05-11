<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pointofsalepo;
use App\Models\PosInvoice;
use App\Models\Wishlist;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CustomerDashboardController extends Controller
{
    /**
     * Get dashboard summary data.
     */
    public function index()
    {
        $user = auth()->user();
        
        $orderCount = Pointofsalepo::where('customer_id', $user->id)
            ->orWhere('phone', $user->phone)
            ->count();

        $wishlistCount = Wishlist::where('user_id', $user->id)->count();

        return response()->json([
            'success' => true,
            'data'    => [
                'order_count'    => $orderCount,
                'wishlist_count' => $wishlistCount,
            ]
        ]);
    }

    /**
     * Get all orders for the customer.
     */
    public function orders()
    {
        $user = auth()->user();
        $orders = Pointofsalepo::with('invoice')
            ->where('customer_id', $user->id)
            ->orWhere('phone', $user->phone)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $orders
        ]);
    }

    /**
     * Get wishlist products.
     */
    public function wishlist()
    {
        $user = auth()->user();
        $wishlistItems = Wishlist::where('user_id', $user->id)
            ->latest()
            ->get();

        $products = [];
        foreach ($wishlistItems as $item) {
            $product = null;
            if ($item->product_type === 'admin') {
                $product = \App\Models\Product::find($item->product_id);
            } elseif ($item->product_type === 'seller') {
                $product = \App\Models\SellerProduct::find($item->product_id);
            } elseif ($item->product_type === 'digital') {
                $product = \App\Models\DigitalProduct::find($item->product_id);
            }

            if ($product) {
                $products[] = $this->mapWishlistProduct($product, $item->product_type);
            }
        }

        return response()->json([
            'success' => true,
            'data'    => $products
        ]);
    }

    private function mapWishlistProduct($product, $type)
    {
        return [
            'id'           => $product->id,
            'name'         => $product->name,
            'thumbnail'    => $product->thumbnail ? (str_starts_with($product->thumbnail, 'http') ? $product->thumbnail : '/' . ltrim($product->thumbnail, '/')) : '/placeholder.jpg',
            'price'        => $product->selling_price,
            'product_type' => $type
        ];
    }

    /**
     * Update customer profile.
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'name'  => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'প্রোফাইল সফলভাবে আপডেট করা হয়েছে।',
            'user'    => $user
        ]);
    }

    /**
     * Change password.
     */
    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password'         => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'বর্তমান পাসওয়ার্ডটি সঠিক নয়।'
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'পাসওয়ার্ড সফলভাবে পরিবর্তন করা হয়েছে।'
        ]);
    }
}
