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
        $gateway->order_confirm = $request->boolean('order_confirm');
        $gateway->forgot_password = $request->boolean('forgot_password');
        $gateway->password_generator = $request->boolean('password_generator');

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
