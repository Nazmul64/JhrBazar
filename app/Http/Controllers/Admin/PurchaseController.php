<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryLedger;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class PurchaseController extends Controller
{
    /**
     * Display a listing of local purchases.
     */
    public function index(Request $request)
    {
        $query = Purchase::with(['supplier.user', 'items.product', 'creator'])->latest('purchase_date')->latest('id');

        // Date Range Filter
        if ($request->filled('from_date')) {
            $query->whereDate('purchase_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('purchase_date', '<=', $request->to_date);
        }

        // Supplier Filter
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // Search by invoice or supplier name
        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('invoice_no', 'like', "%{$s}%")
                  ->orWhereHas('supplier', function ($sq) use ($s) {
                      $sq->where('name', 'like', "%{$s}%")
                         ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$s}%"));
                  });
            });
        }

        // Calculate KPI summaries
        $totalPurchasesCount = (clone $query)->count();
        $totalAmountSum      = (clone $query)->sum('total_amount');
        $totalPaidSum        = (clone $query)->sum('paid_amount');
        $totalDueSum         = (clone $query)->sum('due_amount');

        $purchases = $query->paginate(20)->withQueryString();
        $suppliers = Supplier::where('status', 1)->orWhere('is_active', 1)->get();

        return view('admin.purchase.index', compact(
            'purchases',
            'suppliers',
            'totalPurchasesCount',
            'totalAmountSum',
            'totalPaidSum',
            'totalDueSum'
        ));
    }

    /**
     * Show the form for creating a new local purchase.
     */
    public function create()
    {
        $suppliers = Supplier::where('status', 1)->orWhere('is_active', 1)->get();
        $products  = Product::where('is_active', 1)
            ->select('id', 'name', 'sku', 'barcode', 'buying_price', 'selling_price', 'stock_quantity', 'thumbnail', 'is_unlimited')
            ->orderBy('name')
            ->get();

        $nextInvoiceNo = Purchase::generateInvoiceNumber();

        return view('admin.purchase.create', compact('suppliers', 'products', 'nextInvoiceNo'));
    }

    /**
     * Store a newly created local purchase in storage and increment stock.
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id'         => 'required|exists:suppliers,id',
            'purchase_date'       => 'required|date',
            'payment_method'      => 'required|string',
            'paid_amount'         => 'nullable|numeric|min:0',
            'note'                => 'nullable|string|max:1000',
            'purchase_slip'       => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:10240',
            'items'               => 'required|array|min:1',
            'items.*.product_id'  => 'required|exists:products,id',
            'items.*.quantity'    => 'required|integer|min:1',
            'items.*.unit_price'  => 'required|numeric|min:0',
        ]);

        $slipPath = null;
        if ($request->hasFile('purchase_slip')) {
            $dir = public_path('uploads/purchases');
            if (!File::exists($dir)) {
                File::makeDirectory($dir, 0755, true);
            }
            $filename = time() . '_' . uniqid() . '.' . $request->file('purchase_slip')->getClientOriginalExtension();
            $request->file('purchase_slip')->move($dir, $filename);
            $slipPath = 'uploads/purchases/' . $filename;
        }

        DB::beginTransaction();
        try {
            $totalAmount = 0;
            $itemsData   = [];

            foreach ($request->items as $item) {
                $qty       = (int) $item['quantity'];
                $price     = (float) $item['unit_price'];
                $subtotal  = round($qty * $price, 2);
                $totalAmount += $subtotal;

                $itemsData[] = [
                    'product_id' => $item['product_id'],
                    'quantity'   => $qty,
                    'unit_price' => $price,
                    'subtotal'   => $subtotal,
                ];
            }

            $paidAmount = min($totalAmount, max(0, (float) ($request->paid_amount ?? 0)));
            $dueAmount  = max(0, $totalAmount - $paidAmount);
            $paymentStatus = $dueAmount == 0 ? 'paid' : ($paidAmount > 0 ? 'partial' : 'due');

            $invoiceNo = $request->invoice_no ?: Purchase::generateInvoiceNumber();

            $purchase = Purchase::create([
                'supplier_id'    => $request->supplier_id,
                'invoice_no'     => $invoiceNo,
                'purchase_date'  => $request->purchase_date,
                'total_amount'   => $totalAmount,
                'paid_amount'    => $paidAmount,
                'due_amount'     => $dueAmount,
                'payment_method' => $request->payment_method,
                'payment_status' => $paymentStatus,
                'status'         => 'received',
                'note'           => $request->note,
                'purchase_slip'  => $slipPath,
                'created_by'     => auth()->id(),
            ]);

            $supplier = Supplier::find($request->supplier_id);
            $supplierName = $supplier ? $supplier->name : 'Supplier';

            // Loop items: create purchase items, update product stock & record inventory ledger
            foreach ($itemsData as $row) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id'  => $row['product_id'],
                    'quantity'    => $row['quantity'],
                    'unit_price'  => $row['unit_price'],
                    'subtotal'    => $row['subtotal'],
                    'sub_total'   => $row['subtotal'],
                ]);

                // Atomically update product stock
                $prod = Product::where('id', $row['product_id'])->lockForUpdate()->first();
                if ($prod) {
                    $prevStock = (int) $prod->stock_quantity;
                    $newStock  = $prevStock + $row['quantity'];

                    $prod->update([
                        'stock_quantity' => $newStock,
                        'total_in'       => (int) $prod->total_in + $row['quantity'],
                        'buying_price'   => $row['unit_price'] > 0 ? $row['unit_price'] : $prod->buying_price,
                    ]);

                    // Record Stock In Ledger
                    InventoryLedger::create([
                        'product_id'     => $prod->id,
                        'order_id'       => null,
                        'type'           => 'stock_in',
                        'quantity'       => $row['quantity'],
                        'previous_stock' => $prevStock,
                        'current_stock'  => $newStock,
                        'note'           => "Local Purchase #{$purchase->invoice_no} ({$supplierName})",
                        'created_by'     => auth()->id(),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.purchases.show', $purchase->id)
                ->with('success', "Local Purchase #{$purchase->invoice_no} saved and stock successfully updated!");

        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error recording purchase: ' . $e->getMessage());
        }
    }

    /**
     * Display the purchase invoice / details.
     */
    public function show($id)
    {
        $purchase = Purchase::with(['supplier.user', 'items.product', 'creator'])->findOrFail($id);
        $settings = \App\Models\GenaralSetting::first();

        return view('admin.purchase.show', compact('purchase', 'settings'));
    }

    /**
     * Remove the specified purchase.
     */
    public function destroy($id)
    {
        $purchase = Purchase::with('items')->findOrFail($id);
        if ($purchase->purchase_slip && File::exists(public_path($purchase->purchase_slip))) {
            File::delete(public_path($purchase->purchase_slip));
        }
        $purchase->delete();

        return redirect()->route('admin.purchases.index')
            ->with('success', 'Purchase record deleted.');
    }
}
