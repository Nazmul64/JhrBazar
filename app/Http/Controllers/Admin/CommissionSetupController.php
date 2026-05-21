<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommissionSetup;
use App\Models\GenaralSetting;
use Illuminate\Http\Request;

class CommissionSetupController extends Controller
{
    public function index()
    {
        $commission = CommissionSetup::latest()->first();
        $settings = GenaralSetting::first();

        return view('admin.withdraws.commission_settings', compact('commission', 'settings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'withdraw_commission_percent' => 'nullable|numeric|min:0|max:100',
            'min_withdraw_amount' => 'nullable|numeric|min:0',
            'max_withdraw_amount' => 'nullable|numeric|min:0',
            'withdraw_charge' => 'nullable|numeric|min:0',
            'seller_withdraw_rules' => 'nullable|string',
            'daily_limit' => 'nullable|numeric|min:0',
            'verification_required' => 'nullable|boolean',
        ]);

        $commissionRules = [];
        if ($request->filled('seller_withdraw_rules')) {
            $commissionRules = collect(preg_split('/\r?\n/', $request->seller_withdraw_rules))
                ->map(fn($line) => trim($line))
                ->filter()
                ->values()
                ->all();
        }

        $commissionData = CommissionSetup::create([
            'withdraw_commission_percent' => $request->withdraw_commission_percent ?? 0,
            'min_withdraw_amount' => $request->min_withdraw_amount ?? 0,
            'max_withdraw_amount' => $request->max_withdraw_amount ?? 0,
            'withdraw_charge' => $request->withdraw_charge ?? 0,
            'seller_withdraw_rules' => $commissionRules,
            'daily_limit' => $request->daily_limit,
            'verification_required' => $request->boolean('verification_required'),
        ]);

        $settings = GenaralSetting::first();
        if ($settings) {
            $settings->update([
                'min_withdraw' => $commissionData->min_withdraw_amount,
                'max_withdraw' => $commissionData->max_withdraw_amount,
                'withdraw_commission' => $commissionData->withdraw_commission_percent,
            ]);
        }

        return back()->with('success', 'Commission setup saved successfully.');
    }
}
