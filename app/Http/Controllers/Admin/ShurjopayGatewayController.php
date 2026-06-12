<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShurjopayGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ShurjopayGatewayController extends Controller
{
    public function update(Request $request)
    {
        $gateway = ShurjopayGateway::firstOrNew(['id' => 1]);

        $gateway->username = $request->username;
        $gateway->prefix = $request->prefix;
        $gateway->success_url = $request->success_url;
        $gateway->return_url = $request->return_url;
        $gateway->base_url = $request->base_url;
        $gateway->password = $request->password;

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

        return redirect()->back()->with('success', 'Settings updated successfully!');
    }

    public function toggleStatus()
    {
        $gateway = ShurjopayGateway::firstOrNew(['id' => 1]);
        $gateway->status = !$gateway->status;
        $gateway->save();
        return response()->json(['status' => $gateway->status]);
    }
}
