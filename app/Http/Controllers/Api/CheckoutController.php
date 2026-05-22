<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pointofsalepo;
use App\Models\PosInvoice;
use App\Models\Promocode;
use App\Models\ShippingCharge;
use App\Models\Product;
use App\Models\SellerProduct;
use App\Models\Duplicateordersetting;
use App\Models\StripeGateway;
use App\Models\PaypalGateway;
use App\Models\RazorpayGateway;
use App\Models\PaystackGateway;
use App\Models\AamarpayGateway;
use App\Models\BkashGateway;
use App\Models\BkashPayment;
use App\Models\PaytabsGateway;
use App\Models\QicardGateway;
use App\Models\JazzcashGateway;
use App\Models\ShurjopayGateway;
use App\Models\SslcommerzGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\GenaralSetting;
use App\Models\SmsGateway;
use App\Models\Ipblockmanage;
use App\Models\FraudBlacklist;
use App\Services\SmsService;
use App\Mail\OrderPlacedNotification;
use Illuminate\Support\Facades\Mail;


class CheckoutController extends Controller
{
    /**
     * Get all active shipping charges.
     */
    public function getShippingCharges()
    {
        $charges = ShippingCharge::where('status', 1)->get();
        return response()->json([
            'success' => true,
            'data'    => $charges
        ]);
    }

    /**
     * Get all active payment gateways.
     */
    public function getPaymentGateways()
    {
        $gateways = [];

        $stripe = StripeGateway::first();
        if ($stripe && $stripe->status) {
            $gateways[] = ['name' => 'Stripe', 'key' => 'stripe', 'title' => $stripe->title, 'logo' => $stripe->logo ? asset('storage/' . $stripe->logo) : $this->getDefaultLogo('stripe')];
        }

        $paypal = PaypalGateway::first();
        if ($paypal && $paypal->status) {
            $gateways[] = ['name' => 'PayPal', 'key' => 'paypal', 'title' => $paypal->title, 'logo' => $paypal->logo ? asset('storage/' . $paypal->logo) : $this->getDefaultLogo('paypal')];
        }

        $razorpay = RazorpayGateway::first();
        if ($razorpay && $razorpay->status) {
            $gateways[] = ['name' => 'Razorpay', 'key' => 'razorpay', 'title' => $razorpay->title, 'logo' => $razorpay->logo ? asset('storage/' . $razorpay->logo) : $this->getDefaultLogo('razorpay')];
        }

        $paystack = PaystackGateway::first();
        if ($paystack && $paystack->status) {
            $gateways[] = ['name' => 'Paystack', 'key' => 'paystack', 'title' => $paystack->title, 'logo' => $paystack->logo ? asset('storage/' . $paystack->logo) : $this->getDefaultLogo('paystack')];
        }

        $aamarpay = AamarpayGateway::first();
        if ($aamarpay && $aamarpay->status) {
            $gateways[] = ['name' => 'Aamarpay', 'key' => 'aamarpay', 'title' => $aamarpay->title, 'logo' => $aamarpay->logo ? asset('storage/' . $aamarpay->logo) : $this->getDefaultLogo('aamarpay')];
        }

        $bkash = BkashGateway::first();
        if ($bkash && $bkash->status) {
            $gateways[] = ['name' => 'bKash', 'key' => 'bkash', 'title' => $bkash->title, 'logo' => $bkash->logo ? asset('storage/' . $bkash->logo) : $this->getDefaultLogo('bkash')];
        }

        $bkashPayment = BkashPayment::first();
        if ($bkashPayment && $bkashPayment->status) {
            $gateways[] = ['name' => 'bKash Payment', 'key' => 'bkash_payment', 'title' => 'bKash Payment', 'logo' => $this->getDefaultLogo('bkash')];
        }

        $paytabs = PaytabsGateway::first();
        if ($paytabs && $paytabs->status) {
            $gateways[] = ['name' => 'PayTabs', 'key' => 'paytabs', 'title' => $paytabs->title, 'logo' => $paytabs->logo ? asset('storage/' . $paytabs->logo) : $this->getDefaultLogo('paytabs')];
        }

        $qicard = QicardGateway::first();
        if ($qicard && $qicard->status) {
            $gateways[] = ['name' => 'QiCard', 'key' => 'qicard', 'title' => $qicard->title, 'logo' => $qicard->logo ? asset('storage/' . $qicard->logo) : $this->getDefaultLogo('qicard')];
        }

        $jazzcash = JazzcashGateway::first();
        if ($jazzcash && $jazzcash->status) {
            $gateways[] = ['name' => 'JazzCash', 'key' => 'jazzcash', 'title' => $jazzcash->title, 'logo' => $jazzcash->logo ? asset('storage/' . $jazzcash->logo) : $this->getDefaultLogo('jazzcash')];
        }

        $shurjopay = ShurjopayGateway::first();
        if ($shurjopay && $shurjopay->status) {
            $gateways[] = ['name' => 'Shurjopay', 'key' => 'shurjopay', 'title' => 'Shurjopay', 'logo' => $this->getDefaultLogo('shurjopay')];
        }

        $sslcommerz = SslcommerzGateway::first();
        if ($sslcommerz && $sslcommerz->status) {
            $gateways[] = ['name' => 'SSLCommerz', 'key' => 'sslcommerz', 'title' => $sslcommerz->title, 'logo' => $sslcommerz->logo ? asset('storage/' . $sslcommerz->logo) : $this->getDefaultLogo('sslcommerz')];
        }

        // Add COD by default or check a setting if you have one
        $gateways[] = ['name' => 'Cash on Delivery', 'key' => 'cod', 'title' => 'Cash on Delivery', 'logo' => null];

        return response()->json([
            'success' => true,
            'data'    => $gateways
        ]);
    }

    /**
     * Apply coupon code and calculate discount.
     */
    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string',
            'subtotal'    => 'required|numeric',
        ]);

        $coupon = Promocode::where('coupon_code', $request->coupon_code)
            ->where('status', 1)
            ->where('start_date', '<=', now())
            ->where('expired_date', '>=', now())
            ->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired coupon code.'
            ]);
        }

        if ($request->subtotal < $coupon->minimum_order_amount) {
            return response()->json([
                'success' => false,
                'message' => 'Minimum order amount for this coupon is ৳' . number_format($coupon->minimum_order_amount)
            ]);
        }

        $discount = 0;
        if ($coupon->discount_type === 'percentage') {
            $discount = ($request->subtotal * $coupon->discount) / 100;
            if ($coupon->maximum_discount_amount > 0 && $discount > $coupon->maximum_discount_amount) {
                $discount = $coupon->maximum_discount_amount;
            }
        } else {
            $discount = $coupon->discount;
        }

        return response()->json([
            'success'  => true,
            'message'  => 'Coupon applied successfully!',
            'discount' => $discount,
            'code'     => $coupon->coupon_code
        ]);
    }

    /**
     * Place order and split by seller.
     */
    public function placeOrder(Request $request)
    {
        $request->validate([
            'name'           => 'required|string',
            'phone'          => 'required|string',
            'address'        => 'required|string',
            'city'           => 'required|string',
            'shipping_id'    => 'required|integer',
            'items'          => 'required|array|min:1',
            'items.*.id'     => 'required|integer',
            'items.*.qty'    => 'required|integer|min:1',
            'items.*.product_type' => 'required|string',
            'items.*.uid'    => 'nullable|string',
            'items.*.color'  => 'nullable|string',
            'items.*.size'   => 'nullable|string',
            'payment_method' => 'required|string',
            'online_gateway' => 'nullable|string|required_if:payment_method,online',
            'coupon_code'    => 'nullable|string',
        ]);

        $shipping = ShippingCharge::where('id', $request->shipping_id)->where('status', 1)->first();
        if (!$shipping) {
            return response()->json([
                'success' => false,
                'message' => 'অবৈধ শিপিং জোন নির্বাচন করা হয়েছে। দয়া করে আবার চেষ্টা করুন।'
            ], 422);
        }

        $validatedItems = [];
        foreach ($request->items as $item) {
            $productType = strtolower($item['product_type'] ?? 'admin');
            $product = $this->findOrderProduct($item['id'], $productType);

            if (!$product || !($product->is_active ?? true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'কার্টের একটি পণ্যের তথ্য আর পাওয়া যাচ্ছে না বা আর উপলব্ধ নেই। অনুগ্রহ করে কার্ট রিফ্রেশ করুন এবং আবার চেষ্টা করুন।'
                ], 422);
            }

            if (isset($product->stock_quantity) && $product->stock_quantity < $item['qty']) {
                return response()->json([
                    'success' => false,
                    'message' => 'পণ্যের স্টক সীমা পেরিয়ে গেছেন: ' . ($product->name ?? $product->title ?? 'Unknown Product')
                ], 422);
            }

            $unitPrice = $this->getProductOrderPrice($product);
            if ($unitPrice <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'পণ্যের মূল্যটি সঠিক নয়। অনুগ্রহ করে অন্য পণ্য নির্বাচন করুন।'
                ], 422);
            }

            $validatedItems[] = [
                'uid'              => $item['uid'] ?? (string) Str::uuid(),
                'id'               => $product->id,
                'product_type'     => $productType,
                'seller_id'        => $productType === 'seller' ? ($product->seller_id ?? 0) : 0,
                'title'            => $product->name ?? $product->title ?? 'Product',
                'price'            => $unitPrice,
                'qty'              => $item['qty'],
                'color'            => $item['color'] ?? null,
                'size'             => $item['size'] ?? null,
                'cash_on_delivery' => $product->cash_on_delivery ?? true,
                'online_payment'   => $product->online_payment ?? true,
            ];
        }

        $couponDiscount = 0;
        if ($request->coupon_code) {
            $coupon = Promocode::where('coupon_code', $request->coupon_code)
                ->where('status', 1)
                ->where('start_date', '<=', now())
                ->where('expired_date', '>=', now())
                ->first();

            if (!$coupon) {
                return response()->json([
                    'success' => false,
                    'message' => 'অবৈধ বা মেয়াদোত্তীর্ণ কুপন কোড।'
                ], 422);
            }

            $subTotalForCoupon = collect($validatedItems)->sum(fn($item) => $item['price'] * $item['qty']);
            if ($subTotalForCoupon < $coupon->minimum_order_amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'এই কুপন কোডের জন্য কমপক্ষে ৳' . number_format($coupon->minimum_order_amount) . ' অর্ডার করতে হবে।'
                ], 422);
            }

            $couponDiscount = $this->calculateCouponDiscount($coupon, $subTotalForCoupon);
        }

        $shippingCharge = (float) $shipping->charge;

        // IP Block & Fraud Blacklist Check
        $isIpBlocked = Ipblockmanage::where('ip_address', $request->ip())
            ->where('is_active', true)
            ->exists();

        $isBlacklisted = FraudBlacklist::isBlacklisted('ip', $request->ip()) ||
                         FraudBlacklist::isBlacklisted('phone', $request->phone) ||
                         ($request->email && FraudBlacklist::isBlacklisted('email', $request->email));

        if ($isIpBlocked || $isBlacklisted) {
            return response()->json([
                'success' => false,
                'message' => 'দুঃখিত, আপনার তথ্য আমাদের সিস্টেমে ব্লক করা হয়েছে। আপনি বর্তমানে কোনো অর্ডার করতে পারবেন না।'
            ], 403);
        }

        if ($request->payment_method === 'online') {
            if (!$this->isGatewayConfigured($request->online_gateway)) {
                return response()->json([
                    'success' => false,
                    'message' => 'এই পেমেন্ট মেথডটি এখনো সঠিকভাবে সেটআপ করা হয়নি। দয়া করে এডমিন প্যানেল থেকে কনফিগার করুন।'
                ], 422);
            }
        }

        // Duplicate Order Check
        $duplicateSetting = Duplicateordersetting::instance();
        if ($duplicateSetting->allow_duplicate_orders == false) {
            $duplicateCheck = $this->checkDuplicateOrder($request, $duplicateSetting, $validatedItems);
            if ($duplicateCheck['is_duplicate']) {
                return response()->json([
                    'success' => false,
                    'message' => $duplicateSetting->duplicate_check_message ?: 'Duplicate order detected.'
                ], 422);
            }
        }

        try {
            DB::beginTransaction();

// 1. Group validated items by seller_id
            $groupedItems = [];
            foreach ($validatedItems as $item) {
                $sellerId = $item['seller_id'] ?? 0; // 0 for Admin
                $groupedItems[$sellerId][] = $item;
            }

            $orders = [];
            $totalItems = count($validatedItems);

            // 2. Create an order for each seller
            foreach ($groupedItems as $sellerId => $items) {
                $subTotal = collect($items)->sum(function($item) {
                    return $item['price'] * $item['qty'];
                });

                // Pro-rate discount and shipping across seller orders.
                $totalOrderAmount = collect($validatedItems)->sum(fn($i) => $i['price'] * $i['qty']);
                $orderDiscount = $totalOrderAmount > 0 ? $couponDiscount * ($subTotal / $totalOrderAmount) : 0;
                $orderShipping = $totalOrderAmount > 0 ? $shippingCharge * ($subTotal / $totalOrderAmount) : 0;

                $grandTotal = ($subTotal + $orderShipping) - $orderDiscount;

                $customerId = auth('sanctum')->check() ? auth('sanctum')->id() : null;

                $order = Pointofsalepo::create([
                    'seller_id'      => $sellerId ?: null,
                    'customer_id'    => $customerId,
                    'items'          => $items,
                    'sub_total'      => $subTotal,
                    'discount'       => $orderDiscount,
                    'delivery_charge'=> $orderShipping,
                    'tax_amount'     => 0,
                    'grand_total'    => $grandTotal,
                    'payment_method' => $request->payment_method,
                    'coupon_code'    => $request->coupon_code,
                    'note'           => "Online Order\nName: " . $request->name . "\nPhone: " . $request->phone . "\nAddress: " . $request->address . ", " . $request->city . ($request->email ? "\nEmail: " . $request->email : ""),
                    'status'         => 'pending',
                    'ip_address'     => $request->ip(),
                    'phone'          => $request->phone,
                ]);

                // 3. Create Invoice for the order
                $invoice = PosInvoice::create([
                    'invoice_number'  => PosInvoice::generateInvoiceNumber(),
                    'seller_id'       => $sellerId ?: null,
                    'pointofsalepo_id'=> $order->id,
                    'customer_id'     => $customerId,
                    'items'           => $items,
                    'sub_total'       => $subTotal,
                    'discount'        => $orderDiscount,
                    'delivery_charge' => $orderShipping,
                    'tax_amount'      => 0,
                    'grand_total'     => $grandTotal,
                    'payment_method'  => $request->payment_method,
                    'coupon_code'     => $request->coupon_code,
                ]);

                $orders[] = $order;
            }

            DB::commit();

            // Set persistent tracking cookie for returning customer detector (1 year)
            \Illuminate\Support\Facades\Cookie::queue('customer_tracker_phone', $request->phone, 525600);
            \Illuminate\Support\Facades\Cookie::queue('customer_tracker_name', $request->name, 525600);

            // Send Email & SMS notifications
            try {
                $setting = GenaralSetting::first();
                if ($setting && $setting->email_address && count($orders) > 0) {
                    $firstOrder = $orders[0];
                    $firstInvoice = \App\Models\PosInvoice::where('pointofsalepo_id', $firstOrder->id)->first();
                    if ($firstInvoice) {
                        \Illuminate\Support\Facades\Mail::to($setting->email_address)->send(new \App\Mail\OrderPlacedNotification($firstOrder, $firstInvoice));
                    }
                }
            } catch (\Exception $e) {
                \Log::error("Failed to send order notification email: " . $e->getMessage());
            }

            try {
                $smsGateway = SmsGateway::first();
                if ($smsGateway && $smsGateway->status && $smsGateway->order_confirm) {
                    $invoiceNumber = $orders[0]->invoice_number ?? '';
                    $shopName = GenaralSetting::first()->shop_name ?? 'Our Shop';
                    $message = "প্রিয় গ্রাহক, " . $shopName . "-এ আপনার অর্ডারটি সফলভাবে সম্পন্ন হয়েছে। ইনভয়েস নং: #" . $invoiceNumber . "। আমাদের সাথে কেনাকাটার জন্য ধন্যবাদ।";
                    SmsService::send($request->phone, $message);
                }
            } catch (\Exception $e) {
                \Log::error("Failed to send order SMS: " . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully!',
                'orders'  => $orders
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error placing order: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Track order by invoice number.
     */
    public function trackOrder($invoice_no)
    {
        $invoice = PosInvoice::with('order')->where('invoice_number', $invoice_no)->first();

        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'ইনভয়েস নম্বরটি সঠিক নয়। অনুগ্রহ করে আবার চেষ্টা করুন।'
            ]);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'invoice_number' => $invoice->invoice_number,
                'status'         => $invoice->order ? $invoice->order->status : 'N/A',
                'created_at'     => $invoice->created_at->format('d M Y, h:i A'),
                'grand_total'    => $invoice->grand_total,
                'payment_method' => $invoice->payment_method,
            ]
        ]);
    }

    private function findOrderProduct(int $id, string $productType)
    {
        if (in_array(strtolower($productType), ['seller', 'seller_product'], true)) {
            return SellerProduct::find($id);
        }

        return Product::find($id);
    }

    private function getProductOrderPrice($product): float
    {
        if (isset($product->discount_price) && $product->discount_price > 0) {
            return (float) $product->discount_price;
        }

        if (isset($product->selling_price) && $product->selling_price > 0) {
            return (float) $product->selling_price;
        }

        if (isset($product->price) && $product->price > 0) {
            return (float) $product->price;
        }

        return 0.0;
    }

    private function calculateCouponDiscount(Promocode $coupon, float $subTotal): float
    {
        $discount = 0;
        if ($coupon->discount_type === 'percentage') {
            $discount = ($subTotal * $coupon->discount) / 100;
            if ($coupon->maximum_discount_amount > 0 && $discount > $coupon->maximum_discount_amount) {
                $discount = $coupon->maximum_discount_amount;
            }
        } else {
            $discount = $coupon->discount;
        }

        return max(0, min($discount, $subTotal));
    }

    /**
     * Check for duplicate order based on settings.
     */
    private function checkDuplicateOrder($request, $setting, $validatedItems)
    {
        $timeLimit = $setting->duplicate_time_limit ?: 1;
        $checkType = $setting->duplicate_check_type ?: 'Product + IP + Phone';
        $ip = $request->ip();
        $phone = $request->phone;

        $query = Pointofsalepo::where('created_at', '>=', Carbon::now()->subMinutes($timeLimit));

        if (Str::contains($checkType, 'IP')) {
            $query->where('ip_address', $ip);
        }

        if (Str::contains($checkType, 'Phone')) {
            $query->where('phone', $phone);
        }

        // For Product check, we need to see if any item in the new order matches an item in recent orders
        if (Str::contains($checkType, 'Product')) {
            $newProductIds = collect($validatedItems)->pluck('id')->toArray();

            // This is a bit complex since items is JSON.
            // We'll fetch the recent orders and check manually or use JSON search if supported.
            $recentOrders = $query->get();
            foreach ($recentOrders as $recentOrder) {
                $recentProductIds = collect($recentOrder->items)->pluck('id')->toArray();
                if (array_intersect($newProductIds, $recentProductIds)) {
                    return ['is_duplicate' => true];
                }
            }
            return ['is_duplicate' => false];
        }

        return ['is_duplicate' => $query->exists()];
    }
    /**
     * Check if a gateway is fully configured.
     */
    private function isGatewayConfigured($key)
    {
        switch ($key) {
            case 'stripe':
                $g = StripeGateway::first();
                return $g && $g->secret_key && $g->published_key;
            case 'paypal':
                $g = PaypalGateway::first();
                return $g && $g->client_id && $g->client_secret;
            case 'razorpay':
                $g = RazorpayGateway::first();
                return $g && $g->key && $g->secret;
            case 'paystack':
                $g = PaystackGateway::first();
                return $g && $g->public_key && $g->secret_key;
            case 'aamarpay':
                $g = AamarpayGateway::first();
                return $g && $g->store_id && $g->signature_key;
            case 'bkash':
                $g = BkashGateway::first();
                return $g && $g->app_key && $g->app_secret_key && $g->username && $g->password;
            case 'bkash_payment':
                $g = BkashPayment::first();
                return $g && $g->app_key && $g->app_secret && $g->username && $g->password;
            case 'paytabs':
                $g = PaytabsGateway::first();
                return $g && $g->profile_id && $g->server_key;
            case 'qicard':
                $g = QicardGateway::first();
                return $g && $g->username && $g->password && $g->terminal_id;
            case 'jazzcash':
                $g = JazzcashGateway::first();
                return $g && $g->merchant_id && $g->password && $g->integrity_salt;
            case 'shurjopay':
                $g = ShurjopayGateway::first();
                return $g && $g->username && $g->password && $g->prefix;
            case 'sslcommerz':
                $g = SslcommerzGateway::first();
                return $g && $g->store_id && $g->store_password;
            default:
                return true;
        }
    }

    /**
     * Get default online logo for gateways.
     */
    private function getDefaultLogo($key)
    {
        $logos = [
            'stripe'    => 'https://www.vectorlogo.zone/logos/stripe/stripe-ar21.png',
            'paypal'    => 'https://www.vectorlogo.zone/logos/paypal/paypal-ar21.png',
            'razorpay'  => 'https://www.vectorlogo.zone/logos/razorpay/razorpay-ar21.png',
            'paystack'  => 'https://www.vectorlogo.zone/logos/paystack/paystack-ar21.png',
            'aamarpay'  => 'https://aamarpay.com/images/logo.png',
            'bkash'     => 'https://raw.githubusercontent.com/Nazmul64/JhrBazar/main/public/assets/bkash_logo.png', // Fallback to a known path if online is unstable
            'paytabs'   => 'https://site.paytabs.com/wp-content/uploads/2021/05/PayTabs-Logo.png',
            'qicard'    => 'https://www.qicard.net/wp-content/uploads/2021/04/Qi-Card-Logo.png',
            'jazzcash'  => 'https://www.vectorlogo.zone/logos/jazzcash/jazzcash-ar21.png',
            'shurjopay' => 'https://shurjopay.com.bd/logo/shurjopay-logo.png',
            'sslcommerz' => 'https://securepay.sslcommerz.com/gw/asset/img/sslcommerz-logo.png',
        ];

        // Manual override for bkash if the above fails
        if ($key === 'bkash') return 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/88/BKash_Logo.svg/512px-BKash_Logo.svg.png';

        return $logos[$key] ?? null;
    }
}
