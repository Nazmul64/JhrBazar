<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BkashPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BkashPaymentController extends Controller
{
    public function update(Request $request)
    {
        $gateway = BkashPayment::firstOrNew(['id' => 1]);

        $gateway->username = $request->username;
        $gateway->app_key = $request->app_key;
        $gateway->app_secret = $request->app_secret;
        $gateway->base_url = $request->base_url;
        $gateway->password = $request->password;
        $gateway->status = $request->boolean('status');

        if ($request->hasFile('logo')) {
            if ($gateway->logo && Storage::disk('public')->exists($gateway->logo)) {
                Storage::disk('public')->delete($gateway->logo);
            }
            $gateway->logo = $request->file('logo')->store('gateway-logos', 'public');
        }

        $gateway->save();

        return redirect()->back()->with('bkash_payment_success', 'Settings updated successfully!');
    }

    public function toggleStatus()
    {
        $gateway = BkashPayment::firstOrNew(['id' => 1]);
        $gateway->status = !$gateway->status;
        $gateway->save();
        return response()->json(['status' => $gateway->status]);
    }
}
