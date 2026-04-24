<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SmsGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SmsGatewayController extends Controller
{
    public function update(Request $request)
    {
        $gateway = SmsGateway::firstOrNew(['id' => 1]);

        $gateway->url = $request->url;
        $gateway->api_key = $request->api_key;
        $gateway->sender_id = $request->sender_id;
        $gateway->status = $request->boolean('status');

        if ($request->hasFile('logo')) {
            if ($gateway->logo && Storage::disk('public')->exists($gateway->logo)) {
                Storage::disk('public')->delete($gateway->logo);
            }
            $gateway->logo = $request->file('logo')->store('gateway-logos', 'public');
        }

        $gateway->save();

        return redirect()->back()->with('sms_success', 'Settings updated successfully!');
    }

    public function toggleStatus()
    {
        $gateway = SmsGateway::firstOrNew(['id' => 1]);
        $gateway->status = !$gateway->status;
        $gateway->save();
        return response()->json(['status' => $gateway->status]);
    }
}
