<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PathaoCourier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PathaoCourierController extends Controller
{
    public function update(Request $request)
    {
        $gateway = PathaoCourier::firstOrNew(['id' => 1]);

        $gateway->base_url = $request->base_url;
        $gateway->client_id = $request->client_id;
        $gateway->client_secret = $request->client_secret;
        $gateway->username = $request->username;
        $gateway->password = $request->password;
        $gateway->grant_type = $request->grant_type;

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

        return redirect()->back()->with('pathao_success', 'Settings updated successfully!');
    }

    public function toggleStatus()
    {
        $gateway = PathaoCourier::firstOrNew(['id' => 1]);
        $gateway->status = !$gateway->status;
        $gateway->save();
        return response()->json(['status' => $gateway->status]);
    }
}
