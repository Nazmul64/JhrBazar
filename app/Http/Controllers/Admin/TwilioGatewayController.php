<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TwilioGateway;
use Illuminate\Http\Request;

class TwilioGatewayController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'twilio_sid'   => 'nullable|string|max:255',
            'twilio_token' => 'nullable|string|max:255',
            'twilio_from'  => 'nullable|string|max:50',
        ]);

        $gateway = TwilioGateway::firstOrNew(['id' => 1]);
        $gateway->twilio_sid   = $request->twilio_sid;
        $gateway->twilio_token = $request->twilio_token;
        $gateway->twilio_from  = $request->twilio_from;
        $gateway->save();

        return back()->with('success', 'Twilio settings updated successfully.');
    }

    public function toggleStatus(Request $request)
    {
        // Only one SMS provider can be active at a time
        TwilioGateway::query()->update(['is_active' => false]);
        \App\Models\TelesignGateway::query()->update(['is_active' => false]);
        \App\Models\NexmoGateway::query()->update(['is_active' => false]);
        \App\Models\MessagebirdGateway::query()->update(['is_active' => false]);

        $gateway = TwilioGateway::firstOrNew(['id' => 1]);
        $gateway->is_active = $request->boolean('is_active');
        $gateway->save();

        return back()->with('success', 'Twilio status updated.');
    }
}
