<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promocode;
use App\Models\Shop; // Shop model থাকলে use করুন
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PromocodeController extends Controller
{
    /**
     * সব promo codes list করে
     */
    public function index(): View
    {
        $promocodes = Promocode::latest()->get();
        return view('admin.promocode.index', compact('promocodes'));
    }

    /**
     * নতুন promo code তৈরির form দেখায়
     */
    public function create(): View
    {
        // Shop model থাকলে shops load করুন, না থাকলে empty array
        $shops = [];
        // $shops = Shop::where('status', true)->get();

        return view('admin.promocode.create', compact('shops'));
    }

    /**
     * নতুন promo code store করে
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'shop_ids'               => 'nullable|array',
            'coupon_code'            => 'required|string|unique:promocodes,coupon_code',
            'discount_type'          => 'required|in:amount,percentage',
            'discount'               => 'required|numeric|min:0',
            'minimum_order_amount'   => 'required|numeric|min:0',
            'limit_for_single_user'  => 'nullable|integer|min:1',
            'maximum_discount_amount'=> 'nullable|numeric|min:0',
            'start_date'             => 'required|date',
            'start_time'             => 'required',
            'expired_date'           => 'required|date|after_or_equal:start_date',
            'expired_time'           => 'required',
        ]);

        // shop_ids array হলে JSON করে রাখুন
        if (!empty($validated['shop_ids'])) {
            $validated['shop_ids'] = json_encode($validated['shop_ids']);
        }

        Promocode::create($validated);

        return redirect()->route('admin.promocode.index')
                         ->with('success', 'Promo code created successfully!');
    }

    /**
     * Edit form দেখায়
     */
    public function edit(Promocode $promocode): View
    {
        $shops = [];
        // $shops = Shop::where('status', true)->get();

        return view('admin.promocode.edit', compact('promocode', 'shops'));
    }

    /**
     * Promo code update করে
     */
    public function update(Request $request, Promocode $promocode): RedirectResponse
    {
        $validated = $request->validate([
            'shop_ids'               => 'nullable|array',
            'coupon_code'            => 'required|string|unique:promocodes,coupon_code,' . $promocode->id,
            'discount_type'          => 'required|in:amount,percentage',
            'discount'               => 'required|numeric|min:0',
            'minimum_order_amount'   => 'required|numeric|min:0',
            'limit_for_single_user'  => 'nullable|integer|min:1',
            'maximum_discount_amount'=> 'nullable|numeric|min:0',
            'start_date'             => 'required|date',
            'start_time'             => 'required',
            'expired_date'           => 'required|date|after_or_equal:start_date',
            'expired_time'           => 'required',
        ]);

        if (!empty($validated['shop_ids'])) {
            $validated['shop_ids'] = json_encode($validated['shop_ids']);
        } else {
            $validated['shop_ids'] = null;
        }

        $promocode->update($validated);

        return redirect()->route('admin.promocode.index')
                         ->with('success', 'Promo code updated successfully!');
    }

    /**
     * Promo code delete করে
     */
    public function destroy(Promocode $promocode): RedirectResponse
    {
        $promocode->delete();

        return redirect()->route('admin.promocode.index')
                         ->with('success', 'Promo code deleted successfully!');
    }

    /**
     * Status toggle করে (active/inactive)
     */
    public function toggleStatus(int $id): \Illuminate\Http\JsonResponse
    {
        $promocode = Promocode::findOrFail($id);
        $promocode->update(['status' => !$promocode->status]);

        return response()->json([
            'success' => true,
            'status'  => $promocode->status,
            'message' => 'Status updated successfully!',
        ]);
    }
}
