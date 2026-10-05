<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SellerProduct;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SellerProductController extends Controller
{
    /**
     * Display all seller products with status filtering & search.
     */
    public function index(Request $request)
    {
        $query = SellerProduct::with(['seller', 'category', 'brand'])->latest();

        if ($request->has('status') && in_array($request->status, ['pending', 'approved', 'rejected'])) {
            $query->where('admin_status', $request->status);
        }

        if ($request->search) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                  ->orWhere('sku', 'like', $search)
                  ->orWhere('barcode', 'like', $search)
                  ->orWhereHas('seller', function ($sq) use ($search) {
                      $sq->where('name', 'like', $search)->orWhere('email', 'like', $search);
                  });
            });
        }

        $products = $query->paginate(20);

        // Counts for tabs
        $counts = [
            'all'      => SellerProduct::count(),
            'pending'  => SellerProduct::where('admin_status', 'pending')->count(),
            'approved' => SellerProduct::where('admin_status', 'approved')->count(),
            'rejected' => SellerProduct::where('admin_status', 'rejected')->count(),
        ];

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'products' => $products,
                'counts'   => $counts,
            ]);
        }

        return view('admin.seller_products.index', compact('products', 'counts'));
    }

    /**
     * Update product approval status (Approve / Reject).
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'admin_status'     => 'required|in:approved,rejected',
            'rejection_reason' => 'required_if:admin_status,rejected|nullable|string|max:1000',
        ]);

        $product = SellerProduct::findOrFail($id);

        $isApproved = $request->admin_status === 'approved';

        $product->update([
            'admin_status'     => $request->admin_status,
            'rejection_reason' => $isApproved ? null : $request->rejection_reason,
            'is_active'        => $isApproved,
        ]);

        Cache::forget('homepage_data_v2');
        Cache::forget('home_data_v2');

        $msg = $isApproved ? 'Product approved and published successfully.' : 'Product rejected successfully.';

        if ($request->wantsJson() || $request->ajax() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'product' => $product
            ]);
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Show edit form for admin.
     */
    public function edit($id)
    {
        $product = SellerProduct::with('seller')->findOrFail($id);
        $categories = Category::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();

        return view('admin.seller_products.edit', compact('product', 'categories', 'brands'));
    }

    /**
     * Update product by admin.
     */
    public function update(Request $request, $id)
    {
        $product = SellerProduct::findOrFail($id);

        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'selling_price'  => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'is_active'      => 'nullable|boolean',
            'admin_status'   => 'required|in:pending,approved,rejected',
        ]);

        $product->update([
            'name'           => $validated['name'],
            'selling_price'  => $validated['selling_price'],
            'discount_price' => $validated['discount_price'] ?? 0,
            'stock_quantity' => $validated['stock_quantity'],
            'is_active'      => $request->has('is_active') ? $request->boolean('is_active') : $product->is_active,
            'admin_status'   => $validated['admin_status'],
        ]);

        Cache::forget('homepage_data_v2');
        Cache::forget('home_data_v2');

        if ($request->wantsJson() || $request->ajax() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully.',
                'product' => $product
            ]);
        }

        return redirect()->route('admin.seller-products.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Delete seller product by admin.
     */
    public function destroy(Request $request, $id)
    {
        $product = SellerProduct::findOrFail($id);
        
        if ($product->thumbnail && file_exists(public_path($product->thumbnail))) {
            @unlink(public_path($product->thumbnail));
        }

        $product->delete();

        Cache::forget('homepage_data_v2');
        Cache::forget('home_data_v2');

        if ($request->wantsJson() || $request->ajax() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully.'
            ]);
        }

        return redirect()->back()->with('success', 'Product deleted successfully.');
    }
}
