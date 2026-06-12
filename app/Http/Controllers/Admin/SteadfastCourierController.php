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

        if ($request->hasFile('logo')) {
            if ($gateway->logo) {
                $oldPath = public_path($gateway->logo);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
                if (Storage::disk('public')->exists($gateway->logo)) {
                    Storage::disk('public')->delete($gateway->logo);
                }
            }
            $file = $request->file('logo');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/gateway-logos'), $filename);
            $gateway->logo = 'uploads/gateway-logos/' . $filename;
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
