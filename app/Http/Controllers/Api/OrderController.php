<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order; // Assuming there is an Order model
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * List all orders for the authenticated user.
     */
    public function index()
    {
        // Assuming JWT auth provides the user via auth()->user()
        $user = auth()->user();
        $orders = Order::where('user_id', $user->id)->orderByDesc('created_at')->get();
        return OrderResource::collection($orders);
    }

    /**
     * Show a specific order.
     */
    public function show($id)
    {
        $user = auth()->user();
        $order = Order::where('id', $id)->where('user_id', $user->id)->firstOrFail();
        return new OrderResource($order);
    }
}
