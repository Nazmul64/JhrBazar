<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NexmoGateway;
use Illuminate\Http\Request;

class NexmoGatewayController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'nexmo_key'    => 'nullable|string|max:255',
            'nexmo_secret' => 'nullable|string|max:255',
            'nexmo_from'   => 'nullable|string|max:50',
        ]);

        $gateway = NexmoGateway::firstOrNew(['id' => 1]);
        $gateway->nexmo_key    = $request->nexmo_key;
        $gateway->nexmo_secret = $request->nexmo_secret;
        $gateway->nexmo_from   = $request->nexmo_from;
        $gateway->save();

        return back()->with('success', 'Nexmo settings updated successfully.');
    }

    public function toggleStatus(Request $request)
    {
        \App\Models\TwilioGateway::query()->update(['is_active' => false]);
        \App\Models\TelesignGateway::query()->update(['is_active' => false]);
        NexmoGateway::query()->update(['is_active' => false]);
        \App\Models\MessagebirdGateway::query()->update(['is_active' => false]);

        $gateway = NexmoGateway::firstOrNew(['id' => 1]);
        $gateway->is_active = $request->boolean('is_active');
        $gateway->save();

        return back()->with('success', 'Nexmo status updated.');
    }
}
