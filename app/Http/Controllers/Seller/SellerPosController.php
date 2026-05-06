<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Alltaxe;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\GenaralSetting;
use App\Models\PosInvoice;
use App\Models\Pointofsalepo;
use App\Models\Product;
use App\Models\SellerDigitalProduct;
use App\Models\Shop;
use App\Models\SellerVoucher;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class SellerPosController extends Controller
{
    public function index()
    {
        $brands     = Brand::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $taxes      = Alltaxe::where('status', 1)->get();
        $settings   = GenaralSetting::first();

        // Customers could be global or seller-specific, usually global in this app
        $customers = Customer::with('user')->orderBy('id', 'desc')->get();
        $shippingCharges = \App\Models\ShippingCharge::where('status', 1)->get();
        return view('seller.pos.index', compact('brands', 'categories', 'taxes', 'settings', 'customers', 'shippingCharges'));
    }

    public function getProducts(Request $request): JsonResponse
    {
        $shop = Shop::where('user_id', Auth::id())->first();
        $shopId = $shop ? $shop->id : 0;

        $search = trim($request->search);
        $brandId = $request->brand_id;
        $categoryId = $request->category_id;

        // 1. Normal Products Query (General products associated with this shop)
        $normalQuery = DB::table('products')
            ->select([
                'id', 'name', 'sku', 'barcode',
                'selling_price', 'discount_price',
                'stock_quantity', 'thumbnail',
                DB::raw("'normal' as product_type"),
                'category_id', 'brand_id'
            ])
            ->where('shop_id', $shopId)
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0);

        // 2. Seller Products Query (Specific non-digital products uploaded by this seller)
        $sellerQuery = DB::table('seller_products')
            ->select([
                'id', 'name', 'sku', 'barcode',
                'selling_price', 'discount_price',
                'stock_quantity', 'thumbnail',
                DB::raw("'seller' as product_type"),
                'category_id', 'brand_id'
            ])
            ->where('seller_id', Auth::id())
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0);

        // 3. Digital Products Query
        $digitalQuery = DB::table('seller_digital_products')
            ->select([
                'id', 'name', 'sku', DB::raw("sku as barcode"),
                'selling_price', 'discount_price',
                'stock_quantity', 'thumbnail',
                DB::raw("'digital' as product_type"),
                'category_id', 'brand_id'
            ])
            ->where('seller_id', Auth::id())
            ->where('is_active', true);

        // Apply filters to all
        if ($search) {
            $normalQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%")->orWhere('barcode', 'like', "%{$search}%");
            });
            $sellerQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%")->orWhere('barcode', 'like', "%{$search}%");
            });
            $digitalQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($brandId) {
            $normalQuery->where('brand_id', $brandId);
            $sellerQuery->where('brand_id', $brandId);
            $digitalQuery->where('brand_id', $brandId);
        }

        if ($categoryId) {
            $normalQuery->where('category_id', $categoryId);
            $sellerQuery->where('category_id', $categoryId);
            $digitalQuery->where('category_id', $categoryId);
        }

        // Combine using Union
        $combinedQuery = $normalQuery->union($sellerQuery)->union($digitalQuery);

        // We need a wrapper to paginate a union
        $finalQuery = DB::table(DB::raw("({$combinedQuery->toSql()}) as combined"))
            ->mergeBindings($combinedQuery)
            ->orderBy('name');

        $products = $finalQuery->paginate(12);

        return response()->json($products);
    }

    public function storeCustomer(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'first_name'    => 'required|string|max:100',
                'last_name'     => 'nullable|string|max:100',
                'phone'         => 'required|string|max:20|unique:users,phone',
                'email'         => 'required|email|max:191|unique:users,email',
                'password'      => 'required|string|min:6|confirmed',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $fullName = trim($request->first_name . ' ' . ($request->last_name ?? ''));
            $user = User::create([
                'name'      => $fullName,
                'last_name' => $request->last_name,
                'phone'     => $request->phone,
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'role'      => 'customer',
                'status'    => 1,
            ]);
            $customer = Customer::create([
                'user_id'    => $user->id,
                'first_name' => $request->first_name,
                'last_name'  => $request->last_name,
            ]);
            DB::commit();
            return response()->json(['success' => true, 'id' => $customer->id, 'name' => $fullName]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function applyCoupon(Request $request): JsonResponse
    {
        $voucher = SellerVoucher::where('voucher_code', $request->coupon_code)
            ->where('seller_id', Auth::id())
            ->where('status', 1)
            ->first();

        if (!$voucher) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired promo code.']);
        }

        // Check dates
        $now = now();
        $start = \Carbon\Carbon::parse($voucher->start_date->format('Y-m-d') . ' ' . $voucher->start_time);
        $end = \Carbon\Carbon::parse($voucher->expired_date->format('Y-m-d') . ' ' . $voucher->expired_time);

        if ($now->lt($start)) return response()->json(['success' => false, 'message' => 'This promo code is not active yet.']);
        if ($now->gt($end))   return response()->json(['success' => false, 'message' => 'This promo code has expired.']);

        // Check minimum order amount
        if ($request->sub_total < $voucher->minimum_order_amount) {
            return response()->json([
                'success' => false, 
                'message' => 'Minimum order amount for this code is ৳' . number_format($voucher->minimum_order_amount, 2)
            ]);
        }

        $discount = 0;
        if ($voucher->discount_type === 'percentage') {
            $discount = round($request->sub_total * $voucher->discount / 100, 2);
            if ($voucher->maximum_discount_amount > 0 && $discount > $voucher->maximum_discount_amount) {
                $discount = $voucher->maximum_discount_amount;
            }
        } else {
            $discount = (float)$voucher->discount;
        }

        return response()->json([
            'success'  => true, 
            'discount' => $discount, 
            'message'  => 'Promo code applied successfully!'
        ]);
    }

    public function placeOrder(Request $request): JsonResponse
    {
        return $this->processOrder($request, 'completed');
    }

    public function draft(Request $request): JsonResponse
    {
        return $this->processOrder($request, 'draft');
    }

    public function salesIndex(Request $request)
    {
        $query = PosInvoice::where('seller_id', Auth::id())->with('customer.user')->orderBy('id', 'desc');
        $invoices = $query->paginate(15);
        return view('seller.pos.saleshistory', compact('invoices'));
    }

    public function draftIndex(Request $request)
    {
        $drafts = Pointofsalepo::where('seller_id', Auth::id())->where('status', 'draft')->with('customer.user')->paginate(15);
        return view('seller.pos.draft', compact('drafts'));
    }

    public function getDraft(Pointofsalepo $draft): JsonResponse
    {
        if ($draft->seller_id !== Auth::id()) return response()->json(['success' => false], 403);
        return response()->json(['success' => true, 'items' => $draft->items, 'customer_id' => $draft->customer_id]);
    }

    private function processOrder(Request $request, string $status): JsonResponse
    {
        DB::beginTransaction();
        try {
            $subTotal = 0; $itemSnapshots = [];
            $shop = Shop::where('user_id', Auth::id())->first();
            $shopId = $shop->id ?? 0;

            foreach ($request->items as $item) {
                $type = $item['product_type'] ?? 'normal';
                
                if ($type === 'digital') {
                    $product = SellerDigitalProduct::where('seller_id', Auth::id())->findOrFail($item['id']);
                } elseif ($type === 'seller') {
                    $product = DB::table('seller_products')->where('seller_id', Auth::id())->where('id', $item['id'])->first();
                    if ($product) {
                        DB::table('seller_products')->where('id', $product->id)->decrement('stock_quantity', $item['qty']);
                    } else {
                        throw new \Exception("Seller product not found.");
                    }
                } else {
                    $product = Product::where('shop_id', $shopId)->findOrFail($item['id']);
                    $product->decrement('stock_quantity', $item['qty']);
                }

                $price = $product->discount_price > 0 ? $product->discount_price : $product->selling_price;
                $subTotal += ($price * $item['qty']);
                $itemSnapshots[] = [
                    'id'           => $product->id, 
                    'name'         => $product->name, 
                    'price'        => $price, 
                    'qty'          => $item['qty'],
                    'product_type' => $type,
                    'thumbnail'    => $product->thumbnail
                ];
            }

            $deliveryCharge = $request->delivery_charge ?? 0;
            $grandTotal = ($subTotal - ($request->discount ?? 0)) + ($request->tax_amount ?? 0) + $deliveryCharge;

            $order = Pointofsalepo::create([
                'seller_id'       => Auth::id(),
                'customer_id'     => $request->customer_id ?: null,
                'items'           => $itemSnapshots,
                'sub_total'       => $subTotal,
                'discount'        => $request->discount ?? 0,
                'tax_amount'      => $request->tax_amount ?? 0,
                'delivery_charge' => $deliveryCharge,
                'grand_total'     => $grandTotal,
                'payment_method'  => $request->payment_method ?? 'cash',
                'status'          => $status,
                'coupon_code'     => $request->coupon_code,
            ]);

            if ($status === 'completed') {
                $invoice = PosInvoice::create([
                    'invoice_number'   => PosInvoice::generateInvoiceNumber(),
                    'seller_id'        => Auth::id(),
                    'pointofsalepo_id' => $order->id,
                    'customer_id'      => $order->customer_id,
                    'items'            => $itemSnapshots,
                    'grand_total'      => $order->grand_total,
                    'payment_method'   => $order->payment_method,
                    'sub_total'        => $order->sub_total,
                    'discount'         => $order->discount,
                    'tax_amount'       => $order->tax_amount,
                    'delivery_charge'  => $order->delivery_charge,
                ]);
                
                DB::commit();
                return response()->json([
                    'success' => true, 
                    'message' => 'Order placed successfully!',
                    'invoice_id' => $invoice->id
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Draft saved successfully!']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function printInvoice($id)
    {
        $invoice = PosInvoice::where('seller_id', Auth::id())->with(['customer.user', 'order'])->findOrFail($id);
        $shop = Shop::where('user_id', Auth::id())->first();
        return view('seller.pos.invoice', compact('invoice', 'shop'));
    }
}
