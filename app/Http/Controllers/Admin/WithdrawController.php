<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommissionSetup;
use App\Models\GenaralSetting;
use App\Models\Withdraw;
use App\Models\SellerTransaction;
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

        // Update withdrawal status first
        $withdraw->update([
            'status' => $request->status,
            'admin_note' => $request->admin_note,
        ]);

        // If approved, deduct seller balance and finalize transaction
        if ($request->status === 'approved') {
            // Deduct balance from seller
            $seller = $withdraw->seller; // Assuming relation 'seller' exists on Withdraw model
            if ($seller) {
                $seller->decrement('balance', $withdraw->amount);
            }

            // Update related pending transaction to completed
            $transaction = \App\Models\SellerTransaction::where('seller_id', $withdraw->seller_id)
                ->where('type', 'withdrawal')
                ->where('status', 'pending')
                ->orderBy('id', 'desc')
                ->first();
            if ($transaction) {
                $transaction->update(['status' => 'completed']);
            }
        } elseif ($request->status === 'rejected') {
            // If rejected, mark related pending transaction as rejected (if exists)
            $transaction = \App\Models\SellerTransaction::where('seller_id', $withdraw->seller_id)
                ->where('type', 'withdrawal')
                ->where('status', 'pending')
                ->orderBy('id', 'desc')
                ->first();
            if ($transaction) {
                $transaction->update(['status' => 'rejected']);
            }
        }

        return back()->with('success', 'Withdraw request ' . $request->status . ' successfully!');
    }

    public function settings()
    {
        $settings = GenaralSetting::first();
        $commission = CommissionSetup::latest()->first();

        return view('admin.withdraws.settings', compact('settings', 'commission'));
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

        $commission = CommissionSetup::latest()->first();
        if ($commission) {
            $commission->update([
                'min_withdraw_amount' => $request->min_withdraw,
                'max_withdraw_amount' => $request->max_withdraw,
            ]);
        }

        return back()->with('success', 'Withdraw settings updated successfully!');
    }
}
