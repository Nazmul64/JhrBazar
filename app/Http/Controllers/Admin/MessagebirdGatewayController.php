<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MessagebirdGateway;
use Illuminate\Http\Request;

class MessagebirdGatewayController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'api_key' => 'nullable|string|max:255',
            'from'    => 'nullable|string|max:50',
        ]);

        $gateway = MessagebirdGateway::firstOrNew(['id' => 1]);
        $gateway->api_key = $request->api_key;
        $gateway->from    = $request->from;
        $gateway->save();

        return back()->with('success', 'MessageBird settings updated successfully.');
    }

    public function toggleStatus(Request $request)
    {
        \App\Models\TwilioGateway::query()->update(['is_active' => false]);
        \App\Models\TelesignGateway::query()->update(['is_active' => false]);
        \App\Models\NexmoGateway::query()->update(['is_active' => false]);
        MessagebirdGateway::query()->update(['is_active' => false]);

        $gateway = MessagebirdGateway::firstOrNew(['id' => 1]);
        $gateway->is_active = $request->boolean('is_active');
        $gateway->save();

        return back()->with('success', 'MessageBird status updated.');
    }
}
