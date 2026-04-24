<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JazzcashGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JazzcashGatewayController extends Controller
{
    public function update(Request $request)
    {
        $gateway = JazzcashGateway::firstOrNew(['id' => 1]);

        $gateway->mode = $request->mode;
        $gateway->base_url = $request->base_url;
        $gateway->password = $request->password;
        $gateway->merchant_id = $request->merchant_id;
        $gateway->integrity_salt = $request->integrity_salt;
        $gateway->title = $request->title;
        $gateway->status = $request->boolean('status');

        if ($request->hasFile('logo')) {
            if ($gateway->logo && Storage::disk('public')->exists($gateway->logo)) {
                Storage::disk('public')->delete($gateway->logo);
            }
            $gateway->logo = $request->file('logo')->store('gateway-logos', 'public');
        }

        $gateway->save();

        return redirect()->back()->with('jazzcash_success', 'Settings updated successfully!');
    }

    public function toggleStatus()
    {
        $gateway = JazzcashGateway::firstOrNew(['id' => 1]);
        $gateway->status = !$gateway->status;
        $gateway->save();
        return response()->json(['status' => $gateway->status]);
    }
}
