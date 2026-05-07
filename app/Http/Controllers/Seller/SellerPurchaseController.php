<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\SellerProduct;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SellerPurchaseController extends Controller
{
    public function stockReport()
    {
        $seller_id = Auth::id();
        $products = SellerProduct::where('seller_id', $seller_id)->get();
        
        // Calculate Purchased (In) for each product
        foreach ($products as $product) {
            $product->purchased_in = PurchaseItem::where('product_id', $product->id)
                ->whereHas('purchase', function($q) use ($seller_id) {
                    $q->where('seller_id', $seller_id)->where('status', 'received');
                })->sum('quantity');
                
            // For Sold (Out), we would check PosInvoices. 
            // Since items are stored as array in PosInvoice, we can sum them up.
            $product->sold_out = 0;
            $invoices = \App\Models\PosInvoice::where('seller_id', $seller_id)->get();
            foreach($invoices as $inv) {
                if(is_array($inv->items)) {
                    foreach($inv->items as $item) {
                        if($item['id'] == $product->id) {
                            $product->sold_out += $item['qty'];
                        }
                    }
                }
            }
        }

        return view('seller.purchase.stock_report', compact('products'));
    }

    public function index()
    {
        $purchases = Purchase::where('seller_id', Auth::id())->latest()->get();
        return view('seller.purchase.invoices', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::where('seller_id', Auth::id())->get();
        $products = SellerProduct::where('seller_id', Auth::id())->get();
        return view('seller.purchase.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'purchase_name' => 'nullable|string',
            'supplier_id'   => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'products'      => 'required|array',
            'products.*.id' => 'required|exists:seller_products,id',
            'products.*.qty' => 'required|integer|min:1',
            'products.*.price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $seller_id = Auth::id();
            
            // Handle image upload
            $purchase_slip = null;
            if ($request->hasFile('purchase_slip')) {
                $file = $request->file('purchase_slip');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = public_path('uploads/purchase');
                if (!File::exists($path)) {
                    File::makeDirectory($path, 0777, true, true);
                }
                $file->move($path, $filename);
                $purchase_slip = 'uploads/purchase/' . $filename;
            }

            $total_amount = 0;
            foreach ($request->products as $p) {
                $total_amount += $p['qty'] * $p['price'];
            }

            $purchase = Purchase::create([
                'seller_id'      => $seller_id,
                'supplier_id'    => $request->supplier_id,
                'purchase_name'  => $request->purchase_name,
                'invoice_no'     => 'PUR-' . time(),
                'purchase_date'  => $request->purchase_date,
                'total_amount'   => $total_amount,
                'paid_amount'    => $request->paid_amount ?? 0,
                'due_amount'     => $total_amount - ($request->paid_amount ?? 0),
                'payment_status' => ($request->paid_amount >= $total_amount) ? 'paid' : (($request->paid_amount > 0) ? 'partial' : 'due'),
                'status'         => 'received', // Auto-receive for now to update stock
                'note'           => $request->note,
                'purchase_slip'  => $purchase_slip,
            ]);

            foreach ($request->products as $p) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id'  => $p['id'],
                    'quantity'    => $p['qty'],
                    'unit_price'  => $p['price'],
                    'sub_total'   => $p['qty'] * $p['price'],
                ]);

                // Update Stock
                $product = SellerProduct::find($p['id']);
                $product->increment('stock_quantity', $p['qty']);
            }

            DB::commit();
            return redirect()->route('seller.purchase.index')->with('success', 'Purchase recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function summary(Request $request)
    {
        $query = Purchase::where('seller_id', Auth::id());

        if ($request->supplier_id) {
            $query->where('supplier_id', $request->supplier_id);
        }
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('purchase_date', [$request->start_date, $request->end_date]);
        }

        $purchases = $query->latest()->get();
        $suppliers = Supplier::where('seller_id', Auth::id())->get();
        
        return view('seller.purchase.summary', compact('purchases', 'suppliers'));
    }

    public function returns()
    {
        $returns = PurchaseReturn::where('seller_id', Auth::id())->latest()->get();
        return view('seller.purchase.returns', compact('returns'));
    }

    public function returnCreate()
    {
        $purchases = Purchase::where('seller_id', Auth::id())->get();
        return view('seller.purchase.return_create', compact('purchases'));
    }

    public function returnStore(Request $request)
    {
        $request->validate([
            'purchase_id' => 'required|exists:purchases,id',
            'return_date' => 'required|date',
            'products'    => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            $total_amount = 0;
            foreach ($request->products as $p) {
                $total_amount += $p['qty'] * $p['price'];
            }

            $return = PurchaseReturn::create([
                'seller_id'    => Auth::id(),
                'purchase_id'  => $request->purchase_id,
                'return_date'  => $request->return_date,
                'total_amount' => $total_amount,
                'note'         => $request->note,
            ]);

            foreach ($request->products as $p) {
                PurchaseReturnItem::create([
                    'purchase_return_id' => $return->id,
                    'product_id'         => $p['id'],
                    'quantity'           => $p['qty'],
                    'unit_price'         => $p['price'],
                    'sub_total'          => $p['qty'] * $p['price'],
                ]);

                // Update Stock
                $product = SellerProduct::find($p['id']);
                $product->decrement('stock_quantity', $p['qty']);
            }

            DB::commit();
            return redirect()->route('seller.purchase.returns')->with('success', 'Purchase return recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
    public function getPurchaseDetails($id) {
        $purchase = Purchase::with('items.product')->where('seller_id', Auth::id())->findOrFail($id);
        return response()->json($purchase);
    }
}
