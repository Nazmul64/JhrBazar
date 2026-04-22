<?php
// app/Http/Controllers/Admin/BusinessSettingController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use Illuminate\Http\Request;

class BusinessSettingController extends Controller
{
    // ── index ────────────────────────────────────────────────────
    public function index()
    {
        $setting = BusinessSetting::first();
        return view('admin.businesssettings.index', compact('setting'));
    }

    // ── store (first-time create) ────────────────────────────────
    public function store(Request $request)
    {
        $data = $this->validated($request);
        BusinessSetting::create($data);

        return redirect()->route('admin.businesssettings.index')
            ->with('success', 'Business settings saved successfully!');
    }

    // ── update ───────────────────────────────────────────────────
    public function update(Request $request, string $id)
    {
        $setting = BusinessSetting::findOrFail($id);
        $data    = $this->validated($request);
        $setting->update($data);

        return redirect()->route('admin.businesssettings.index')
            ->with('success', 'Business settings updated successfully!');
    }

    // ── toggleStatus (AJAX) ──────────────────────────────────────
    public function toggleStatus(Request $request, string $id)
    {
        $setting = BusinessSetting::findOrFail($id);
        $field   = $request->input('field');

        $allowed = [
            'cash_on_delivery', 'online_payment',
            'commission_enabled', 'subscription_enabled',
            'pos_in_shop_panel', 'shop_registration',
            'need_product_approval', 'update_product_approval',
        ];

        if (!in_array($field, $allowed)) {
            return response()->json(['error' => 'Invalid field'], 422);
        }

        $setting->$field = !$setting->$field;
        $setting->save();

        return response()->json(['success' => true, 'status' => $setting->$field]);
    }

    // ── destroy ──────────────────────────────────────────────────
    public function destroy(string $id)
    {
        BusinessSetting::findOrFail($id)->delete();

        return redirect()->route('admin.businesssettings.index')
            ->with('success', 'Settings deleted.');
    }

    // ── private helper ───────────────────────────────────────────
    private function validated(Request $request): array
    {
        $request->validate([
            'company_name'              => 'nullable|string|max:255',
            'company_email'             => 'nullable|email|max:255',
            'company_phone'             => 'nullable|string|max:20',
            'business_model'            => 'nullable|in:single_shop,multi_shop',
            'currency_position'         => 'nullable|in:left,right',
            'timezone'                  => 'nullable|string|max:100',
            'return_order_within_days'  => 'nullable|integer|min:0',
            'commission'                => 'nullable|numeric|min:0',
            'commission_type'           => 'nullable|in:fixed,percentage',
            'commission_charge'         => 'nullable|in:per_order,per_item',
            'min_withdraw_amount'       => 'nullable|numeric|min:0',
            'max_withdraw_amount'       => 'nullable|numeric|min:0',
            'min_day_withdraw_request'  => 'nullable|integer|min:0',
        ]);

        return [
            'company_name'             => $request->company_name,
            'company_email'            => $request->company_email,
            'company_phone'            => $request->company_phone,
            'business_model'           => $request->business_model ?? 'multi_shop',
            'currency_position'        => $request->currency_position ?? 'left',
            'timezone'                 => $request->timezone,
            'return_order_within_days' => $request->return_order_within_days ?? 3,
            'cash_on_delivery'         => $request->has('cash_on_delivery') ? 1 : 0,
            'online_payment'           => $request->has('online_payment') ? 1 : 0,
            'commission_enabled'       => $request->has('commission_enabled') ? 1 : 0,
            'subscription_enabled'     => $request->has('subscription_enabled') ? 1 : 0,
            'commission'               => $request->commission ?? 0,
            'commission_type'          => $request->commission_type ?? 'fixed',
            'commission_charge'        => $request->commission_charge ?? 'per_order',
            'pos_in_shop_panel'        => $request->has('pos_in_shop_panel') ? 1 : 0,
            'shop_registration'        => $request->has('shop_registration') ? 1 : 0,
            'need_product_approval'    => $request->has('need_product_approval') ? 1 : 0,
            'update_product_approval'  => $request->has('update_product_approval') ? 1 : 0,
            'min_withdraw_amount'      => $request->min_withdraw_amount ?? 0,
            'max_withdraw_amount'      => $request->max_withdraw_amount,
            'min_day_withdraw_request' => $request->min_day_withdraw_request,
        ];
    }
}
