<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Withdraw;
use App\Models\GenaralSetting;
use Illuminate\Http\Request;

class WithdrawController extends Controller
{
    public function index()
    {
        $withdraws = Withdraw::with(['seller', 'bank'])->latest()->paginate(15);
        return view('admin.withdraws.index', compact('withdraws'));
    }

    public function updateStatus(Request $request, Withdraw $withdraw)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_note' => 'nullable|string',
        ]);

        $withdraw->update([
            'status' => $request->status,
            'admin_note' => $request->admin_note,
        ]);

        return back()->with('success', 'Withdraw request ' . $request->status . ' successfully!');
    }

    public function settings()
    {
        $settings = GenaralSetting::first();
        return view('admin.withdraws.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'min_withdraw' => 'required|numeric|min:0',
            'max_withdraw' => 'required|numeric|min:0',
        ]);

        $settings = GenaralSetting::first();
        $settings->update([
            'min_withdraw' => $request->min_withdraw,
            'max_withdraw' => $request->max_withdraw,
        ]);

        return back()->with('success', 'Withdraw settings updated successfully!');
    }
}
