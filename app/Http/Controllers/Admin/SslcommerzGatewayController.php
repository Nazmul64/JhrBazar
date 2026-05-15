<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SslcommerzGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SslcommerzGatewayController extends Controller
{
    public function update(Request $request)
    {
        $gateway = SslcommerzGateway::firstOrNew(['id' => 1]);

        $gateway->mode = $request->mode;
        $gateway->store_id = $request->store_id;
        $gateway->store_password = $request->store_password;
        $gateway->title = $request->title;
        $gateway->status = $request->boolean('status');

        if ($request->hasFile('logo')) {
            if ($gateway->logo && Storage::disk('public')->exists($gateway->logo)) {
                Storage::disk('public')->delete($gateway->logo);
            }
            $gateway->logo = $request->file('logo')->store('gateway-logos', 'public');
        }

        $gateway->save();

        return redirect()->back()->with('sslcommerz_success', 'SSLCommerz settings updated successfully!');
    }

    public function toggleStatus()
    {
        $gateway = SslcommerzGateway::firstOrNew(['id' => 1]);
        $gateway->status = !$gateway->status;
        $gateway->save();
        return response()->json(['status' => $gateway->status]);
    }
}
