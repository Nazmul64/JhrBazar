<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TelesignGateway;
use Illuminate\Http\Request;

class TelesignGatewayController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'customer_id' => 'nullable|string|max:255',
            'api_key'     => 'nullable|string|max:255',
        ]);

        $gateway = TelesignGateway::firstOrNew(['id' => 1]);
        $gateway->customer_id = $request->customer_id;
        $gateway->api_key     = $request->api_key;
        $gateway->save();

        return back()->with('success', 'Telesign settings updated successfully.');
    }

    public function toggleStatus(Request $request)
    {
        \App\Models\TwilioGateway::query()->update(['is_active' => false]);
        TelesignGateway::query()->update(['is_active' => false]);
        \App\Models\NexmoGateway::query()->update(['is_active' => false]);
        \App\Models\MessagebirdGateway::query()->update(['is_active' => false]);

        $gateway = TelesignGateway::firstOrNew(['id' => 1]);
        $gateway->is_active = $request->boolean('is_active');
        $gateway->save();

        return back()->with('success', 'Telesign status updated.');
    }
}
