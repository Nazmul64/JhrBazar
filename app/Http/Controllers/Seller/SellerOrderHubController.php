<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\PosInvoice;
use App\Models\GenaralSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\EmployeeSeller;
use App\Models\SteadfastCourier;
use App\Models\PathaoCourier;
use Illuminate\Support\Facades\Http;
use App\Services\FraudDetectionService;

class SellerOrderHubController extends Controller
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
     * Display a listing of orders for the authenticated seller.
     */
    public function index(Request $request, $status = 'all')
    {
        // ISOLATION: Filter by seller_id
        $query = PosInvoice::where('seller_id', Auth::id())
            ->with(['customer.user', 'order.sellerStaff'])
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
        
        // Seller's Employees (Fetched from EmployeeSeller model)
        $staffs = \App\Models\EmployeeSeller::where('seller_id', Auth::id())->get();
        
        $steadfast = SteadfastCourier::first();
        $pathao = PathaoCourier::first();

        // Summary Stats (Isolated for Seller)
        $totalOrders     = PosInvoice::where('seller_id', Auth::id())->count();
        $pendingOrders   = PosInvoice::where('seller_id', Auth::id())->whereHas('order', fn($q) => $q->where('status', 'pending'))->count();
        $processingOrders= PosInvoice::where('seller_id', Auth::id())->whereHas('order', fn($q) => $q->where('status', 'processing'))->count();
        $shippedOrders   = PosInvoice::where('seller_id', Auth::id())->whereHas('order', fn($q) => $q->where('status', 'shipped'))->count();
        $deliveredOrders = PosInvoice::where('seller_id', Auth::id())->whereHas('order', fn($q) => $q->where('status', 'delivered'))->count();

        return view('seller.orders.index', compact(
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
            'deliveredOrders'
        ));
    }

    /**
     * Show single order detail.
     */
    public function show($id)
    {
        $invoice = PosInvoice::where('seller_id', Auth::id())->with(['customer.user', 'order'])->findOrFail($id);
        $settings = GenaralSetting::first();
        return view('seller.pos.show', compact('invoice', 'settings'));
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|string']);
        $invoice = PosInvoice::where('seller_id', Auth::id())->findOrFail($id);
        
        if ($invoice->order) {
            $invoice->order->update(['status' => $request->status]);
            return response()->json(['success' => true, 'message' => 'Status updated to ' . ucfirst($request->status)]);
        }

        return response()->json(['success' => false, 'message' => 'Order link not found.'], 422);
    }

    /**
     * Assign staff to order.
     */
    public function assignStaff(Request $request, $id)
    {
        $request->validate(['staff_id' => 'required|exists:employee_sellers,id']);
        $invoice = PosInvoice::where('seller_id', Auth::id())->findOrFail($id);
        
        if ($invoice->order) {
            $invoice->order->update(['staff_id' => $request->staff_id]);
            return response()->json(['success' => true, 'message' => 'Order assigned successfully.']);
        }
        return response()->json(['success' => false, 'message' => 'Order link not found.'], 422);
    }

    /**
     * Update payment status.
     */
    public function updatePaymentStatus(Request $request, $id)
    {
        $request->validate(['payment_status' => 'required|string']);
        $invoice = PosInvoice::where('seller_id', Auth::id())->findOrFail($id);
        
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
        
        // Ensure seller only acts on their own orders
        $invoices = PosInvoice::where('seller_id', Auth::id())->whereIn('id', $ids)->get();

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
                if (str_starts_with($action, 'status:')) {
                    $newStatus = str_replace('status:', '', $action);
                    foreach ($invoices as $inv) {
                        if ($inv->order) $inv->order->update(['status' => $newStatus]);
                    }
                    return response()->json(['success' => true, 'message' => 'Selected orders updated to ' . ucfirst($newStatus)]);
                }
        }

        return response()->json(['success' => false, 'message' => 'Invalid action.'], 422);
    }

    private function bulkSendToSteadfast($invoices)
    {
        $gateway = SteadfastCourier::first();
        if (!$gateway || !$gateway->status) {
            return response()->json(['success' => false, 'message' => 'Steadfast Courier is not active.'], 422);
        }

        $successCount = 0;
        foreach ($invoices as $inv) {
            if (!$inv->order || $inv->order->steadfast_order_id) continue;

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
            }
        }

        return response()->json(['success' => true, 'message' => "Successfully sent {$successCount} orders to Steadfast."]);
    }

    /**
     * Send to Pathao (Bulk).
     */
    private function bulkSendToPathao(Request $request, $invoices)
    {
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
        // Ensure seller only sees their own invoices
        $invoices = PosInvoice::where('seller_id', Auth::id())
            ->with(['customer.user', 'order', 'seller'])
            ->whereIn('id', $ids)
            ->get();
            
        $settings = GenaralSetting::first();
        
        return view('admin.Invoice.bulk-invoice', compact('invoices', 'settings'));
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
}
