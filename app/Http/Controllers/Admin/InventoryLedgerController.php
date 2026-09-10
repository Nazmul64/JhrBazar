<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\InventoryLedger;
use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryLedgerController extends Controller
{
    /**
     * Display the inventory ledger and stock management page.
     */
    public function index(Request $request)
    {
        // ── Top Stats ──
        $totalProducts    = Product::count();
        $inStockCount     = Product::where('is_unlimited', false)
            ->where('stock_quantity', '>', DB::raw('COALESCE(low_stock_threshold, 3)'))
            ->count();
        $lowStockCount    = Product::where('is_unlimited', false)
            ->where('stock_quantity', '<=', DB::raw('COALESCE(low_stock_threshold, 3)'))
            ->where('stock_quantity', '>', 0)
            ->count();
        $outOfStockCount  = Product::where('is_unlimited', false)
            ->where('stock_quantity', '<=', 0)
            ->count();
        $unlimitedCount   = Product::where('is_unlimited', true)->count();

        // ── Product Listing with Filters ──
        $query = Product::with(['category', 'brand'])->latest();

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('sku', 'like', "%{$s}%")
                  ->orWhere('barcode', 'like', "%{$s}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            match ($request->status) {
                'in_stock'     => $query->where('is_unlimited', false)
                                       ->where('stock_quantity', '>', DB::raw('COALESCE(low_stock_threshold, 3)')),
                'low_stock'    => $query->where('is_unlimited', false)
                                       ->where('stock_quantity', '<=', DB::raw('COALESCE(low_stock_threshold, 3)'))
                                       ->where('stock_quantity', '>', 0),
                'out_of_stock' => $query->where('is_unlimited', false)
                                       ->where('stock_quantity', '<=', 0),
                'unlimited'    => $query->where('is_unlimited', true),
                default        => null,
            };
        }

        $products   = $query->paginate(20)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.inventory.index', compact(
            'products',
            'categories',
            'totalProducts',
            'inStockCount',
            'lowStockCount',
            'outOfStockCount',
            'unlimitedCount'
        ));
    }

    /**
     * Handle quick Stock In or Manual Adjustment via AJAX.
     */
    public function adjustStock(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'action'     => 'required|in:stock_in,manual_adjustment',
            'quantity'   => 'required_if:action,stock_in|nullable|integer|min:1',
            'new_stock'  => 'required_if:action,manual_adjustment|nullable|integer|min:0',
            'note'       => 'nullable|string|max:255',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->is_unlimited) {
            return response()->json([
                'success' => false,
                'message' => 'This product has Unlimited Stock enabled. Please disable unlimited stock in product settings to manage stock quantities.',
            ], 422);
        }

        if ($request->action === 'stock_in') {
            $ledger = InventoryService::stockIn($product, (int) $request->quantity, $request->note, auth()->id());
            $message = "Successfully added {$request->quantity} units to stock.";
        } else {
            $ledger = InventoryService::manualAdjustment($product, (int) $request->new_stock, $request->note, auth()->id());
            $message = "Stock adjusted to {$request->new_stock} units successfully.";
        }

        $product->refresh();

        return response()->json([
            'success'        => true,
            'message'        => $message,
            'stock_quantity' => $product->stock_quantity,
            'total_in'       => $product->total_in,
            'total_sold'     => $product->total_sold,
            'status'         => $product->stock_status,
        ]);
    }

    /**
     * Get stock movement history for a product (History View modal).
     */
    public function history($productId): JsonResponse
    {
        $product = Product::with('category')->findOrFail($productId);
        $ledgers = InventoryLedger::with(['order.invoice', 'creator'])
            ->where('product_id', $productId)
            ->latest()
            ->take(50)
            ->get()
            ->map(function ($ledger) {
                return [
                    'id'             => $ledger->id,
                    'type'           => $ledger->type,
                    'type_label'     => $ledger->type_label,
                    'type_badge'     => $ledger->type_badge_class,
                    'quantity'       => $ledger->quantity,
                    'previous_stock' => $ledger->previous_stock,
                    'current_stock'  => $ledger->current_stock,
                    'order_id'       => $ledger->order_id,
                    'invoice_number' => $ledger->order?->invoice?->invoice_number ?? ($ledger->order_id ? '#' . $ledger->order_id : null),
                    'note'           => $ledger->note,
                    'admin_name'     => $ledger->creator?->name ?? 'System',
                    'date'           => $ledger->created_at ? $ledger->created_at->format('d M, Y h:i A') : '—',
                ];
            });

        return response()->json([
            'success' => true,
            'product' => [
                'id'                  => $product->id,
                'name'                => $product->name,
                'sku'                 => $product->sku,
                'thumbnail'           => $product->thumbnail ? asset($product->thumbnail) : null,
                'stock_quantity'      => $product->stock_quantity,
                'is_unlimited'        => $product->is_unlimited,
                'low_stock_threshold' => $product->low_stock_threshold,
                'total_in'            => $product->total_in,
                'total_sold'          => $product->total_sold,
                'status'              => $product->stock_status,
            ],
            'ledgers' => $ledgers,
        ]);
    }

    /**
     * Get active low stock alerts for topbar notifications.
     */
    public function lowStockAlerts(): JsonResponse
    {
        $lowStockProducts = Product::where('is_unlimited', false)
            ->where(function ($q) {
                $q->where('stock_quantity', '<=', DB::raw('COALESCE(low_stock_threshold, 3)'));
            })
            ->select('id', 'name', 'sku', 'thumbnail', 'stock_quantity', 'low_stock_threshold')
            ->orderBy('stock_quantity', 'asc')
            ->take(15)
            ->get()
            ->map(function ($p) {
                return [
                    'id'             => $p->id,
                    'name'           => $p->name,
                    'sku'            => $p->sku,
                    'stock_quantity' => $p->stock_quantity,
                    'threshold'      => $p->low_stock_threshold,
                    'thumbnail'      => $p->thumbnail ? asset($p->thumbnail) : null,
                    'is_out_of_stock'=> $p->stock_quantity <= 0,
                    'view_url'       => route('admin.inventory.index', ['search' => $p->sku ?: $p->name]),
                ];
            });

        return response()->json([
            'success' => true,
            'count'   => $lowStockProducts->count(),
            'data'    => $lowStockProducts,
        ]);
    }
}
