<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaystackGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaystackGatewayController extends Controller
{
    public function update(Request $request)
    {
        $gateway = PaystackGateway::firstOrNew(['id' => 1]);

        $gateway->mode = $request->mode;
        $gateway->public_key = $request->public_key;
        $gateway->secret_key = $request->secret_key;
        $gateway->merchant_email = $request->merchant_email;
        $gateway->title = $request->title;
        $gateway->status = $request->boolean('status');

        if ($request->hasFile('logo')) {
            if ($gateway->logo && Storage::disk('public')->exists($gateway->logo)) {
                Storage::disk('public')->delete($gateway->logo);
            }
            $gateway->logo = $request->file('logo')->store('gateway-logos', 'public');
        }

        $gateway->save();

        return redirect()->back()->with('paystack_success', 'Settings updated successfully!');
    }

    public function toggleStatus()
    {
        $gateway = PaystackGateway::firstOrNew(['id' => 1]);
        $gateway->status = !$gateway->status;
        $gateway->save();
        return response()->json(['status' => $gateway->status]);
    }
}
