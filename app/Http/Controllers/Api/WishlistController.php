<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wishlist;
use App\Models\Product;
use App\Models\SellerProduct;
use App\Models\DigitalProduct;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Get wishlist items for the current user or session.
     */
    public function index(Request $request)
    {
        $query = Wishlist::query();

        if (Auth::check()) {
            $query->where('user_id', Auth::id());
        } else {
            $query->where('session_id', $request->header('X-Session-Id'));
        }

        $wishlists = $query->latest()->get();
        $data = [];

        foreach ($wishlists as $item) {
            $product = null;
            if ($item->product_type == 'admin') {
                $product = Product::with('brand')->find($item->product_id);
            } elseif ($item->product_type == 'seller') {
                $product = SellerProduct::with('brand')->find($item->product_id);
            } elseif ($item->product_type == 'digital') {
                $product = DigitalProduct::find($item->product_id);
            }

            if ($product) {
                $finalPrice = $product->discount_price > 0 ? $product->discount_price : $product->selling_price;
                $data[] = [
                    'wishlist_id'  => $item->id,
                    'id'           => $product->id,
                    'uid'          => $item->product_type . '_' . $product->id,
                    'title'        => $product->name ?? $product->title ?? 'No Name',
                    'image'        => $product->thumbnail ?? $product->image,
                    'price'        => $finalPrice ?? 0,
                    'oldPrice'     => $product->selling_price ?? 0,
                    'product_type' => $item->product_type,
                    'stock'        => $product->stock_quantity ?? 0,
                    'brand'        => $product->brand ? $product->brand->name : null,
                    'size'         => $product->size,
                    'color'        => $product->color,
                    'unit'         => $product->unit,
                    'slug'         => $product->slug ?? '',
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data'    => $data
        ]);
    }

    /**
     * Toggle item in wishlist.
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'product_id'   => 'required',
            'product_type' => 'required'
        ]);

        $userId = Auth::id();
        $sessionId = $request->header('X-Session-Id');

        $query = Wishlist::where('product_id', $request->product_id)
                         ->where('product_type', $request->product_type);

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId);
        }

        $exists = $query->first();

        if ($exists) {
            $exists->delete();
            return response()->json([
                'success' => true,
                'action'  => 'removed',
                'message' => 'Product removed from wishlist'
            ]);
        } else {
            Wishlist::create([
                'user_id'      => $userId,
                'session_id'   => $userId ? null : $sessionId,
                'product_id'   => $request->product_id,
                'product_type' => $request->product_type
            ]);

            return response()->json([
                'success' => true,
                'action'  => 'added',
                'message' => 'Product added to wishlist'
            ]);
        }
    }

    /**
     * Sync guest wishlist to user wishlist after login.
     */
    public function sync(Request $request)
    {
        if (!Auth::check()) return response()->json(['success' => false]);

        $sessionId = $request->header('X-Session-Id');
        if (!$sessionId) return response()->json(['success' => false]);

        $guestItems = Wishlist::where('session_id', $sessionId)->get();

        foreach ($guestItems as $item) {
            // Check if user already has this item
            $exists = Wishlist::where('user_id', Auth::id())
                              ->where('product_id', $item->product_id)
                              ->where('product_type', $item->product_type)
                              ->first();

            if (!$exists) {
                $item->update([
                    'user_id'    => Auth::id(),
                    'session_id' => null
                ]);
            } else {
                $item->delete();
            }
        }

        return response()->json(['success' => true]);
    }
}
