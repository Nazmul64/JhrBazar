<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RazorpayGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RazorpayGatewayController extends Controller
{
    public function update(Request $request)
    {
        $gateway = RazorpayGateway::firstOrNew(['id' => 1]);

        $gateway->mode = $request->mode;
        $gateway->key = $request->key;
        $gateway->secret = $request->secret;
        $gateway->title = $request->title;
        $gateway->status = $request->boolean('status');

        if ($request->hasFile('logo')) {
            if ($gateway->logo && Storage::disk('public')->exists($gateway->logo)) {
                Storage::disk('public')->delete($gateway->logo);
            }
            $gateway->logo = $request->file('logo')->store('gateway-logos', 'public');
        }

        $gateway->save();

        return redirect()->back()->with('razorpay_success', 'Settings updated successfully!');
    }

    public function toggleStatus()
    {
        $gateway = RazorpayGateway::firstOrNew(['id' => 1]);
        $gateway->status = !$gateway->status;
        $gateway->save();
        return response()->json(['status' => $gateway->status]);
    }
}
