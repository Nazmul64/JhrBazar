<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pointofsalepo;
use App\Models\PosInvoice;
use App\Models\Promocode;
use App\Models\SellerVoucher;
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
            $gateways[] = ['name' => 'Stripe', 'key' => 'stripe', 'title' => $stripe->title, 'logo' => $this->getGatewayLogoUrl($stripe, 'stripe')];
        }

        $paypal = PaypalGateway::first();
        if ($paypal && $paypal->status) {
            $gateways[] = ['name' => 'PayPal', 'key' => 'paypal', 'title' => $paypal->title, 'logo' => $this->getGatewayLogoUrl($paypal, 'paypal')];
        }

        $razorpay = RazorpayGateway::first();
        if ($razorpay && $razorpay->status) {
            $gateways[] = ['name' => 'Razorpay', 'key' => 'razorpay', 'title' => $razorpay->title, 'logo' => $this->getGatewayLogoUrl($razorpay, 'razorpay')];
        }

        $paystack = PaystackGateway::first();
        if ($paystack && $paystack->status) {
            $gateways[] = ['name' => 'Paystack', 'key' => 'paystack', 'title' => $paystack->title, 'logo' => $this->getGatewayLogoUrl($paystack, 'paystack')];
        }

        $aamarpay = AamarpayGateway::first();
        if ($aamarpay && $aamarpay->status) {
            $gateways[] = ['name' => 'Aamarpay', 'key' => 'aamarpay', 'title' => $aamarpay->title, 'logo' => $this->getGatewayLogoUrl($aamarpay, 'aamarpay')];
        }

        $bkash = BkashGateway::first();
        if ($bkash && $bkash->status) {
            $gateways[] = ['name' => 'bKash', 'key' => 'bkash', 'title' => $bkash->title, 'logo' => $this->getGatewayLogoUrl($bkash, 'bkash')];
        }



        $paytabs = PaytabsGateway::first();
        if ($paytabs && $paytabs->status) {
            $gateways[] = ['name' => 'PayTabs', 'key' => 'paytabs', 'title' => $paytabs->title, 'logo' => $this->getGatewayLogoUrl($paytabs, 'paytabs')];
        }

        $qicard = QicardGateway::first();
        if ($qicard && $qicard->status) {
            $gateways[] = ['name' => 'QiCard', 'key' => 'qicard', 'title' => $qicard->title, 'logo' => $this->getGatewayLogoUrl($qicard, 'qicard')];
        }

        $jazzcash = JazzcashGateway::first();
        if ($jazzcash && $jazzcash->status) {
            $gateways[] = ['name' => 'JazzCash', 'key' => 'jazzcash', 'title' => $jazzcash->title, 'logo' => $this->getGatewayLogoUrl($jazzcash, 'jazzcash')];
        }

        $shurjopay = ShurjopayGateway::first();
        if ($shurjopay && $shurjopay->status) {
            $gateways[] = ['name' => 'Shurjopay', 'key' => 'shurjopay', 'title' => 'Shurjopay', 'logo' => $this->getGatewayLogoUrl($shurjopay, 'shurjopay')];
        }

        $sslcommerz = SslcommerzGateway::first();
        if ($sslcommerz && $sslcommerz->status) {
            $gateways[] = ['name' => 'SSLCommerz', 'key' => 'sslcommerz', 'title' => $sslcommerz->title, 'logo' => $this->getGatewayLogoUrl($sslcommerz, 'sslcommerz')];
        }

        // COD is handled separately in the UI, do not add it as an online gateway
        // $gateways[] = ['name' => 'Cash on Delivery', 'key' => 'cod', 'title' => 'Cash on Delivery', 'logo' => null];

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
            'items'       => 'nullable|array',
        ]);

        $coupon = Promocode::where('coupon_code', $request->coupon_code)
            ->where('status', 1)
            ->where('start_date', '<=', now())
            ->where('expired_date', '>=', now())
            ->first();

        $isVoucher = false;

        if (!$coupon) {
            $voucher = SellerVoucher::where('voucher_code', $request->coupon_code)
                ->where('status', 1)
                ->first();

            if ($voucher) {
                $now = now();
                $start = \Carbon\Carbon::parse($voucher->start_date->format('Y-m-d') . ' ' . $voucher->start_time);
                $end = \Carbon\Carbon::parse($voucher->expired_date->format('Y-m-d') . ' ' . $voucher->expired_time);

                if ($now->greaterThanOrEqualTo($start) && $now->lessThanOrEqualTo($end)) {
                    $coupon = $voucher;
                    $isVoucher = true;
                }
            }
        }

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired coupon code.'
            ]);
        }

        $subtotal = (float) $request->subtotal;

        if ($isVoucher) {
            $sellerId = (int)$coupon->seller_id;
            
            if ($request->has('items') && is_array($request->items)) {
                $sellerItems = collect($request->items)->filter(function($item) use ($sellerId) {
                    $itemSellerId = isset($item['seller_id']) ? (int)$item['seller_id'] : 0;
                    return $itemSellerId === $sellerId;
                });

                if ($sellerItems->isEmpty()) {
                    $sellerName = "this seller";
                    $sellerUser = \App\Models\User::find($sellerId);
                    if ($sellerUser) {
                        $shop = \App\Models\Shop::where('user_id', $sellerId)->first();
                        $sellerName = $shop ? $shop->name : $sellerUser->name;
                    }
                    return response()->json([
                        'success' => false,
                        'message' => "This promo code is only valid for products from {$sellerName}."
                    ]);
                }

                // Subtotal of only this seller's products
                $subtotal = $sellerItems->sum(function($item) {
                    return (float)$item['price'] * (int)$item['qty'];
                });
            }
        }

        if ($subtotal < $coupon->minimum_order_amount) {
            return response()->json([
                'success' => false,
                'message' => 'Minimum order amount for this coupon is ৳' . number_format($coupon->minimum_order_amount)
            ]);
        }

        $discount = 0;
        if ($coupon->discount_type === 'percentage') {
            $discount = ($subtotal * $coupon->discount) / 100;
            if ($coupon->maximum_discount_amount > 0 && $discount > $coupon->maximum_discount_amount) {
                $discount = $coupon->maximum_discount_amount;
            }
        } else {
            $discount = $coupon->discount;
        }

        $discount = min($discount, $subtotal);

        return response()->json([
            'success'  => true,
            'message'  => 'Coupon applied successfully!',
            'discount' => $discount,
            'code'     => $isVoucher ? $coupon->voucher_code : $coupon->coupon_code
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
            'device_fingerprint' => 'nullable|string',
            'browser'        => 'nullable|string',
            'os'             => 'nullable|string',
            'device_type'    => 'nullable|string',
            'otp_code'       => 'nullable|string',
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

        $coupon = null;
        $isVoucher = false;
        $couponDiscount = 0;
        if ($request->coupon_code) {
            $coupon = Promocode::where('coupon_code', $request->coupon_code)
                ->where('status', 1)
                ->where('start_date', '<=', now())
                ->where('expired_date', '>=', now())
                ->first();

            if (!$coupon) {
                $voucher = SellerVoucher::where('voucher_code', $request->coupon_code)
                    ->where('status', 1)
                    ->first();

                if ($voucher) {
                    $now = now();
                    $start = \Carbon\Carbon::parse($voucher->start_date->format('Y-m-d') . ' ' . $voucher->start_time);
                    $end = \Carbon\Carbon::parse($voucher->expired_date->format('Y-m-d') . ' ' . $voucher->expired_time);

                    if ($now->greaterThanOrEqualTo($start) && $now->lessThanOrEqualTo($end)) {
                        $coupon = $voucher;
                        $isVoucher = true;
                    }
                }
            }

            if (!$coupon) {
                return response()->json([
                    'success' => false,
                    'message' => 'অবৈধ বা মেয়াদোত্তীর্ণ কুপন কোড।'
                ], 422);
            }

            if ($isVoucher) {
                $sellerId = (int)$coupon->seller_id;
                $sellerItems = collect($validatedItems)->filter(function($item) use ($sellerId) {
                    $itemSellerId = isset($item['seller_id']) ? (int)$item['seller_id'] : 0;
                    return $itemSellerId === $sellerId;
                });

                if ($sellerItems->isEmpty()) {
                    $sellerName = "this seller";
                    $sellerUser = \App\Models\User::find($sellerId);
                    if ($sellerUser) {
                        $shop = \App\Models\Shop::where('user_id', $sellerId)->first();
                        $sellerName = $shop ? $shop->name : $sellerUser->name;
                    }
                    return response()->json([
                        'success' => false,
                        'message' => "এই কুপন কোডটি শুধুমাত্র {$sellerName}-এর পণ্যের জন্য প্রযোজ্য।"
                    ], 422);
                }

                $subTotalForCoupon = $sellerItems->sum(fn($item) => $item['price'] * $item['qty']);
            } else {
                $subTotalForCoupon = collect($validatedItems)->sum(fn($item) => $item['price'] * $item['qty']);
            }

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
        $settings = \App\Models\GenaralSetting::first();
        $ipBlockEnabled = $settings && $settings->ip_block_status;

        $hasPreviousOrders = false;
        if ($ipBlockEnabled) {
            $hasPreviousOrders = \App\Models\Pointofsalepo::where('phone', $request->phone)
                ->orWhere('ip_address', $request->ip())
                ->orWhere('device_fingerprint', $request->device_fingerprint)
                ->exists();
        }

        if ($ipBlockEnabled && $hasPreviousOrders) {
            $isIpBlocked = Ipblockmanage::where('ip_address', $request->ip())
                ->where('is_active', true)
                ->exists();

            $isBlacklisted = FraudBlacklist::isBlacklisted('ip', $request->ip()) ||
                             FraudBlacklist::isBlacklisted('phone', $request->phone) ||
                             ($request->email && FraudBlacklist::isBlacklisted('email', $request->email)) ||
                             ($request->device_fingerprint && FraudBlacklist::isBlacklisted('device', $request->device_fingerprint));

            if ($isIpBlocked || $isBlacklisted) {
                return response()->json([
                    'success' => false,
                    'message' => 'দুঃখিত, আপনার তথ্য আমাদের সিস্টেমে ব্লক করা হয়েছে। আপনি বর্তমানে কোনো অর্ডার করতে পারবেন না।'
                ], 403);
            }
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

        $totalOrderAmount = collect($validatedItems)->sum(fn($i) => $i['price'] * $i['qty']);
        $grandTotalForFraud = ($totalOrderAmount + $shippingCharge) - $couponDiscount;

        // Run Fraud Detection Service Analysis only if IP Block is enabled AND they have previous orders
        if ($ipBlockEnabled && $hasPreviousOrders) {
            try {
                $fraudData = [
                    'type' => 'transaction',
                    'customer_name' => $request->name,
                    'customer_email' => $request->email,
                    'customer_phone' => $request->phone,
                    'ip_address' => $request->ip(),
                    'transaction_amount' => $grandTotalForFraud,
                    'device_type' => $request->device_type,
                    'browser' => $request->browser,
                    'os' => $request->os,
                    'device_fingerprint' => $request->device_fingerprint,
                    'notes' => 'Checkout Auto-check',
                ];

                $fraudDetectionService = app(\App\Services\FraudDetectionService::class);
                $fraudCheck = $fraudDetectionService->analyze($fraudData);

                if ($fraudCheck->status === 'declined' || $fraudCheck->risk_score >= 80) {
                    // Auto Block: Add user, IP, phone, and fingerprint to blacklist
                    Ipblockmanage::updateOrCreate(
                        ['ip_address' => $request->ip()],
                        ['is_active' => true, 'reason' => 'Auto Block: High Fraud Risk Score (' . $fraudCheck->risk_score . ')']
                    );

                    FraudBlacklist::updateOrCreate(
                        ['type' => 'ip', 'value' => $request->ip()],
                        ['reason' => 'Auto Block: High Fraud Risk Score (' . $fraudCheck->risk_score . ')', 'is_active' => true, 'created_by' => null]
                    );

                    FraudBlacklist::updateOrCreate(
                        ['type' => 'phone', 'value' => $request->phone],
                        ['reason' => 'Auto Block: High Fraud Risk Score (' . $fraudCheck->risk_score . ')', 'is_active' => true, 'created_by' => null]
                    );

                    if ($request->device_fingerprint) {
                        FraudBlacklist::updateOrCreate(
                            ['type' => 'device', 'value' => $request->device_fingerprint],
                            ['reason' => 'Auto Block: High Fraud Risk Score (' . $fraudCheck->risk_score . ')', 'is_active' => true, 'created_by' => null]
                        );
                    }

                    if ($request->email) {
                        FraudBlacklist::updateOrCreate(
                            ['type' => 'email', 'value' => $request->email],
                            ['reason' => 'Auto Block: High Fraud Risk Score (' . $fraudCheck->risk_score . ')', 'is_active' => true, 'created_by' => null]
                        );
                    }

                    // If customer is logged in, block their account
                    if (auth('sanctum')->check()) {
                        $user = auth('sanctum')->user();
                        $user->update(['is_blocked' => true]);
                    }

                    return response()->json([
                        'success' => false,
                        'message' => 'দুঃখিত, আপনার লেনদেনের নিরাপত্তা ঝুঁকি অত্যন্ত বেশি হওয়ার কারণে সিস্টেম অর্ডারটি ব্লক করেছে।'
                    ], 403);
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Fraud checking during checkout failed: ' . $e->getMessage());
            }
        }
        // OTP Verification Check
        $smsGateway = SmsGateway::where('status', true)->first();
        $otpSetting = \App\Models\Verificatiootpsettings::first();
        $otpRequired = $smsGateway && $otpSetting && $otpSetting->must_verify_account_on_order_placement;

        if ($otpRequired) {
            // Check if phone has already been verified via verify-otp endpoint
            $isVerified = \Illuminate\Support\Facades\Cache::get('checkout_otp_verified_' . $request->phone);
            if ($isVerified) {
                // Verified successfully, delete verification flag and proceed
                \Illuminate\Support\Facades\Cache::forget('checkout_otp_verified_' . $request->phone);
            } else {
                if (!$request->has('otp_code') || empty($request->otp_code)) {
                    // Generate and send OTP
                    $otp = rand(100000, 999999);
                    \Illuminate\Support\Facades\Cache::put('checkout_otp_' . $request->phone, $otp, 300); // valid for 5 minutes

                    $shopName = GenaralSetting::first()->shop_name ?? 'JhrBazar';
                    $message = "প্রিয় গ্রাহক, JhrBazar-এ আপনার অর্ডারটি নিশ্চিত করতে ওটিপি কোডটি ব্যবহার করুন: " . $otp . "। ধন্যবাদ!";
                    
                    try {
                        $sent = SmsService::send($request->phone, $message);
                        if (!$sent) {
                            return response()->json([
                                'success' => false,
                                'message' => 'মোবাইলে ওটিপি (OTP) পাঠাতে ব্যর্থ হয়েছে। অনুগ্রহ করে মোবাইল নম্বরটি পরীক্ষা করুন।'
                            ], 500);
                        }
                    } catch (\Exception $e) {
                        \Log::error("Failed to send OTP SMS: " . $e->getMessage());
                        return response()->json([
                            'success' => false,
                            'message' => 'ওটিপি (OTP) সার্ভারে ত্রুটি দেখা দিয়েছে। আবার চেষ্টা করুন।'
                        ], 500);
                    }

                    return response()->json([
                        'success' => false,
                        'otp_required' => true,
                        'message' => 'আপনার মোবাইলে ওটিপি (OTP) কোড পাঠানো হয়েছে। অর্ডার সম্পন্ন করতে কোডটি দিন।'
                    ]);
                } else {
                    $cachedOtp = \Illuminate\Support\Facades\Cache::get('checkout_otp_' . $request->phone);
                    if (!$cachedOtp || $cachedOtp != $request->otp_code) {
                        return response()->json([
                            'success' => false,
                            'message' => 'অবৈধ বা মেয়াদোত্তীর্ণ ওটিপি (OTP) কোড। সঠিক কোডটি দিন।'
                        ], 422);
                    }
                    // OTP is valid, clear it from cache
                    \Illuminate\Support\Facades\Cache::forget('checkout_otp_' . $request->phone);
                }
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
                if ($coupon && $isVoucher) {
                    if ((int)$sellerId === (int)$coupon->seller_id) {
                        $orderDiscount = $couponDiscount;
                    } else {
                        $orderDiscount = 0;
                    }
                } else {
                    $orderDiscount = $totalOrderAmount > 0 ? $couponDiscount * ($subTotal / $totalOrderAmount) : 0;
                }
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
                    'device_fingerprint' => $request->device_fingerprint,
                    'browser'        => $request->browser,
                    'os'             => $request->os,
                    'device_type'    => $request->device_type,
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

                // Attach invoice_number so frontend receives the 8-digit number
                $order->invoice_number = $invoice->invoice_number;
                $orders[] = $order;
            }

            $paymentUrl = null;
            if ($request->payment_method === 'online' && $request->online_gateway === 'sslcommerz') {
                $sslcommerz = SslcommerzGateway::first();
                if ($sslcommerz && $sslcommerz->status) {
                    $mode = $sslcommerz->mode ?? 'test';
                    $domain = $mode === 'live' ? 'https://securepay.sslcommerz.com' : 'https://sandbox.sslcommerz.com';
                    $post_url = "$domain/gwprocess/v4/api.php";

                    $tran_id = $orders[0]->invoice_number;

                    $post_data = [
                        'store_id' => $sslcommerz->store_id,
                        'store_passwd' => $sslcommerz->store_password,
                        'total_amount' => $grandTotal,
                        'currency' => 'BDT',
                        'tran_id' => $tran_id,
                        'success_url' => route('sslcommerz.success'),
                        'fail_url' => route('sslcommerz.fail'),
                        'cancel_url' => route('sslcommerz.cancel'),
                        'ipn_url' => route('sslcommerz.ipn'),
                        // Customer Details
                        'cus_name' => $request->name,
                        'cus_email' => $request->email ?: $request->phone . '@jhrbazar.com',
                        'cus_add1' => $request->address,
                        'cus_city' => $request->city ?: 'Dhaka',
                        'cus_state' => $request->city ?: 'Dhaka',
                        'cus_postcode' => '1000',
                        'cus_country' => 'Bangladesh',
                        'cus_phone' => $request->phone,
                        // Shipping & Product profile
                        'shipping_method' => 'NO',
                        'num_of_item' => count($validatedItems),
                        'product_name' => 'Products ordered',
                        'product_category' => 'E-commerce',
                        'product_profile' => 'general',
                    ];

                    $response = \Illuminate\Support\Facades\Http::asForm()->post($post_url, $post_data);

                    if ($response->successful() && $response->json('status') === 'SUCCESS') {
                        $paymentUrl = $response->json('GatewayPageURL');
                    } else {
                        throw new \Exception('Payment Gateway Error: ' . ($response->json('failedreason') ?: 'Unable to initiate SSLCommerz payment.'));
                    }
                }
            }

            if ($request->payment_method === 'online' && $request->online_gateway === 'bkash') {
                $bkash = BkashGateway::first();
                if ($bkash && $bkash->status) {
                    $baseUrl = $bkash->base_url ?: ($bkash->mode === 'live' ? 'https://tokenized.pay.bka.sh/v1.2.0-beta' : 'https://tokenized.sandbox.bka.sh/v1.2.0-beta');
                    $baseUrl = rtrim($baseUrl, '/');
                    
                    // Call Grant Token
                    $authResponse = \Illuminate\Support\Facades\Http::withHeaders([
                        'username' => $bkash->username,
                        'password' => $bkash->password
                    ])->post("$baseUrl/tokenized/checkout/token/grant", [
                        'app_key' => $bkash->app_key,
                        'app_secret' => $bkash->app_secret_key
                    ]);

                    if (!$authResponse->successful() || !$authResponse->json('id_token')) {
                        throw new \Exception('bKash Authentication Failed: ' . ($authResponse->json('errorMessage') ?: 'Credentials check failed.'));
                    }

                    $idToken = $authResponse->json('id_token');
                    $invoiceNo = $orders[0]->invoice_number;
                    $callbackUrl = route('bkash.callback', ['invoice' => $invoiceNo]);

                    // Call Create Payment
                    $createResponse = \Illuminate\Support\Facades\Http::withHeaders([
                        'Authorization' => $idToken,
                        'X-APP-Key' => $bkash->app_key
                    ])->post("$baseUrl/tokenized/checkout/single/create", [
                        'mode' => '0011',
                        'payerReference' => $request->phone,
                        'callbackURL' => $callbackUrl,
                        'amount' => strval($grandTotalForFraud),
                        'currency' => 'BDT',
                        'intent' => 'sale',
                        'merchantInvoiceNumber' => $invoiceNo
                    ]);

                    if ($createResponse->successful() && $createResponse->json('bkashURL')) {
                        $paymentUrl = $createResponse->json('bkashURL');
                    } else {
                        throw new \Exception('bKash Payment Creation Failed: ' . ($createResponse->json('errorMessage') ?: 'Unable to initiate bKash payment.'));
                    }
                }
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
                $smsGateway = SmsGateway::where('status', true)->first();
                if ($smsGateway && $smsGateway->order_confirm) {
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
                'orders'  => $orders,
                'payment_url' => $paymentUrl
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

    private function calculateCouponDiscount($coupon, float $subTotal): float
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
        $fingerprint = $request->device_fingerprint;

        $query = Pointofsalepo::where('created_at', '>=', Carbon::now()->subMinutes($timeLimit));

        if ($fingerprint) {
            $query->where(function($q) use ($ip, $phone, $fingerprint) {
                $q->where('device_fingerprint', $fingerprint)
                  ->orWhere('ip_address', $ip)
                  ->orWhere('phone', $phone);
            });
        } else {
            if (Str::contains($checkType, 'IP')) {
                $query->where('ip_address', $ip);
            }
            if (Str::contains($checkType, 'Phone')) {
                $query->where('phone', $phone);
            }
        }

        // For Product check, we need to see if any item in the new order matches an item in recent orders
        if (Str::contains($checkType, 'Product')) {
            $newProductIds = collect($validatedItems)->pluck('id')->toArray();

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

    /**
     * Resolve correct gateway logo URL.
     */
    private function getGatewayLogoUrl($gateway, $key)
    {
        if (!$gateway || !$gateway->logo) {
            return $this->getDefaultLogo($key);
        }

        $logo = $gateway->logo;

        if (str_starts_with($logo, 'http://') || str_starts_with($logo, 'https://')) {
            return $logo;
        }

        if (str_starts_with($logo, 'storage/') || str_starts_with($logo, 'uploads/')) {
            return asset($logo);
        }

        return asset('storage/' . $logo);
    }

    /**
     * Get details of a placed order/invoice (public endpoint for checkout redirect pages).
     */
    public function orderDetails($invoice_no)
    {
        $invoice = PosInvoice::with(['customer.user', 'order'])->where('invoice_number', $invoice_no)->first();
        if (!$invoice) {
            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }
        return response()->json([
            'success' => true,
            'orders' => [$invoice]
        ]);
    }

    /**
     * SSLCommerz Payment Success Callback
     */
    public function successCallback(Request $request)
    {
        $tran_id = $request->input('tran_id');
        $val_id = $request->input('val_id');

        if (!$tran_id || !$val_id) {
            return redirect('/checkout?payment_error=true');
        }

        $sslcommerz = SslcommerzGateway::first();
        if ($sslcommerz) {
            $mode = $sslcommerz->mode ?? 'test';
            $domain = $mode === 'live' ? 'https://securepay.sslcommerz.com' : 'https://sandbox.sslcommerz.com';
            $validation_url = "$domain/validator/api/validationserverAPI.php";

            $response = \Illuminate\Support\Facades\Http::get($validation_url, [
                'val_id' => $val_id,
                'store_id' => $sslcommerz->store_id,
                'store_passwd' => $sslcommerz->store_password,
                'format' => 'json'
            ]);

            if ($response->successful() && in_array($response->json('status'), ['VALID', 'VALIDATED'])) {
                $invoice = PosInvoice::with('order')->where('invoice_number', $tran_id)->first();
                if ($invoice) {
                    $invoice->update(['payment_status' => 'Paid', 'received_amount' => $invoice->grand_total]);
                    if ($invoice->order) {
                        $invoice->order->update(['payment_status' => 'paid', 'status' => 'processing']);
                    }
                }
                return redirect('/order-success?invoice=' . $tran_id);
            }
        }

        return redirect('/checkout?payment_error=true');
    }

    /**
     * SSLCommerz Payment Failure Callback
     */
    public function failCallback(Request $request)
    {
        return redirect('/checkout?payment_failed=true');
    }

    /**
     * SSLCommerz Payment Cancel Callback
     */
    public function cancelCallback(Request $request)
    {
        return redirect('/checkout?payment_cancelled=true');
    }

    /**
     * SSLCommerz Payment IPN Callback
     */
    public function ipnCallback(Request $request)
    {
        $tran_id = $request->input('tran_id');
        $val_id = $request->input('val_id');

        if ($tran_id && $val_id) {
            $sslcommerz = SslcommerzGateway::first();
            if ($sslcommerz) {
                $mode = $sslcommerz->mode ?? 'test';
                $domain = $mode === 'live' ? 'https://securepay.sslcommerz.com' : 'https://sandbox.sslcommerz.com';
                $validation_url = "$domain/validator/api/validationserverAPI.php";

                $response = \Illuminate\Support\Facades\Http::get($validation_url, [
                    'val_id' => $val_id,
                    'store_id' => $sslcommerz->store_id,
                    'store_passwd' => $sslcommerz->store_password,
                    'format' => 'json'
                ]);

                if ($response->successful() && in_array($response->json('status'), ['VALID', 'VALIDATED'])) {
                    $invoice = PosInvoice::with('order')->where('invoice_number', $tran_id)->first();
                    if ($invoice) {
                        $invoice->update(['payment_status' => 'Paid', 'received_amount' => $invoice->grand_total]);
                        if ($invoice->order) {
                            $invoice->order->update(['payment_status' => 'paid', 'status' => 'processing']);
                        }
                    }
                    return response()->json(['status' => 'success', 'message' => 'IPN Processed.']);
                }
            }
        }
        return response()->json(['status' => 'failed', 'message' => 'Verification failed.'], 400);
    }

    /**
     * Process checkout (V1 endpoint).
     */
    public function process(Request $request)
    {
        return $this->placeOrder($request);
    }

    /**
     * Get OTP verification settings for checkout.
     */
    public function getOtpSettings()
    {
        $smsGateway = SmsGateway::where('status', true)->first();
        $otpSetting = \App\Models\Verificatiootpsettings::first();
        $otpRequired = $smsGateway && $otpSetting && $otpSetting->must_verify_account_on_order_placement;

        return response()->json([
            'success' => true,
            'otp_required' => (bool)$otpRequired,
            'min_phone_length' => $otpSetting->min_phone_length ?? 11,
            'max_phone_length' => $otpSetting->max_phone_length ?? 11,
            'sms_gateway_active' => (bool)$smsGateway,
        ]);
    }

    /**
     * Send OTP to a phone number for checkout verification.
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        $smsGateway = SmsGateway::where('status', true)->first();
        if (!$smsGateway) {
            return response()->json([
                'success' => false,
                'message' => 'এসএমএস গেটওয়ে কনফিগার করা নেই বা নিষ্ক্রিয় রয়েছে।'
            ], 400);
        }

        $otp = rand(100000, 999999);
        \Illuminate\Support\Facades\Cache::put('checkout_otp_' . $request->phone, $otp, 300); // 5 minutes

        $shopName = GenaralSetting::first()->shop_name ?? 'JhrBazar';
        $message = "প্রিয় গ্রাহক, " . $shopName . "-এ আপনার অর্ডারটি নিশ্চিত করতে ওটিপি কোডটি ব্যবহার করুন: " . $otp . "। ধন্যবাদ!";

        try {
            $sent = SmsService::send($request->phone, $message);
            if ($sent) {
                return response()->json([
                    'success' => true,
                    'message' => 'আপনার মোবাইলে ওটিপি (OTP) কোড পাঠানো হয়েছে।'
                ]);
            }
            return response()->json([
                'success' => false,
                'message' => 'ওটিপি পাঠাতে ব্যর্থ হয়েছে। অনুগ্রহ করে মোবাইল নম্বরটি পরীক্ষা করুন।'
            ], 500);
        } catch (\Exception $e) {
            \Log::error("Failed to send OTP SMS from API: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'ওটিপি সার্ভারে অভ্যন্তরীণ ত্রুটি দেখা দিয়েছে।'
            ], 500);
        }
    }

    /**
     * Verify checkout OTP for a phone number.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'otp_code' => 'required|string',
        ]);

        $cachedOtp = \Illuminate\Support\Facades\Cache::get('checkout_otp_' . $request->phone);

        if (!$cachedOtp || $cachedOtp != $request->otp_code) {
            return response()->json([
                'success' => false,
                'message' => 'অবৈধ বা মেয়াদোত্তীর্ণ ওটিপি (OTP) কোড।'
            ], 422);
        }

        // Keep a verification flag in cache for 10 minutes so that order placement can check it
        \Illuminate\Support\Facades\Cache::put('checkout_otp_verified_' . $request->phone, true, 600);
        
        // Remove OTP code from cache
        \Illuminate\Support\Facades\Cache::forget('checkout_otp_' . $request->phone);

        return response()->json([
            'success' => true,
            'message' => 'ওটিপি (OTP) সফলভাবে যাচাই করা হয়েছে।'
        ]);
    }

    /**
     * bKash Payment Callback
     */
    public function bkashCallback(Request $request)
    {
        $status = $request->input('status');
        $paymentID = $request->input('paymentID');
        $invoiceNo = $request->input('invoice');

        if (!$invoiceNo) {
            return redirect('/checkout?payment_error=true');
        }

        if ($status === 'success' && $paymentID) {
            $bkash = BkashGateway::first();
            if ($bkash) {
                $baseUrl = $bkash->mode === 'live' ? 'https://tokenized.pay.bka.sh/v1.2.0-beta' : 'https://tokenized.sandbox.bka.sh/v1.2.0-beta';
                
                // Get Grant Token
                $authResponse = \Illuminate\Support\Facades\Http::withHeaders([
                    'username' => $bkash->username,
                    'password' => $bkash->password
                ])->post("$baseUrl/tokenized/checkout/token/grant", [
                    'app_key' => $bkash->app_key,
                    'app_secret' => $bkash->app_secret_key
                ]);

                if ($authResponse->successful() && $authResponse->json('id_token')) {
                    $idToken = $authResponse->json('id_token');

                    // Call Execute Payment
                    $executeResponse = \Illuminate\Support\Facades\Http::withHeaders([
                        'Authorization' => $idToken,
                        'X-APP-Key' => $bkash->app_key
                    ])->post("$baseUrl/tokenized/checkout/single/execute", [
                        'paymentID' => $paymentID
                    ]);

                    if ($executeResponse->successful() && in_array($executeResponse->json('statusCode'), ['0000', '2062'])) {
                        // Payment Successful
                        $invoice = PosInvoice::with('order')->where('invoice_number', $invoiceNo)->first();
                        if ($invoice) {
                            $invoice->update(['payment_status' => 'Paid', 'received_amount' => $invoice->grand_total]);
                            if ($invoice->order) {
                                $invoice->order->update(['payment_status' => 'paid', 'status' => 'processing']);
                            }
                        }
                        return redirect('/order-success?invoice=' . $invoiceNo);
                    }
                }
            }
            return redirect('/checkout?payment_failed=true');
        } elseif ($status === 'cancel') {
            return redirect('/checkout?payment_cancelled=true');
        }

        return redirect('/checkout?payment_failed=true');
    }
}
