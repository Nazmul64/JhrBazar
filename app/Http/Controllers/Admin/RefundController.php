<?php

namespace App\Http\Controllers\Admin;

use App\Models\Refund;
use App\Models\RefundItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Courier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RefundController extends \App\Http\Controllers\Controller
{
    /**
     * Display refund management list
     */
    public function index(Request $request): View
    {
        $query = Refund::with(['order', 'product', 'seller', 'courier'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->status) {
            $query->where('refund_status', $request->status);
        }

        // Filter by reason
        if ($request->reason) {
            $query->where('cancel_reason', $request->reason);
        }

        // Search by order ID or product name
        if ($request->search) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', $search)
                  ->orWhereHas('order', function ($q) use ($search) {
                      $q->where('order_no', 'like', $search);
                  });
            });
        }

        $refunds = $query->paginate(20);
        $statuses = ['pending', 'approved', 'processing', 'completed', 'rejected'];
        $reasons = [
            'admin_cancel' => 'Admin Cancelled',
            'seller_fraud' => 'Seller Fraud',
            'payment_issue' => 'Payment Issue',
            'courier_cancel' => 'Courier Cancelled',
            'customer_request' => 'Customer Request',
            'damaged_product' => 'Damaged Product',
        ];

        return view('admin.refund.index', compact('refunds', 'statuses', 'reasons'));
    }

    /**
     * Show create refund form
     */
    public function create(Request $request): View
    {
        $order = null;
        if ($request->order_id) {
            $order = Order::with('orderItems.product')->findOrFail($request->order_id);
        }

        $couriers = Courier::active()->get();
        $reasons = [
            'admin_cancel' => 'Admin Cancelled',
            'seller_fraud' => 'Seller Fraud',
            'payment_issue' => 'Payment Issue',
            'courier_cancel' => 'Courier Cancelled',
            'customer_request' => 'Customer Request',
            'damaged_product' => 'Damaged Product',
            'other' => 'Other',
        ];

        return view('admin.refund.create', compact('order', 'couriers', 'reasons'));
    }

    /**
     * Store refund in database
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'product_name' => 'required|string|max:255',
            'product_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'courier_id' => 'nullable|exists:couriers,id',
            'cancel_reason' => 'required|in:admin_cancel,seller_fraud,payment_issue,courier_cancel,customer_request,damaged_product,other',
            'cancel_reason_description' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $totalAmount = $validated['product_price'] * $validated['quantity'];

            $refund = Refund::create([
                'order_id' => $validated['order_id'],
                'product_id' => $validated['product_id'],
                'seller_id' => Order::find($validated['order_id'])->seller_id,
                'courier_id' => $validated['courier_id'],
                'product_name' => $validated['product_name'],
                'product_price' => $validated['product_price'],
                'quantity' => $validated['quantity'],
                'total_amount' => $totalAmount,
                'cancel_reason' => $validated['cancel_reason'],
                'cancel_reason_description' => $validated['cancel_reason_description'],
                'refund_status' => 'pending',
                'refund_date' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Refund request created successfully!',
                'refund_id' => $refund->id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error creating refund: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Show refund details
     */
    public function show(Refund $refund): View
    {
        $refund->load(['order', 'product', 'seller', 'courier', 'items']);
        return view('admin.refund.show', compact('refund'));
    }

    /**
     * Update refund status
     */
    public function updateStatus(Request $request, Refund $refund): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,processing,completed,rejected',
            'admin_note' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $refund->update([
                'refund_status' => $validated['status'],
                'admin_note' => $validated['admin_note'] ?? $refund->admin_note,
            ]);

            // Log status change
            activity('refund')
                ->performedOn($refund)
                ->withProperties(['old_status' => $refund->getOriginal('refund_status'), 'new_status' => $validated['status']])
                ->log("Refund status updated to {$validated['status']}");

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Refund status updated successfully!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating refund: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Approve refund
     */
    public function approve(Refund $refund): JsonResponse
    {
        return $this->updateStatus(request(), $refund);
    }

    /**
     * Reject refund
     */
    public function reject(Request $request, Refund $refund): JsonResponse
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $refund->update([
            'refund_status' => 'rejected',
            'admin_note' => $validated['reason'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Refund rejected successfully!',
        ]);
    }

    /**
     * Get products for autocomplete
     */
    public function getProducts(Request $request): JsonResponse
    {
        $search = $request->get('q', '');

        $products = Product::where('name', 'like', "%{$search}%")
            ->limit(10)
            ->get(['id', 'name', 'unit_price as price'])
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'text' => "{$product->name} - ৳{$product->price}",
                    'name' => $product->name,
                    'price' => $product->price,
                ];
            });

        return response()->json(['results' => $products]);
    }

    /**
     * Get couriers
     */
    public function getCouriers(): JsonResponse
    {
        $couriers = Courier::active()->get(['id', 'name']);

        return response()->json([
            'success' => true,
            'couriers' => $couriers
        ]);
    }

    /**
     * Bulk update refund status
     */
    public function bulkUpdateStatus(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'status' => 'required|in:approved,processing,completed,rejected',
        ]);

        DB::beginTransaction();
        try {
            Refund::whereIn('id', $validated['ids'])
                ->update([
                    'refund_status' => $validated['status']
                ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($validated['ids']) . ' refund(s) updated successfully!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating refunds: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Delete refund
     */
    public function destroy(Refund $refund): JsonResponse
    {
        DB::beginTransaction();
        try {
            $refund->items()->delete();
            $refund->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Refund deleted successfully!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting refund: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Export refunds to CSV
     */
    public function export(Request $request)
    {
        $refunds = Refund::with(['order', 'product', 'seller'])
            ->when($request->status, function ($q) use ($request) {
                return $q->where('refund_status', $request->status);
            })
            ->get();

        $filename = 'refunds-' . date('Y-m-d-His') . '.csv';
        $handle = fopen('php://memory', 'r+');

        // Add headers
        fputcsv($handle, ['Refund ID', 'Order ID', 'Product', 'Quantity', 'Amount', 'Reason', 'Status', 'Date']);

        // Add data
        foreach ($refunds as $refund) {
            fputcsv($handle, [
                $refund->id,
                $refund->order->order_no ?? 'N/A',
                $refund->product_name,
                $refund->quantity,
                $refund->total_amount,
                $refund->getCancelReasonDisplay(),
                ucfirst($refund->refund_status),
                $refund->refund_date->format('d-m-Y'),
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
