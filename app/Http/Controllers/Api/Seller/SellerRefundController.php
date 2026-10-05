<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Models\Refund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerRefundController extends Controller
{
    public function index(Request $request)
    {
        $sellerId = Auth::id() ?? auth()->id();
        $refunds = Refund::where('seller_id', $sellerId)
            ->with(['order.invoice', 'product'])
            ->latest()
            ->paginate(15);

        return response()->json($refunds);
    }

    public function review(Request $request, $id)
    {
        $sellerId = Auth::id() ?? auth()->id();
        $refund = Refund::where('seller_id', $sellerId)->findOrFail($id);

        $validated = $request->validate([
            'seller_approval' => 'required|in:approved,rejected',
            'seller_note'     => 'nullable|string|max:1000',
        ]);

        $refund->update([
            'seller_approval' => $validated['seller_approval'],
            'seller_note'     => $validated['seller_note'] ?? $refund->seller_note,
        ]);

        return response()->json([
            'message' => 'Seller review recorded successfully.',
            'refund'  => $refund
        ]);
    }
}
