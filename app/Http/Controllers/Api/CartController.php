<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\CartItemResource;
use App\Models\CartItem;

class CartController extends Controller
{
    /**
     * Add a product to the cart.
     */
    public function add(Request $request)
    {
        // Simple stub: expect product_id and quantity
        $validated = $request->validate([
            'product_id' => 'required|integer',
            'quantity'   => 'required|integer|min:1',
        ]);

        // In a real app you would associate the cart with the authenticated user.
        $cartItem = CartItem::create([
            'product_id' => $validated['product_id'],
            'quantity'   => $validated['quantity'],
            // 'user_id' => auth()->id(), // assuming JWT auth
        ]);

        return response()->json([
            'message' => 'Product added to cart',
            'data'    => new CartItemResource($cartItem),
        ], 201);
    }

    /**
     * Show current user's cart items.
     */
    public function show()
    {
        // Stub: return all cart items (replace with user scoped query)
        $items = CartItem::all();
        return CartItemResource::collection($items);
    }

    /**
     * Remove an item from the cart.
     */
    public function remove($itemId)
    {
        $item = CartItem::findOrFail($itemId);
        $item->delete();
        return response()->json(['message' => 'Item removed'], 200);
    }
}
