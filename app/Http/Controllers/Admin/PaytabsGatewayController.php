<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaytabsGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaytabsGatewayController extends Controller
{
    public function update(Request $request)
    {
        $gateway = PaytabsGateway::firstOrNew(['id' => 1]);

        $gateway->mode = $request->mode;
        $gateway->base_url = $request->base_url;
        $gateway->currency = $request->currency;
        $gateway->profile_id = $request->profile_id;
        $gateway->server_key = $request->server_key;
        $gateway->title = $request->title;
        $gateway->status = $request->boolean('status');

        if ($request->hasFile('logo')) {
            if ($gateway->logo && Storage::disk('public')->exists($gateway->logo)) {
                Storage::disk('public')->delete($gateway->logo);
            }
            $gateway->logo = $request->file('logo')->store('gateway-logos', 'public');
        }

        $gateway->save();

        return redirect()->back()->with('paytabs_success', 'Settings updated successfully!');
    }

    public function toggleStatus()
    {
        $gateway = PaytabsGateway::firstOrNew(['id' => 1]);
        $gateway->status = !$gateway->status;
        $gateway->save();
        return response()->json(['status' => $gateway->status]);
    }
}
