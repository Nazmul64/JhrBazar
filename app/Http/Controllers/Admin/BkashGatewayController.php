<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BkashGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BkashGatewayController extends Controller
{
    public function update(Request $request)
    {
        $gateway = BkashGateway::firstOrNew(['id' => 1]);

        $gateway->mode = $request->mode;
        $gateway->app_key = $request->app_key;
        $gateway->password = $request->password;
        $gateway->username = $request->username;
        $gateway->app_secret_key = $request->app_secret_key;
        $gateway->title = $request->title;
        $gateway->status = $request->boolean('status');

        if ($request->hasFile('logo')) {
            if ($gateway->logo && Storage::disk('public')->exists($gateway->logo)) {
                Storage::disk('public')->delete($gateway->logo);
            }
            $gateway->logo = $request->file('logo')->store('gateway-logos', 'public');
        }

        $gateway->save();

        return redirect()->back()->with('bkash_success', 'Settings updated successfully!');
    }

    public function toggleStatus()
    {
        $gateway = BkashGateway::firstOrNew(['id' => 1]);
        $gateway->status = !$gateway->status;
        $gateway->save();
        return response()->json(['status' => $gateway->status]);
    }
}
