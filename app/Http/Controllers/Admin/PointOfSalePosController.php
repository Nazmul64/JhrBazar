<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alltaxe;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\GenaralSetting;
use App\Models\PosInvoice;
use App\Models\Pointofsalepo;
use App\Models\Product;
use App\Models\Promocode;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class PointOfSalePosController extends Controller
{
    // ──────────────────────────────────────────────────────────────
    //  INDEX — Main POS view
    // ──────────────────────────────────────────────────────────────
    public function index()
    {
        $brands     = Brand::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $taxes      = Alltaxe::where('status', 1)->get();
        $settings   = GenaralSetting::first();

        $customers = Customer::with('user')
                        ->orderBy('id', 'desc')
                        ->get();

        return view('admin.pointofsalepos.index',
            compact('brands', 'categories', 'taxes', 'settings', 'customers'));
    }

    // ──────────────────────────────────────────────────────────────
    //  AJAX — Products (paginated + filterable)
    // ──────────────────────────────────────────────────────────────
    public function getProducts(Request $request): JsonResponse
    {
        $query = Product::where('is_active', true)
                        ->where('stock_quantity', '>', 0);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('name',    'like', "%{$s}%")
                  ->orWhere('sku',     'like', "%{$s}%")
                  ->orWhere('barcode', 'like', "%{$s}%");
            });
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query
            ->select([
                'id', 'name', 'sku', 'barcode',
                'selling_price', 'discount_price',
                'stock_quantity', 'thumbnail',
                'size', 'color', 'unit', 'short_description',
            ])
            ->orderBy('name')
            ->paginate(12);

        return response()->json($products);
    }

    // ──────────────────────────────────────────────────────────────
    //  AJAX — Store New Customer
    // ──────────────────────────────────────────────────────────────
    public function storeCustomer(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'first_name'    => 'required|string|max:100',
                'last_name'     => 'nullable|string|max:100',
                'phone'         => 'required|string|max:20|unique:users,phone',
                'email'         => 'required|email|max:191|unique:users,email',
                'password'      => 'required|string|min:6|confirmed',
                'gender'        => 'nullable|in:male,female,other',
                'date_of_birth' => 'nullable|date',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors'  => $e->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $fullName = trim($request->first_name . ' ' . ($request->last_name ?? ''));

            $user = User::create([
                'name'     => $fullName,
                'phone'    => $request->phone,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => 'customer',
            ]);

            $customer = Customer::create([
                'user_id'       => $user->id,
                'first_name'    => $request->first_name,
                'last_name'     => $request->last_name    ?? null,
                'gender'        => $request->gender       ?? null,
                'date_of_birth' => $request->date_of_birth ?? null,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'id'      => $customer->id,
                'name'    => $fullName,
                'phone'   => $request->phone,
                'email'   => $request->email,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create customer: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ──────────────────────────────────────────────────────────────
    //  AJAX — Apply Coupon / Promo Code
    // ──────────────────────────────────────────────────────────────
    public function applyCoupon(Request $request): JsonResponse
    {
        $request->validate([
            'coupon_code' => 'required|string|max:100',
            'sub_total'   => 'required|numeric|min:0',
        ]);

        $coupon = Promocode::where('code', $request->coupon_code)
            ->where('status', 1)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>=', now());
            })
            ->first();

        if (! $coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired coupon code.',
            ]);
        }

        if ($coupon->min_order && $request->sub_total < $coupon->min_order) {
            return response()->json([
                'success' => false,
                'message' => "Minimum order ৳{$coupon->min_order} required for this coupon.",
            ]);
        }

        $discount = $coupon->type === 'percent'
            ? round($request->sub_total * $coupon->value / 100, 2)
            : (float) $coupon->value;

        $discount = min($discount, (float) $request->sub_total);

        return response()->json([
            'success'  => true,
            'discount' => $discount,
            'message'  => "Coupon \"{$coupon->code}\" applied! −৳{$discount} discount.",
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    //  AJAX — Place Order (completed)
    // ──────────────────────────────────────────────────────────────
    public function placeOrder(Request $request): JsonResponse
    {
        $request->validate([
            'items'           => 'required|array|min:1',
            'items.*.id'      => 'required|exists:products,id',
            'items.*.qty'     => 'required|integer|min:1',
            'payment_method'  => 'nullable|string|max:50',
            'received_amount' => 'nullable|numeric|min:0',
        ]);

        return $this->processOrder($request, 'completed');
    }

    // ──────────────────────────────────────────────────────────────
    //  AJAX — Save Draft
    // ──────────────────────────────────────────────────────────────
    public function draft(Request $request): JsonResponse
    {
        $request->validate([
            'items'       => 'required|array|min:1',
            'items.*.id'  => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        return $this->processOrder($request, 'draft');
    }

    // ──────────────────────────────────────────────────────────────
    //  INVOICE VIEW
    // ──────────────────────────────────────────────────────────────
    public function invoice(PosInvoice $invoice)
    {
        $invoice->load('customer.user', 'order');
        $settings = GenaralSetting::first();

        return view('admin.invoice.invoice',
            compact('invoice', 'settings'));
    }

    // ──────────────────────────────────────────────────────────────
    //  POS SALES HISTORY — Index (paginated list)
    // ──────────────────────────────────────────────────────────────
    public function salesIndex(Request $request)
    {
        $query = PosInvoice::with('customer.user')
            ->orderBy('id', 'desc');

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('invoice_number', 'like', "%{$s}%")
                  ->orWhereHas('customer.user', fn($u) =>
                      $u->where('name',  'like', "%{$s}%")
                        ->orWhere('phone', 'like', "%{$s}%")
                  );
            });
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $invoices    = $query->paginate(15)->withQueryString();
        $settings    = GenaralSetting::first();
        $totalSales  = PosInvoice::sum('grand_total');
        $totalOrders = PosInvoice::count();
        $todaySales  = PosInvoice::whereDate('created_at', today())->sum('grand_total');
        $todayOrders = PosInvoice::whereDate('created_at', today())->count();

        return view('admin.pointofsalepos.salesistory',
            compact('invoices', 'settings', 'totalSales', 'totalOrders', 'todaySales', 'todayOrders'));
    }

    // ──────────────────────────────────────────────────────────────
    //  POS SALES HISTORY — Show single invoice detail
    // ──────────────────────────────────────────────────────────────
    public function salesShow(PosInvoice $invoice)
    {
        $invoice->load('customer.user', 'order');
        $settings = GenaralSetting::first();

        return view('admin.pointofsalepos.show',
            compact('invoice', 'settings'));
    }

    // ──────────────────────────────────────────────────────────────
    //  POS SALES HISTORY — Update order status (AJAX)
    // ──────────────────────────────────────────────────────────────
    public function updateStatus(Request $request, PosInvoice $invoice): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:completed,delivered,cancelled,draft',
        ]);

        if ($invoice->order) {
            $invoice->order->update(['status' => $request->status]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.',
            'status'  => $request->status,
        ]);
    }

    // ══════════════════════════════════════════════════════════════
    //  POS DRAFT — Index (paginated list)
    // ══════════════════════════════════════════════════════════════
    public function draftIndex(Request $request)
    {
        $query = Pointofsalepo::with('customer.user')
            ->where('status', 'draft')
            ->orderBy('id', 'desc');

        // Search by customer name or phone
        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->whereHas('customer.user', fn($u) =>
                    $u->where('name',  'like', "%{$s}%")
                      ->orWhere('phone', 'like', "%{$s}%")
                );
            });
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $drafts   = $query->paginate(15)->withQueryString();
        $settings = GenaralSetting::first();

        // Summary stats
        $totalDrafts     = Pointofsalepo::where('status', 'draft')->count();
        $totalDraftValue = Pointofsalepo::where('status', 'draft')->sum('grand_total');
        $todayDrafts     = Pointofsalepo::where('status', 'draft')
                            ->whereDate('created_at', today())->count();
        $totalItems      = Pointofsalepo::where('status', 'draft')->get()
                            ->sum(fn($d) => count($d->items ?? []));

        return view('admin.pointofsalepos.draft',
            compact('drafts', 'settings', 'totalDrafts', 'totalDraftValue', 'todayDrafts', 'totalItems'));
    }

    // ══════════════════════════════════════════════════════════════
    //  POS DRAFT — Delete a draft order
    // ══════════════════════════════════════════════════════════════
    public function draftDestroy(Pointofsalepo $draft)
    {
        // Only allow deleting draft orders
        if ($draft->status !== 'draft') {
            return redirect()->route('admin.pointofsalepos.draft.index')
                ->with('error', 'Only draft orders can be deleted.');
        }

        $draft->delete();

        return redirect()->route('admin.pointofsalepos.draft.index')
            ->with('success', 'Draft order deleted successfully.');
    }

    // ══════════════════════════════════════════════════════════════
    //  AJAX — Get Draft Data (for loading into POS cart)
    //  Called when ?draft_id=X in POS index URL
    // ══════════════════════════════════════════════════════════════
    public function getDraft(Pointofsalepo $draft): JsonResponse
    {
        if ($draft->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Not a draft order.',
            ], 422);
        }

        return response()->json([
            'success'     => true,
            'items'       => $draft->items,
            'customer_id' => $draft->customer_id,
            'discount'    => $draft->discount,
            'coupon_code' => $draft->coupon_code,
            'note'        => $draft->note,
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    //  PRIVATE — Core order processor
    // ──────────────────────────────────────────────────────────────
    private function processOrder(Request $request, string $status): JsonResponse
    {
        DB::beginTransaction();
        try {
            $productIds = collect($request->items)->pluck('id');
            $products   = Product::whereIn('id', $productIds)->get()->keyBy('id');

            $itemSnapshots = [];
            $subTotal      = 0;

            foreach ($request->items as $item) {
                $product = $products->get($item['id']);
                if (! $product) continue;

                $price     = (float)(
                    ((float)($product->discount_price ?? 0)) > 0
                        ? $product->discount_price
                        : $product->selling_price
                );
                $qty       = max(1, (int)$item['qty']);
                $lineTotal = round($price * $qty, 2);
                $subTotal += $lineTotal;

                $itemSnapshots[] = [
                    'id'                => $product->id,
                    'name'              => $product->name,
                    'sku'               => $product->sku               ?? null,
                    'barcode'           => $product->barcode           ?? null,
                    'thumbnail'         => $product->thumbnail         ?? null,
                    'selling_price'     => (float)$product->selling_price,
                    'discount_price'    => (float)($product->discount_price ?? 0),
                    'price'             => $price,
                    'qty'               => $qty,
                    'size'              => $product->size              ?? null,
                    'color'             => $product->color             ?? null,
                    'unit'              => $product->unit              ?? null,
                    'short_description' => $product->short_description ?? null,
                    'line_total'        => $lineTotal,
                ];

                if ($status === 'completed') {
                    $product->decrement('stock_quantity', $qty);
                }
            }

            if (empty($itemSnapshots)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'No valid products found.',
                ], 422);
            }

            $discount  = max(0, (float)($request->discount ?? 0));
            $afterDisc = max(0, $subTotal - $discount);

            $taxes        = Alltaxe::where('status', 1)->get();
            $taxBreakdown = [];
            $totalTax     = 0;

            foreach ($taxes as $tax) {
                $taxAmt         = round($afterDisc * $tax->percentage / 100, 2);
                $totalTax      += $taxAmt;
                $taxBreakdown[] = [
                    'name'   => $tax->name,
                    'rate'   => $tax->percentage,
                    'amount' => $taxAmt,
                ];
            }

            $grandTotal     = round($afterDisc + $totalTax, 2);
            $receivedAmount = max(0, (float)($request->received_amount ?? 0));
            $changeAmount   = max(0, round($receivedAmount - $grandTotal, 2));

            $order = Pointofsalepo::create([
                'customer_id'    => $request->customer_id ?? null,
                'items'          => $itemSnapshots,
                'sub_total'      => $subTotal,
                'discount'       => $discount,
                'tax_amount'     => $totalTax,
                'grand_total'    => $grandTotal,
                'payment_method' => $request->payment_method ?? 'cash',
                'coupon_code'    => $request->coupon_code    ?? null,
                'note'           => $request->note           ?? null,
                'status'         => $status,
            ]);

            $invoiceUrl = null;

            if ($status === 'completed') {
                $invoice = PosInvoice::create([
                    'invoice_number'   => PosInvoice::generateInvoiceNumber(),
                    'pointofsalepo_id' => $order->id,
                    'customer_id'      => $request->customer_id ?? null,
                    'items'            => $itemSnapshots,
                    'sub_total'        => $subTotal,
                    'discount'         => $discount,
                    'tax_amount'       => $totalTax,
                    'delivery_charge'  => 0,
                    'grand_total'      => $grandTotal,
                    'payment_method'   => $request->payment_method ?? 'cash',
                    'received_amount'  => $receivedAmount,
                    'change_amount'    => $changeAmount,
                    'coupon_code'      => $request->coupon_code ?? null,
                    'note'             => $request->note        ?? null,
                    'tax_breakdown'    => $taxBreakdown,
                ]);

                $invoiceUrl = route('admin.pointofsalepos.invoice', $invoice->id);
            }

            DB::commit();

            return response()->json([
                'success'     => true,
                'order_id'    => $order->id,
                'invoice_url' => $invoiceUrl,
                'message'     => $status === 'draft'
                    ? 'Draft saved successfully.'
                    : 'Order placed successfully.',
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
