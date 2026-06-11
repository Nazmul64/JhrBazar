<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SteadfastCourier;
use App\Models\PathaoCourier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\PosInvoice;
use App\Models\GenaralSetting;
use App\Models\OrderStatusLog;
use Illuminate\Support\Facades\DB;
use App\Services\FraudDetectionService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class OrderHubController extends Controller
{
    public function __construct(private readonly FraudDetectionService $fraudService) {}

    /**
     * Perform Fraud Check via External API
     */
    public function fraudCheck(Request $request)
    {
        $request->validate(['phone' => 'required']);
        $externalResult = $this->fraudService->checkExternalCourierApi($request->phone);
        $internalResult = $this->fraudService->analyzeCourierHistory($request->phone, null);

        return response()->json([
            'success'  => true,
            'external' => $externalResult,
            'internal' => $internalResult
        ]);
    }

    /**
     * Display a listing of orders, optionally filtered by status.
     */
    public function index(Request $request, $status = 'all')
    {
        $query = PosInvoice::with(['customer.user', 'order.staff', 'seller'])
            ->orderBy('id', 'desc');

        // Status Filtering
        if ($status && $status !== 'all') {
            $query->whereHas('order', function ($q) use ($status) {
                $q->where('status', $status);
            });
        }

        // Search Filtering
        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('invoice_number', 'like', "%{$s}%")
                  ->orWhereHas('customer.user', function ($u) use ($s) {
                      $u->where('name', 'like', "%{$s}%")
                        ->orWhere('phone', 'like', "%{$s}%");
                  });
            });
        }

        $orders = $query->paginate(15)->withQueryString();
        $settings = GenaralSetting::first();
        $staffs = User::whereIn('role', ['employee', 'manager'])->get();

        $steadfast = SteadfastCourier::first();
        $pathao = PathaoCourier::first();

        // Summary Stats (Global for Admin)
        $totalOrders     = PosInvoice::count();
        $pendingOrders   = PosInvoice::whereHas('order', fn($q) => $q->where('status', 'pending'))->count();
        $processingOrders= PosInvoice::whereHas('order', fn($q) => $q->where('status', 'processing'))->count();
        $shippedOrders   = PosInvoice::whereHas('order', fn($q) => $q->where('status', 'shipped'))->count();
        $deliveredOrders = PosInvoice::whereHas('order', fn($q) => $q->where('status', 'delivered'))->count();
        $cancelledOrders = PosInvoice::whereHas('order', fn($q) => $q->where('status', 'cancelled'))->count();

        return view('admin.orders.index', compact(
            'orders',
            'status',
            'settings',
            'staffs',
            'steadfast',
            'pathao',
            'totalOrders',
            'pendingOrders',
            'processingOrders',
            'shippedOrders',
            'deliveredOrders',
            'cancelledOrders'
        ));
    }

    /**
     * Show the form for creating a new order.
     */
    public function create()
    {
        $products = \App\Models\Product::where('is_active', 1)->get();
        $customers = \App\Models\Customer::with('user')->get();
        $shippingCharges = \App\Models\ShippingCharge::where('status', 1)->get();
        $settings = GenaralSetting::first();

        return view('admin.orders.create', compact('products', 'customers', 'shippingCharges', 'settings'));
    }

    /**
     * Store a newly created order.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name'    => 'required|string',
            'customer_phone'   => 'required|string',
            'customer_address' => 'required|string',
            'items'            => 'required|array|min:1',
        ]);

        DB::beginTransaction();
        try {
            // 1. Handle Customer
            $user = User::where('phone', $request->customer_phone)->first();
            if (!$user) {
                $user = User::create([
                    'name'     => $request->customer_name,
                    'phone'    => $request->customer_phone,
                    'email'    => $request->customer_phone . '@jhrbazar.com', // Dummy email to satisfy DB constraint
                    'role'     => 'customer',
                    'password' =>Hash::make($request->customer_phone),
                ]);
            }

            $customer = \App\Models\Customer::where('user_id', $user->id)->first();
            if (!$customer) {
                $customer = \App\Models\Customer::create([
                    'user_id'    => $user->id,
                    'first_name' => $request->customer_name,
                    'last_name'  => '',
                    'address'    => $request->customer_address,
                ]);
            } else {
                $customer->update([
                    'first_name' => $request->customer_name,
                    'address'    => $request->customer_address
                ]);
            }

            // 2. Process Items
            $subTotal = 0;
            $itemSnapshots = [];
            foreach ($request->items as $item) {
                $product = \App\Models\Product::find($item['id']);
                if (!$product) continue;

                $qty = (int)$item['qty'];
                $itemPrice = (float)$item['price'];
                $itemDiscount = (float)($item['discount'] ?? 0);
                $lineTotal = ($itemPrice * $qty) - $itemDiscount;
                $subTotal += $lineTotal;

                $itemSnapshots[] = [
                    'id'             => $product->id,
                    'name'           => $product->name,
                    'sku'            => $product->sku,
                    'thumbnail'      => $product->thumbnail,
                    'price'          => $itemPrice,
                    'qty'            => $qty,
                    'discount'       => $itemDiscount,
                    'line_total'     => $lineTotal,
                ];

                // Decrement Stock
                $product->decrement('stock_quantity', $qty);
            }

            $shipping = (float)($request->shipping_charge ?? 0);
            $grandTotal = $subTotal + $shipping;

            // 3. Create POS Order Record (Pointofsalepo)
            $order = \App\Models\Pointofsalepo::create([
                'customer_id'    => $customer->id,
                'items'          => $itemSnapshots,
                'sub_total'      => $subTotal,
                'grand_total'    => $grandTotal,
                'delivery_charge'=> $shipping,
                'payment_method' => $request->payment_method ?? 'cod',
                'payment_status' => $request->payment_status ?? 'pending',
                'status'         => 'pending', // Set to pending for admin hub orders
                'note'           => "Area: " . ($request->delivery_area ?? 'N/A') . ". " . ($request->trx_id ? "TrxID: " . $request->trx_id : ""),
            ]);

            // 4. Create Invoice
            $invoice = PosInvoice::create([
                'invoice_number'   => PosInvoice::generateInvoiceNumber(),
                'pointofsalepo_id' => $order->id,
                'customer_id'      => $customer->id,
                'items'            => $itemSnapshots,
                'sub_total'        => $subTotal,
                'delivery_charge'  => $shipping,
                'grand_total'      => $grandTotal,
                'payment_method'   => $request->payment_method ?? 'cod',
                'note'             => $request->trx_id ? "TrxID: " . $request->trx_id : null,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully!',
                'invoice_url' => route('admin.pointofsalepos.invoice', $invoice->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show single order detail.
     */
    public function show($id)
    {
        $invoice = PosInvoice::with(['customer.user', 'order.staff', 'seller', 'order.statusLogs.changedBy'])->findOrFail($id);
        $settings = GenaralSetting::first();
        $staffs = \App\Models\User::whereIn('role', ['employee', 'manager', 'staff'])->get();
        $allProducts = \App\Models\Product::where('is_active', 1)->orderBy('name')->get();
        $shippingCharges = \App\Models\ShippingCharge::where('status', 1)->orderBy('area_name')->get();

        return view('admin.pointofsalepos.show', compact('invoice', 'settings', 'staffs', 'allProducts', 'shippingCharges'));
    }

    /**
     * Update order details (A-Z details update including customer, items, shipping, discounts & stock).
     */
    public function updateOrder(Request $request, $id)
    {
        $request->validate([
            'customer_name'    => 'required|string',
            'customer_phone'   => 'required|string',
            'customer_address' => 'required|string',
            'delivery_charge'  => 'required|numeric|min:0',
            'discount'         => 'required|numeric|min:0',
            'items'            => 'required|array|min:1',
            'items.*.id'       => 'required|integer',
            'items.*.qty'      => 'required|integer|min:1',
            'items.*.price'    => 'required|numeric|min:0',
            'items.*.size'     => 'nullable|string',
            'items.*.color'    => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $invoice = PosInvoice::with('order')->findOrFail($id);
            $order = $invoice->order;

            // 1. Update/Link Customer
            $phone = trim($request->customer_phone);
            $user = User::where('phone', $phone)->first();
            if (!$user) {
                $user = User::create([
                    'name'     => $request->customer_name,
                    'phone'    => $phone,
                    'email'    => $phone . '@jhrbazar.com',
                    'role'     => 'customer',
                    'password' => Hash::make($phone),
                ]);
            } else {
                $user->update([
                    'name'    => $request->customer_name,
                    'address' => $request->customer_address,
                ]);
            }

            $customer = \App\Models\Customer::where('user_id', $user->id)->first();
            if (!$customer) {
                $customer = \App\Models\Customer::create([
                    'user_id'    => $user->id,
                    'first_name' => $request->customer_name,
                    'last_name'  => '',
                    'address'    => $request->customer_address,
                ]);
            } else {
                $customer->update([
                    'first_name' => $request->customer_name,
                    'address'    => $request->customer_address,
                ]);
            }

            // 2. Re-stock previous items
            if ($order && is_array($order->items)) {
                foreach ($order->items as $oldItem) {
                    $prod = \App\Models\Product::find($oldItem['id']);
                    if ($prod) {
                        $prod->increment('stock_quantity', (int)$oldItem['qty']);
                    }
                }
            }

            // 3. Process new items and decrement stock
            $subTotal = 0;
            $itemSnapshots = [];
            foreach ($request->items as $item) {
                $product = \App\Models\Product::find($item['id']);
                if (!$product) continue;

                $qty = (int)$item['qty'];
                $itemPrice = (float)$item['price'];
                $lineTotal = $itemPrice * $qty;
                $subTotal += $lineTotal;

                $itemSnapshots[] = [
                    'id'         => $product->id,
                    'name'       => $product->name,
                    'sku'        => $product->sku,
                    'thumbnail'  => $product->thumbnail,
                    'price'      => $itemPrice,
                    'qty'        => $qty,
                    'size'       => $item['size'] ?? null,
                    'color'      => $item['color'] ?? null,
                    'line_total' => $lineTotal,
                ];

                // Decrement Stock
                $product->decrement('stock_quantity', $qty);
            }

            $shipping = (float)$request->delivery_charge;
            $discount = (float)$request->discount;
            $grandTotal = ($subTotal + $shipping) - $discount;

            // 4. Update Parent Order
            if ($order) {
                $order->update([
                    'customer_id'     => $customer->id,
                    'items'           => $itemSnapshots,
                    'sub_total'       => $subTotal,
                    'discount'        => $discount,
                    'delivery_charge' => $shipping,
                    'grand_total'     => $grandTotal,
                    'phone'           => $phone,
                ]);
            }

            // 5. Update Invoice
            $invoice->update([
                'customer_id'     => $customer->id,
                'items'           => $itemSnapshots,
                'sub_total'       => $subTotal,
                'discount'        => $discount,
                'delivery_charge' => $shipping,
                'grand_total'     => $grandTotal,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order updated successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating order: ' . $e->getMessage()
            ], 500);
        }
    }

    protected function getSellerOrderTransactionNet(PosInvoice $invoice)
    {
        if (!$invoice->seller) {
            return 0;
        }

        return \App\Models\SellerTransaction::where('seller_id', $invoice->seller->id)
            ->where('description', 'like', '%Order #' . $invoice->invoice_number . '%')
            ->sum('net_amount');
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, $id)
    {
        if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'manager'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $request->validate(['status' => 'required|string']);
        // Map UI 'complete' to internal 'delivered' status
        if ($request->status === 'complete') {
            $request->merge(['status' => 'delivered']);
        }
        $invoice = PosInvoice::with('order', 'seller')->findOrFail($id);

        if ($invoice->order) {
            $previousStatus = $invoice->order->status;

            DB::beginTransaction();
            try {
                // Update Order Status
                $invoice->order->update(['status' => $request->status]);

                // Log Status Change
                OrderStatusLog::create([
                    'order_id'        => $invoice->order->id,
                    'previous_status' => $previousStatus,
                    'current_status'  => $request->status,
                    'changed_by'      => auth()->id(),
                    'note'            => $request->note ?? 'Status updated by admin',
                ]);

                // Handle Delivered Status for Seller Balance
                if ($request->status === 'delivered' && $previousStatus !== 'delivered') {
                    if ($invoice->seller) {
                        $existingNet = $this->getSellerOrderTransactionNet($invoice);
                        if ($existingNet <= 0) {
                            $settings = GenaralSetting::first();
                            $commissionPercent = $settings->seller_commission ?? 10;
                            $commissionAmount = ($invoice->grand_total * $commissionPercent) / 100;
                            $netAmount = $invoice->grand_total - $commissionAmount;

                            $invoice->seller->increment('balance', $netAmount);

                            \App\Models\SellerTransaction::create([
                                'seller_id'      => $invoice->seller->id,
                                'transaction_id' => 'EARN-' . strtoupper(Str::random(10)),
                                'type'           => 'earning',
                                'amount'         => $invoice->grand_total,
                                'commission'     => $commissionAmount,
                                'net_amount'     => $netAmount,
                                'status'         => 'completed',
                                'description'    => 'Earning from Order #' . $invoice->invoice_number,
                            ]);
                        }
                    }
                }

                // Reversal: If previous was Delivered and new is NOT Delivered (e.g. Returned/Cancelled)
                if ($previousStatus === 'delivered' && $request->status !== 'delivered') {
                    if ($invoice->seller) {
                        $settings = GenaralSetting::first();
                        $commissionPercent = $settings->seller_commission ?? 10;
                        $commissionAmount = ($invoice->grand_total * $commissionPercent) / 100;
                        $netAmount = $invoice->grand_total - $commissionAmount;

                        $invoice->seller->decrement('balance', $netAmount);

                        \App\Models\SellerTransaction::create([
                            'seller_id'      => $invoice->seller->id,
                            'transaction_id' => 'REV-' . strtoupper(Str::random(10)),
                            'type'           => 'adjustment', // Reversal/Adjustment
                            'amount'         => -$invoice->grand_total,
                            'commission'     => -$commissionAmount,
                            'net_amount'     => -$netAmount,
                            'status'         => 'completed',
                            'description'    => 'Reversal from Order #' . $invoice->invoice_number . ' (Status: ' . $request->status . ')',
                        ]);
                    }
                }

                DB::commit();
                // Determine success message
                if ($request->status === 'delivered') {
                    $msg = 'Order Completed & Seller Balance Added Successfully';
                } else {
                    $msg = 'Status updated to ' . ucfirst($request->status);
                }
                return response()->json(['success' => true, 'message' => $msg]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
            }
        }

        return response()->json(['success' => false, 'message' => 'Order link not found.'], 422);
    }


    /**
     * Assign staff to order.
     */
    public function assignStaff(Request $request, $id)
    {
        $request->validate(['staff_id' => 'required|exists:users,id']);
        $invoice = PosInvoice::findOrFail($id);

        if ($invoice->order) {
            $invoice->order->update(['staff_id' => $request->staff_id]);
            $staffName = User::find($request->staff_id)->name;
            return response()->json(['success' => true, 'message' => 'Order assigned to ' . $staffName]);
        }
        return response()->json(['success' => false, 'message' => 'Order link not found.'], 422);
    }

    /**
     * Update payment status.
     */
    public function updatePaymentStatus(Request $request, $id)
    {
        $request->validate(['payment_status' => 'required|string']);
        $invoice = PosInvoice::findOrFail($id);

        if ($invoice->order) {
            $invoice->order->update(['payment_status' => $request->payment_status]);
            return response()->json(['success' => true, 'message' => 'Payment status updated to ' . ucfirst($request->payment_status)]);
        }
        return response()->json(['success' => false, 'message' => 'Order link not found.'], 422);
    }

    /**
     * Bulk Actions.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|string',
            'ids'    => 'required|array|min:1'
        ]);

        $ids = $request->ids;
        $action = $request->action;
        $invoices = PosInvoice::with('order', 'seller')->whereIn('id', $ids)->get();

        switch ($action) {
            case 'delete':
                foreach ($invoices as $inv) {
                    if ($inv->order) $inv->order->delete();
                    $inv->delete();
                }
                return response()->json(['success' => true, 'message' => 'Selected orders deleted.']);

            case 'steadfast':
                return $this->bulkSendToSteadfast($invoices);

            case 'pathao':
                return $this->bulkSendToPathao($request, $invoices);

            default:
                // For bulk status updates (e.g. status:pending, status:delivered)
                if (str_starts_with($action, 'status:')) {
                    $newStatus = str_replace('status:', '', $action);

                    DB::beginTransaction();
                    try {
                        foreach ($invoices as $inv) {
                            if ($inv->order) {
                                $previousStatus = $inv->order->status;
                                if ($previousStatus === $newStatus) continue;

                                $inv->order->update(['status' => $newStatus]);

                                OrderStatusLog::create([
                                    'order_id'        => $inv->order->id,
                                    'previous_status' => $previousStatus,
                                    'current_status'  => $newStatus,
                                    'changed_by'      => auth()->id(),
                                    'note'            => 'Bulk status update by admin',
                                ]);

                                if ($newStatus === 'delivered' && $previousStatus !== 'delivered') {
                                    if ($inv->seller) {
                                        $existingNet = $this->getSellerOrderTransactionNet($inv);
                                        if ($existingNet <= 0) {
                                            $settings = GenaralSetting::first();
                                            $commissionPercent = $settings->seller_commission ?? 10;
                                            $commissionAmount = ($inv->grand_total * $commissionPercent) / 100;
                                            $netAmount = $inv->grand_total - $commissionAmount;

                                            $inv->seller->increment('balance', $netAmount);

                                            \App\Models\SellerTransaction::create([
                                                'seller_id'      => $inv->seller->id,
                                                'transaction_id' => 'EARN-' . strtoupper(str_random(10)),
                                                'type'           => 'earning',
                                                'amount'         => $inv->grand_total,
                                                'commission'     => $commissionAmount,
                                                'net_amount'     => $netAmount,
                                                'status'         => 'completed',
                                                'description'    => 'Bulk earning from Order #' . $inv->invoice_number,
                                            ]);
                                        }
                                    }
                                }
                            }
                        }
                        DB::commit();
                        return response()->json(['success' => true, 'message' => 'Selected orders updated to ' . ucfirst($newStatus)]);
                    } catch (\Exception $e) {
                        DB::rollBack();
                        return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
                    }
                }
        }

        return response()->json(['success' => false, 'message' => 'Invalid action.'], 422);
    }

    /**
     * Send to Steadfast (Bulk).
     */
    private function bulkSendToSteadfast($invoices)
    {
        $gateway = SteadfastCourier::first();
        if (!$gateway || !$gateway->status) {
            return response()->json(['success' => false, 'message' => 'Steadfast Courier is not active or configured.'], 422);
        }

        $successCount = 0;
        $errors = [];

        foreach ($invoices as $inv) {
            if (!$inv->order) continue;

            // Skip if already sent
            if ($inv->order->steadfast_order_id) continue;

            $response = Http::withHeaders([
                'Api-Key' => $gateway->api_key,
                'Secret-Key' => $gateway->secret_key,
                'Content-Type' => 'application/json'
            ])->post($gateway->url, [
                'invoice' => $inv->invoice_number,
                'recipient_name' => $inv->customer?->user?->name ?? 'Customer',
                'recipient_phone' => $inv->customer?->user?->phone ?? '',
                'recipient_address' => $inv->customer?->address ?? 'N/A',
                'cod_amount' => $inv->grand_total,
                'note' => $inv->note ?? ''
            ]);

            if ($response->successful() && $response->json('status') == 200) {
                $inv->order->update([
                    'steadfast_order_id' => $response->json('order.consignment_id'),
                    'courier_name' => 'Steadfast',
                    'courier_status' => 'sent'
                ]);
                $successCount++;
            } else {
                $errors[] = "Invoice {$inv->invoice_number}: " . ($response->json('message') ?? 'Unknown Error');
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully sent {$successCount} orders to Steadfast.",
            'errors' => $errors
        ]);
    }

    /**
     * Send to Pathao (Bulk).
     */
    private function bulkSendToPathao($request, $invoices)
    {
        $gateway = PathaoCourier::first();
        if (!$gateway || !$gateway->status) {
            return response()->json(['success' => false, 'message' => 'Pathao Courier is not active or configured.'], 422);
        }

        // Logic for Pathao token and order creation would go here
        $gateway = PathaoCourier::first();
        if (!$gateway || !$gateway->status) {
            return response()->json(['success' => false, 'message' => 'Pathao Courier is not active or configured.'], 422);
        }

        $token = $this->getPathaoToken($gateway);
        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Failed to authenticate with Pathao.'], 422);
        }

        $successCount = 0;
        $errors = [];

        foreach ($invoices as $inv) {
            if (!$inv->order || $inv->order->pathao_consignment_id) continue;

            $response = Http::withToken($token)->post($gateway->base_url . '/aladdin/api/v1/orders', [
                'store_id'            => $request->store_id,
                'merchant_order_id'   => $inv->invoice_number,
                'recipient_name'      => $inv->customer?->user?->name ?? 'Customer',
                'recipient_phone'     => $inv->customer?->user?->phone ?? '',
                'recipient_address'   => $inv->customer?->address ?? 'N/A',
                'recipient_city'      => $request->city_id,
                'recipient_zone'      => $request->zone_id,
                'recipient_area'      => $request->area_id,
                'delivery_type'       => 48, // Standard
                'item_type'           => 2,  // Parcel
                'special_instruction' => $inv->note ?? '',
                'item_quantity'       => $inv->total_qty,
                'item_weight'         => 0.5,
                'amount_to_collect'   => $inv->grand_total,
                'item_description'    => 'Order #' . $inv->invoice_number
            ]);

            if ($response->successful() && $response->json('type') == 'success') {
                $inv->order->update([
                    'pathao_consignment_id' => $response->json('data.consignment_id'),
                    'courier_name' => 'Pathao',
                    'courier_status' => 'sent'
                ]);
                $successCount++;
            } else {
                $errors[] = "Invoice {$inv->invoice_number}: " . ($response->json('message') ?? 'Unknown Error');
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully sent {$successCount} orders to Pathao.",
            'errors' => $errors
        ]);
    }

    /**
     * Bulk Generate Invoice.
     */
    public function bulkGenerateInvoice(Request $request)
    {
        $ids = explode(',', $request->ids);
        $invoices = PosInvoice::with(['customer.user', 'order', 'seller'])->whereIn('id', $ids)->get();
        $settings = GenaralSetting::first();

        return view('admin.invoice.bulk_invoice', compact('invoices', 'settings'));
    }

    /**
     * Pathao API Helpers.
     */
    private function getPathaoToken($gateway)
    {
        $response = Http::post($gateway->base_url . '/aladdin/api/v1/issue-token', [
            'client_id'     => $gateway->client_id,
            'client_secret' => $gateway->client_secret,
            'username'      => $gateway->username,
            'password'      => $gateway->password,
            'grant_type'    => $gateway->grant_type ?? 'password',
        ]);

        return $response->successful() ? $response->json('access_token') : null;
    }

    public function getPathaoCities()
    {
        $gateway = PathaoCourier::first();
        $token = $this->getPathaoToken($gateway);
        $response = Http::withToken($token)->get($gateway->base_url . '/aladdin/api/v1/cities');
        return response()->json($response->json('data.data') ?? []);
    }

    public function getPathaoZones($cityId)
    {
        $gateway = PathaoCourier::first();
        $token = $this->getPathaoToken($gateway);
        $response = Http::withToken($token)->get($gateway->base_url . "/aladdin/api/v1/cities/{$cityId}/zone-list");
        return response()->json($response->json('data.data') ?? []);
    }

    public function getPathaoAreas($zoneId)
    {
        $gateway = PathaoCourier::first();
        $token = $this->getPathaoToken($gateway);
        $response = Http::withToken($token)->get($gateway->base_url . "/aladdin/api/v1/zones/{$zoneId}/area-list");
        return response()->json($response->json('data.data') ?? []);
    }

    public function getPathaoStores()
    {
        $gateway = PathaoCourier::first();
        $token = $this->getPathaoToken($gateway);
        $response = Http::withToken($token)->get($gateway->base_url . '/aladdin/api/v1/stores');
        return response()->json($response->json('data.data') ?? []);
    }

    /**
     * Display staff assignments for orders.
     */
    public function staffAssignments(Request $request)
    {
        $query = PosInvoice::with(['customer.user', 'order.staff', 'seller'])
            ->whereHas('order', function($q) {
                $q->whereNotNull('staff_id');
            })
            ->orderBy('id', 'desc');

        if ($request->filled('staff_id')) {
            $query->whereHas('order', function($q) use ($request) {
                $q->where('staff_id', $request->staff_id);
            });
        }

        $orders = $query->paginate(20)->withQueryString();
        $staffs = User::whereIn('role', ['employee', 'manager', 'staff', 'admin'])->get();
        $settings = GenaralSetting::first();

        return view('admin.orders.staff_assignments', [
            'orders' => $orders,
            'status' => 'assigned',
            'staffs' => $staffs,
            'settings' => $settings,
            'title' => 'Staff Assignments',
            'currentStaffId' => $request->staff_id
        ]);
    }

    /**
     * Display activity history of orders.
     */
    public function activityHistory(Request $request)
    {
        $staffs = User::whereIn('role', ['employee', 'manager', 'staff', 'admin'])
            ->withCount([
                'posOrders as total_orders',
                'posOrders as pending_orders' => function($q) {
                    $q->where('status', 'pending');
                },
                'posOrders as processing_orders' => function($q) {
                    $q->where('status', 'processing');
                },
                'posOrders as delivered_orders' => function($q) {
                    $q->where('status', 'delivered');
                }
            ])
            ->get();

        $settings = GenaralSetting::first();

        return view('admin.orders.activity_history', [
            'staffs' => $staffs,
            'status' => 'activity',
            'settings' => $settings,
            'title' => 'Staff Activity Analytics'
        ]);
    }
}
