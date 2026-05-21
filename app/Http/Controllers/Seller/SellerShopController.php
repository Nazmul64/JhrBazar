<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Pointofsalepo;
use App\Models\Product;
use App\Models\SellerDigitalProduct;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class SellerShopController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $shop = Shop::where('user_id', $user->id)->first();
        
        // Dynamic counts
        $normalProductCount = \App\Models\SellerProduct::where('seller_id', $user->id)->count();
        $digitalProductCount = SellerDigitalProduct::where('seller_id', $user->id)->count();
        $totalProducts = $normalProductCount + $digitalProductCount;
        
        $totalOrders = \App\Models\Pointofsalepo::where('seller_id', $user->id)->count(); 
        $totalReviews = \App\Models\Review::where('shop_id', $shop->id ?? 0)->count();

        return view('seller.shop.index', compact('user', 'shop', 'totalProducts', 'totalOrders', 'totalReviews'));
    }

    public function edit()
    {
        $user = Auth::user();
        $shop = Shop::where('user_id', $user->id)->first();
        return view('seller.shop.edit', compact('user', 'shop'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $shop = Shop::where('user_id', $user->id)->first();

        $request->validate([
            'name'                => 'required|string|max:191',
            'first_name'          => 'required|string|max:100',
            'last_name'           => 'nullable|string|max:100',
            'phone'               => 'required|string|max:20',
            'email'               => 'required|email|max:191',
            'address'             => 'required|string',
            'min_order_amount'    => 'required|numeric',
            'opening_time'        => 'required',
            'closing_time'        => 'required',
            'estimated_delivery'  => 'required',
            'order_prefix'        => 'nullable|string|max:10',
            'description'         => 'nullable|string',
            'logo'                => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'banner'              => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        // Update User info
        $user->update([
            'name'      => $request->first_name . ' ' . $request->last_name,
            'last_name' => $request->last_name,
            'phone'     => $request->phone,
            'email'     => $request->email,
            'gender'    => $request->gender,
        ]);

        if ($request->hasFile('profile_image')) {
            if ($user->profile_image && File::exists(public_path($user->profile_image))) {
                File::delete(public_path($user->profile_image));
            }
            $image = $request->file('profile_image');
            $name = time() . '_profile.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/profile'), $name);
            $user->update(['profile_image' => 'uploads/profile/' . $name]);
        }

        // Update Shop info
        $shopData = [
            'name'               => $request->name,
            'address'            => $request->address,
            'min_order_amount'   => $request->min_order_amount,
            'opening_time'       => $request->opening_time,
            'closing_time'       => $request->closing_time,
            'estimated_delivery' => $request->estimated_delivery,
            'order_prefix'       => $request->order_prefix,
            'description'        => $request->description,
            'latitude'           => $request->latitude,
            'longitude'          => $request->longitude,
        ];

        if ($request->hasFile('logo')) {
            if ($shop->logo && File::exists(public_path($shop->logo))) {
                File::delete(public_path($shop->logo));
            }
            $image = $request->file('logo');
            $name = time() . '_logo.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/shop'), $name);
            $shopData['logo'] = 'uploads/shop/' . $name;
        }

        if ($request->hasFile('banner')) {
            if ($shop->banner && File::exists(public_path($shop->banner))) {
                File::delete(public_path($shop->banner));
            }
            $image = $request->file('banner');
            $name = time() . '_banner.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/shop'), $name);
            $shopData['banner'] = 'uploads/shop/' . $name;
        }

        $shop->update($shopData);

        return redirect()->route('seller.shop.index')->with('success', 'Shop updated successfully!');
    }
}
