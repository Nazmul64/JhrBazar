<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SteadfastCourier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SteadfastCourierController extends Controller
{
    public function update(Request $request)
    {
        $gateway = SteadfastCourier::firstOrNew(['id' => 1]);

        $gateway->api_key = $request->api_key;
        $gateway->secret_key = $request->secret_key;
        $gateway->url = $request->url;
        $gateway->status = $request->boolean('status');

        if ($request->hasFile('logo')) {
            if ($gateway->logo && Storage::disk('public')->exists($gateway->logo)) {
                Storage::disk('public')->delete($gateway->logo);
            }
            $gateway->logo = $request->file('logo')->store('gateway-logos', 'public');
        }

        $gateway->save();

        return redirect()->back()->with('steadfast_success', 'Settings updated successfully!');
    }

    public function toggleStatus()
    {
        $gateway = SteadfastCourier::firstOrNew(['id' => 1]);
        $gateway->status = !$gateway->status;
        $gateway->save();
        return response()->json(['status' => $gateway->status]);
    }
}
