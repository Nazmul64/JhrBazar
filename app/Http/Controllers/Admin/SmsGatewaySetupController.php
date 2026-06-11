<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SmsGateway;
use Illuminate\Http\Request;

class SmsGatewaySetupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $gateways = SmsGateway::all();
        return view('admin.smsgatewaysetup.index', compact('gateways'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.smsgatewaysetup.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'url'       => 'required|string',
            'api_key'   => 'required|string',
            'sender_id' => 'required|string',
        ]);

        $gateway = new SmsGateway();
        $gateway->url = $request->url;
        $gateway->api_key = $request->api_key;
        $gateway->sender_id = $request->sender_id;
        $gateway->status = $request->boolean('status');
        $gateway->order_confirm = $request->boolean('order_confirm');
        $gateway->forgot_password = $request->boolean('forgot_password');
        $gateway->password_generator = $request->boolean('password_generator');
        $gateway->save();

        // If this gateway is set as active, deactivate all others
        if ($gateway->status) {
            SmsGateway::where('id', '!=', $gateway->id)->update(['status' => false]);
        }

        return redirect()->route('admin.smsgatewaysetup.index')
            ->with('success', 'SMS Gateway configured successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $gateway = SmsGateway::findOrFail($id);
        return view('admin.smsgatewaysetup.edit', compact('gateway'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'url'       => 'required|string',
            'api_key'   => 'required|string',
            'sender_id' => 'required|string',
        ]);

        $gateway = SmsGateway::findOrFail($id);
        $gateway->url = $request->url;
        $gateway->api_key = $request->api_key;
        $gateway->sender_id = $request->sender_id;
        $gateway->status = $request->boolean('status');
        $gateway->order_confirm = $request->boolean('order_confirm');
        $gateway->forgot_password = $request->boolean('forgot_password');
        $gateway->password_generator = $request->boolean('password_generator');
        $gateway->save();

        // If this gateway is set as active, deactivate all others
        if ($gateway->status) {
            SmsGateway::where('id', '!=', $gateway->id)->update(['status' => false]);
        }

        return redirect()->route('admin.smsgatewaysetup.index')
            ->with('success', 'SMS Gateway updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $gateway = SmsGateway::findOrFail($id);
        $gateway->delete();

        return redirect()->route('admin.smsgatewaysetup.index')
            ->with('success', 'SMS Gateway deleted successfully.');
    }

    /**
     * Toggle status via AJAX.
     */
    public function toggleStatus(Request $request, $id)
    {
        $gateway = SmsGateway::findOrFail($id);
        $gateway->status = !$gateway->status;
        $gateway->save();

        if ($gateway->status) {
            SmsGateway::where('id', '!=', $gateway->id)->update(['status' => false]);
        }

        return response()->json([
            'success' => true,
            'status'  => $gateway->status,
            'message' => 'SMS Gateway status updated successfully.'
        ]);
    }
}
