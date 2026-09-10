<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pointofsalepo;
use App\Models\PosInvoice;
use App\Models\Wishlist;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

// ─── Helper: strip leading +88 / 880 so phone comparisons don't fail ────────
function normalizePhone(?string $phone): ?string
{
    if (!$phone) return null;
    // Remove non-digit prefix chars so +8801XXXXXXXX → 01XXXXXXXX
    $phone = preg_replace('/^\+88/', '', $phone);
    $phone = preg_replace('/^880/', '0', $phone);
    return $phone;
}

class CustomerDashboardController extends Controller
{
    /**
     * Get dashboard summary data.
     */
    public function index()
    {
        $user  = auth('sanctum')->user();

        // Normalize phone for flexible matching (e.g. +8801XXXXXXXX ↔ 01XXXXXXXX)
        $rawPhone   = $user->phone ?? '';
        $cleanPhone = normalizePhone($rawPhone);
        $phones     = array_filter(array_unique([
            $rawPhone,
            $cleanPhone,
            $cleanPhone ? '+88' . $cleanPhone : null,
        ]));

        $orderCount = Pointofsalepo::where(function ($q) use ($user, $phones) {
            $q->where('customer_id', $user->id);
            if (count($phones)) {
                $q->orWhereIn('phone', array_values($phones));
            }
        })->count();

        $wishlistCount = Wishlist::where('user_id', $user->id)->count();

        return response()->json([
            'success' => true,
            'data'    => [
                'order_count'    => $orderCount,
                'wishlist_count' => $wishlistCount,
            ]
        ]);
    }

    /**
     * Get all orders for the customer.
     */
    public function orders()
    {
        $user  = auth('sanctum')->user();

        // Normalize phone for flexible matching
        $rawPhone   = $user->phone ?? '';
        $cleanPhone = normalizePhone($rawPhone);
        $phones     = array_filter(array_unique([
            $rawPhone,
            $cleanPhone,
            $cleanPhone ? '+88' . $cleanPhone : null,
        ]));

        $orders = Pointofsalepo::with('invoice')
            ->where(function ($q) use ($user, $phones) {
                $q->where('customer_id', $user->id);
                if (count($phones)) {
                    $q->orWhereIn('phone', array_values($phones));
                }
            })
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $orders
        ]);
    }

    /**
     * Get wishlist products.
     */
    public function wishlist()
    {
        $user = auth('sanctum')->user();
        $wishlistItems = Wishlist::where('user_id', $user->id)
            ->latest()
            ->get();

        // Group IDs by product_type
        $groupedIds = [];
        foreach ($wishlistItems as $item) {
            $groupedIds[$item->product_type][] = $item->product_id;
        }

        // Fetch products in batch
        $adminProducts = [];
        $sellerProducts = [];
        $digitalProducts = [];

        if (!empty($groupedIds['admin'])) {
            $adminProducts = \App\Models\Product::whereIn('id', $groupedIds['admin'])->get()->keyBy('id');
        }
        if (!empty($groupedIds['seller'])) {
            $sellerProducts = \App\Models\SellerProduct::whereIn('id', $groupedIds['seller'])->get()->keyBy('id');
        }
        if (!empty($groupedIds['digital'])) {
            $digitalProducts = \App\Models\DigitalProduct::whereIn('id', $groupedIds['digital'])->get()->keyBy('id');
        }

        $products = [];
        foreach ($wishlistItems as $item) {
            $product = null;
            if ($item->product_type === 'admin') {
                $product = $adminProducts[$item->product_id] ?? null;
            } elseif ($item->product_type === 'seller') {
                $product = $sellerProducts[$item->product_id] ?? null;
            } elseif ($item->product_type === 'digital') {
                $product = $digitalProducts[$item->product_id] ?? null;
            }

            if ($product) {
                $products[] = $this->mapWishlistProduct($product, $item->product_type);
            }
        }

        return response()->json([
            'success' => true,
            'data'    => $products
        ]);
    }

    private function mapWishlistProduct($product, $type)
    {
        return [
            'id'           => $product->id,
            'name'         => $product->name,
            'thumbnail'    => $product->thumbnail ? (str_starts_with($product->thumbnail, 'http') ? $product->thumbnail : '/' . ltrim($product->thumbnail, '/')) : '/placeholder.jpg',
            'price'        => $product->selling_price,
            'product_type' => $type
        ];
    }

    /**
     * Update customer profile.
     */
    public function updateProfile(Request $request)
    {
        $user = auth('sanctum')->user();

        $validator = Validator::make($request->all(), [
            'name'          => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone'         => 'nullable|string|max:20',
            'address'       => 'nullable|string|max:1000',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $data = [
            'name'    => $request->name,
            'email'   => $request->email,
            'phone'   => $request->phone,
            'address' => $request->address,
        ];

        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            $imageName = time() . '_' . $user->id . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('uploads/profile_images');
            
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            
            $image->move($destinationPath, $imageName);
            $data['profile_image'] = $imageName;
            
            // Delete old image if exists
            if ($user->profile_image && file_exists(public_path('uploads/profile_images/' . $user->profile_image))) {
                @unlink(public_path('uploads/profile_images/' . $user->profile_image));
            }
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'প্রোফাইল সফলভাবে আপডেট করা হয়েছে।',
            'user'    => $user->fresh()
        ]);
    }

    /**
     * Change password.
     */
    public function updatePassword(Request $request)
    {
        $user = auth('sanctum')->user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password'         => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'বর্তমান পাসওয়ার্ডটি সঠিক নয়।'
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'পাসওয়ার্ড সফলভাবে পরিবর্তন করা হয়েছে।'
        ]);
    }
}
