<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\SellerProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SellerProductController extends Controller
{
    // 1. All Seller Products List (with status filtering)
    public function index(Request $request)
    {
        $query = SellerProduct::with(['seller:id,name,email', 'category:id,name', 'brand:id,name'])->latest();

        if ($request->has('status') && in_array($request->status, ['pending', 'approved', 'rejected'])) {
            $query->where('admin_status', $request->status);
        }

        if ($request->search) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                  ->orWhere('sku', 'like', $search)
                  ->orWhereHas('seller', function ($sq) use ($search) {
                      $sq->where('name', 'like', $search)->orWhere('email', 'like', $search);
                  });
            });
        }

        return response()->json($query->paginate(15));
    }

    // 2. Status Update (Approve / Reject)
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'admin_status'     => 'required|in:approved,rejected',
            'rejection_reason' => 'required_if:admin_status,rejected|nullable|string',
        ]);

        $product = SellerProduct::findOrFail($id);
        $isApproved = $validated['admin_status'] === 'approved';

        $product->update([
            'admin_status'     => $validated['admin_status'],
            'rejection_reason' => $isApproved ? null : ($validated['rejection_reason'] ?? null),
            'is_active'        => $isApproved,
        ]);

        Cache::forget('homepage_data_v2');
        Cache::forget('home_data_v2');

        return response()->json([
            'message' => "Product status updated to {$validated['admin_status']}.",
            'product' => $product
        ]);
    }

    // 3. Admin Edit Product
    public function update(Request $request, $id)
    {
        $product = SellerProduct::findOrFail($id);

        $validated = $request->validate([
            'name'           => 'sometimes|string|max:255',
            'selling_price'  => 'sometimes|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'sometimes|integer|min:0',
            'description'    => 'sometimes|string',
            'is_active'      => 'sometimes|boolean',
            'admin_status'   => 'sometimes|in:pending,approved,rejected',
        ]);

        $product->update($validated);

        Cache::forget('homepage_data_v2');
        Cache::forget('home_data_v2');

        return response()->json([
            'message' => 'Product updated successfully.',
            'product' => $product
        ]);
    }

    // 4. Admin Delete Product
    public function destroy($id)
    {
        $product = SellerProduct::findOrFail($id);
        $product->delete();

        Cache::forget('homepage_data_v2');
        Cache::forget('home_data_v2');

        return response()->json(['message' => 'Product deleted successfully.']);
    }
}
