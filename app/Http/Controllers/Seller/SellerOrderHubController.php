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
        $cancelledOrders = PosInvoice::where('seller_id', Auth::id())->whereHas('order', fn($q) => $q->where('status', 'cancelled'))->count();

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
            'deliveredOrders',
            'cancelledOrders'
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
        if ($request->status === 'delivered') {
            return response()->json(['success' => false, 'message' => 'Sellers cannot mark orders as delivered. Delivery is confirmed by Admin or Courier.'], 403);
        }
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
        return response()->json(['success' => false, 'message' => 'Payment status can only be updated by Admin.'], 403);
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
                    if ($newStatus === 'delivered') {
                        return response()->json(['success' => false, 'message' => 'Sellers cannot mark orders as delivered.'], 403);
                    }
                    foreach ($invoices as $inv) {
                        if ($inv->order) $inv->order->update(['status' => $newStatus]);
                    }
                    return response()->json(['success' => true, 'message' => 'Selected orders updated to ' . ucfirst($newStatus)]);
                }
        }

        return response()->json(['success' => false, 'message' => 'Invalid action.'], 422);
    }

    /**
     * Resolve recipient name, phone, address, and item notes from order/invoice.
     */
    private function resolveOrderCustomerDetails($inv): array
    {
        $order = $inv->order;
        $note = $inv->note ?: ($order?->note ?: '');

        // 1. Name
        $name = trim(($inv->customer?->first_name ?? '') . ' ' . ($inv->customer?->last_name ?? ''));
        if (!$name || $name === '') {
            $name = $inv->customer?->user?->name ?? '';
        }
        if (!$name && $note && preg_match('/Name:\s*([^\r\n]+)/i', $note, $m)) {
            $name = trim($m[1]);
        }
        if (!$name) {
            $name = 'Customer';
        }

        // 2. Phone
        $phone = $inv->customer?->user?->phone ?? ($order?->phone ?? '');
        if (!$phone && $note && preg_match('/(?:Phone|Mobile):\s*([0-9\+\-\s]+)/i', $note, $m)) {
            $phone = trim($m[1]);
        }
        if (!$phone && $note && preg_match('/(01[3-9]\d{8})/', $note, $m)) {
            $phone = trim($m[1]);
        }
        $phone = preg_replace('/[^0-9]/', '', (string)$phone);
        if (str_starts_with($phone, '8801')) {
            $phone = substr($phone, 2);
        }

        // 3. Address
        $address = $inv->customer?->address ?? ($inv->customer?->user?->address ?? '');
        if (!$address && $order) {
            $address = $order->shipping_address ?? ($order->address ?? '');
        }
        if (!$address && $note && preg_match('/Address:\s*([^\r\n]+)/i', $note, $m)) {
            $address = trim($m[1]);
        }
        if (!$address || $address === 'N/A' || $address === '') {
            $address = 'Bangladesh';
        }

        // 4. Product description / Note for courier label
        $items = $inv->items ?? ($order?->items ?? []);
        $itemSummaries = [];
        if (is_array($items)) {
            foreach ($items as $item) {
                $pName = $item['name'] ?? ($item['title'] ?? 'Product');
                $qty = $item['qty'] ?? 1;
                $price = $item['price'] ?? 0;
                $itemSummaries[] = "{$pName} (x{$qty} BDT {$price})";
            }
        }
        $courierNote = implode(', ', $itemSummaries);
        if (strlen($courierNote) > 240) {
            $courierNote = substr($courierNote, 0, 237) . '...';
        }

        return [
            'name'    => $name,
            'phone'   => $phone,
            'address' => $address,
            'note'    => $courierNote,
        ];
    }

    private function bulkSendToSteadfast($invoices)
    {
        $gateway = SteadfastCourier::first();
        if (!$gateway || !$gateway->status || empty($gateway->api_key) || empty($gateway->secret_key)) {
            return response()->json(['success' => false, 'message' => 'Steadfast Courier is not active or configured.'], 422);
        }

        $endpoint = rtrim($gateway->url ?: 'https://portal.steadfast.com.bd/api/v1/create_order', '/');
        if (!str_ends_with($endpoint, 'create_order')) {
            $endpoint .= '/create_order';
        }

        $successCount = 0;
        $errors = [];

        foreach ($invoices as $inv) {
            if (!$inv->order || $inv->order->steadfast_order_id) continue;

            $cData = $this->resolveOrderCustomerDetails($inv);

            try {
                $response = Http::withHeaders([
                    'Api-Key' => $gateway->api_key,
                    'Secret-Key' => $gateway->secret_key,
                    'Content-Type' => 'application/json'
                ])->post($endpoint, [
                    'invoice'           => $inv->invoice_number,
                    'recipient_name'    => $cData['name'],
                    'recipient_phone'   => $cData['phone'],
                    'recipient_address' => $cData['address'],
                    'cod_amount'        => (float)$inv->grand_total,
                    'note'              => $cData['note']
                ]);

                if ($response->successful() && ($response->json('status') == 200 || $response->json('status') === 'success' || isset($response->json('order')['consignment_id']))) {
                    $consignmentId = $response->json('order.consignment_id') ?? $response->json('consignment.consignment_id') ?? $response->json('consignment_id');
                    $inv->order->update([
                        'steadfast_order_id' => $consignmentId,
                        'courier_name'       => 'Steadfast',
                        'courier_status'     => 'sent'
                    ]);
                    $successCount++;
                } else {
                    $errText = $response->json('message') ?? ($response->json('errors') ? json_encode($response->json('errors')) : 'HTTP ' . $response->status());
                    $errors[] = "Invoice {$inv->invoice_number}: {$errText}";
                }
            } catch (\Exception $e) {
                $errors[] = "Invoice {$inv->invoice_number}: " . $e->getMessage();
            }
        }

        if ($successCount > 0) {
            return response()->json(['success' => true, 'message' => "Successfully sent {$successCount} orders to Steadfast."]);
        }

        return response()->json([
            'success' => false,
            'message' => count($errors) ? implode('; ', $errors) : "No eligible orders were sent to Steadfast.",
            'errors' => $errors
        ], 422);
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

        $auth = $this->getPathaoToken($gateway);
        if (!$auth['token']) {
            return response()->json(['success' => false, 'message' => $auth['error']], 422);
        }
        $token = $auth['token'];

        $successCount = 0;
        $errors = [];
        $baseUrl = rtrim($gateway->base_url ?: 'https://api-hermes.pathao.com', '/');

        foreach ($invoices as $inv) {
            if (!$inv->order || $inv->order->pathao_consignment_id) continue;

            $cData = $this->resolveOrderCustomerDetails($inv);

            // 1. Sanitize phone: must be 11 digits (e.g. 017xxxxxxxx)
            $phone = preg_replace('/[^0-9]/', '', (string)$cData['phone']);
            if (str_starts_with($phone, '8801')) {
                $phone = substr($phone, 2);
            } elseif (strlen($phone) === 10 && str_starts_with($phone, '1')) {
                $phone = '0' . $phone;
            }

            // 2. Sanitize address: Pathao requires minimum 10 characters
            $address = trim((string)$cData['address']);
            if (mb_strlen($address) < 10) {
                $address = $address . ', Bangladesh';
            }
            if (mb_strlen($address) < 10) {
                $address = 'Delivery Address: ' . $address;
            }

            $recipientName = trim((string)$cData['name']) ?: 'Customer';

            $payload = [
                'store_id'            => (int)$request->store_id,
                'merchant_order_id'   => (string)$inv->invoice_number,
                'recipient_name'      => $recipientName,
                'recipient_phone'     => $phone,
                'recipient_address'   => $address,
                'recipient_city'      => (int)$request->city_id,
                'recipient_zone'      => (int)$request->zone_id,
                'delivery_type'       => 48, // Standard
                'item_type'           => 2,  // Parcel
                'special_instruction' => $cData['note'] ?: ($inv->note ?? ''),
                'item_quantity'       => max(1, (int)$inv->total_qty),
                'item_weight'         => 0.5,
                'amount_to_collect'   => (float)$inv->grand_total,
                'item_description'    => 'Order #' . $inv->invoice_number
            ];

            if ($request->filled('area_id') && (int)$request->area_id > 0) {
                $payload['recipient_area'] = (int)$request->area_id;
            }

            try {
                $response = Http::withToken($token)->post($baseUrl . '/aladdin/api/v1/orders', $payload);

                if ($response->successful() && ($response->json('type') == 'success' || $response->json('data.consignment_id') || $response->json('order.consignment_id') || $response->json('consignment_id'))) {
                    $consignmentId = $response->json('data.consignment_id') ?? $response->json('order.consignment_id') ?? $response->json('consignment_id');
                    $inv->order->update([
                        'pathao_consignment_id' => $consignmentId,
                        'courier_name'          => 'Pathao',
                        'courier_status'        => 'sent'
                    ]);
                    $successCount++;
                } else {
                    $rawErrors = $response->json('errors');
                    if (is_array($rawErrors) && count($rawErrors)) {
                        $errParts = [];
                        foreach ($rawErrors as $field => $fieldErrors) {
                            $msg = is_array($fieldErrors) ? implode(', ', $fieldErrors) : (string)$fieldErrors;
                            $errParts[] = "{$field}: {$msg}";
                        }
                        $errText = implode(' | ', $errParts);
                    } else {
                        $errText = $response->json('message') ?? ('HTTP ' . $response->status());
                    }
                    $errors[] = "Invoice {$inv->invoice_number}: {$errText}";
                }
            } catch (\Exception $e) {
                $errors[] = "Invoice {$inv->invoice_number}: " . $e->getMessage();
            }
        }

        if ($successCount > 0) {
            return response()->json([
                'success' => true,
                'message' => "Successfully sent {$successCount} orders to Pathao." . (count($errors) ? " (Some failed: " . implode('; ', $errors) . ")" : ""),
                'errors'  => $errors
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => count($errors) ? implode('; ', $errors) : "No eligible orders were sent to Pathao.",
            'errors'  => $errors
        ], 422);
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
        if (!$gateway || !$gateway->status) {
            return ['token' => null, 'error' => 'Pathao Courier is not active or configured. Please check Courier Management settings.'];
        }

        if (empty($gateway->client_id) || empty($gateway->client_secret) || empty($gateway->username) || empty($gateway->password)) {
            return ['token' => null, 'error' => 'Pathao credentials (Client ID, Client Secret, Username, Password) are incomplete.'];
        }

        $baseUrl = rtrim($gateway->base_url ?? 'https://api-hermes.pathao.com', '/');

        $response = Http::post($baseUrl . '/aladdin/api/v1/issue-token', [
            'client_id'     => $gateway->client_id,
            'client_secret' => $gateway->client_secret,
            'username'      => $gateway->username,
            'password'      => $gateway->password,
            'grant_type'    => $gateway->grant_type ?? 'password',
        ]);

        if ($response->successful() && $response->json('access_token')) {
            return ['token' => $response->json('access_token'), 'error' => null];
        }

        $errorMsg = $response->json('message') ?? 'Failed to authenticate with Pathao. Please verify your Pathao credentials and Base URL in Courier Management.';
        return ['token' => null, 'error' => $errorMsg];
    }

    public function getPathaoCities()
    {
        $gateway = PathaoCourier::first();
        $auth = $this->getPathaoToken($gateway);
        if (!$auth['token']) {
            return response()->json(['success' => false, 'message' => $auth['error']], 422);
        }

        $baseUrl = rtrim($gateway->base_url, '/');
        $response = Http::withToken($auth['token'])->get($baseUrl . '/aladdin/api/v1/countries/1/city-list');
        if (!$response->successful()) {
            $response = Http::withToken($auth['token'])->get($baseUrl . '/aladdin/api/v1/cities');
        }

        $data = $response->json('data.data') ?? $response->json('data') ?? [];
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function getPathaoZones($cityId)
    {
        $gateway = PathaoCourier::first();
        $auth = $this->getPathaoToken($gateway);
        if (!$auth['token']) {
            return response()->json(['success' => false, 'message' => $auth['error']], 422);
        }

        $baseUrl = rtrim($gateway->base_url, '/');
        $response = Http::withToken($auth['token'])->get($baseUrl . "/aladdin/api/v1/cities/{$cityId}/zone-list");
        $data = $response->json('data.data') ?? $response->json('data') ?? [];
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function getPathaoAreas($zoneId)
    {
        $gateway = PathaoCourier::first();
        $auth = $this->getPathaoToken($gateway);
        if (!$auth['token']) {
            return response()->json(['success' => false, 'message' => $auth['error']], 422);
        }

        $baseUrl = rtrim($gateway->base_url, '/');
        $response = Http::withToken($auth['token'])->get($baseUrl . "/aladdin/api/v1/zones/{$zoneId}/area-list");
        $data = $response->json('data.data') ?? $response->json('data') ?? [];
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function getPathaoStores()
    {
        $gateway = PathaoCourier::first();
        $auth = $this->getPathaoToken($gateway);
        if (!$auth['token']) {
            return response()->json(['success' => false, 'message' => $auth['error']], 422);
        }

        $baseUrl = rtrim($gateway->base_url, '/');
        $response = Http::withToken($auth['token'])->get($baseUrl . '/aladdin/api/v1/stores');
        $data = $response->json('data.data') ?? $response->json('data') ?? [];
        return response()->json(['success' => true, 'data' => $data]);
    }
}
