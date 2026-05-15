<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FraudApi;
use Illuminate\Http\Request;

class FraudApiController extends Controller
{
    public function index()
    {
        $freeApi = FraudApi::firstOrCreate(['type' => 'free'], [
            'api_url' => 'https://bdcourier.com/api/courier-check',
            'is_active' => true
        ]);

        $paidApi = FraudApi::firstOrCreate(['type' => 'paid'], [
            'api_url' => 'https://bdcourier.com/api/pro/courier-check',
            'is_active' => false
        ]);

        return view('admin.fraud.apis.index', compact('freeApi', 'paidApi'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'apis.free.api_url' => 'required|url',
            'apis.paid.api_url' => 'required|url',
            'active_type'       => 'required|in:free,paid',
        ]);

        $data = $request->input('apis');
        $activeType = $request->input('active_type');

        foreach ($data as $type => $values) {
            FraudApi::updateOrCreate(
                ['type' => $type],
                [
                    'api_url'   => $values['api_url'],
                    'api_key'   => $values['api_key'] ?? null,
                    'is_active' => ($type === $activeType),
                ]
            );
        }

        return back()->with('success', 'Fraud API settings updated successfully!');
    }
}
