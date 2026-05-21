<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\CommissionSetup;
use App\Models\GenaralSetting;
use App\Models\Withdraw;
use App\Models\SellerTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SellerWithdrawController extends Controller
{
    protected function commissionSettings()
    {
        return CommissionSetup::latest()->first();
    }

    protected function getWithdrawConfig()
    {
        $commission = $this->commissionSettings();
        $settings = GenaralSetting::first();

        return [
            'min' => optional($commission)->min_withdraw_amount ?? $settings->min_withdraw ?? 0,
            'max' => optional($commission)->max_withdraw_amount ?? $settings->max_withdraw ?? 1000000,
            'commission_percent' => optional($commission)->withdraw_commission_percent ?? $settings->withdraw_commission ?? 0,
            'charge' => optional($commission)->withdraw_charge ?? 0,
            'rules' => optional($commission)->seller_withdraw_rules ?? [],
            'daily_limit' => optional($commission)->daily_limit,
            'verification_required' => optional($commission)->verification_required ?? false,
            'settings' => $settings,
        ];
    }

    public function index()
    {
        $seller = Auth::user();
        $balance = $seller->balance;

        $withdraws = Withdraw::where('seller_id', $seller->id)->with('bank')->latest()->paginate(10);
        $banks = Bank::where('status', 'active')->orderBy('name')->get();
        $withdrawConfig = $this->getWithdrawConfig();

        return view('seller.withdraws.index', compact('withdraws', 'banks', 'balance', 'withdrawConfig'));
    }

    public function store(Request $request)
    {
        $seller = Auth::user();
        $balance = $seller->balance;
        $config = $this->getWithdrawConfig();

        $min = $config['min'];
        $max = $config['max'];

        $request->validate([
            'amount' => "required|numeric|min:$min|max:$max",
            'bank_id' => 'required|exists:banks,id',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'seller_note' => 'nullable|string',
        ], [
            'amount.min' => "The minimum withdrawal amount is ৳" . number_format($min, 2),
            'amount.max' => "The maximum withdrawal amount is ৳" . number_format($max, 2),
        ]);

        if ($request->amount > $balance) {
            return back()->with('error', 'Insufficient balance! You cannot withdraw more than ৳' . number_format($balance, 2));
        }

        $commissionPercent = $config['commission_percent'];
        $withdrawCharge = $config['charge'];
        $commissionAmount = ($request->amount * $commissionPercent) / 100;
        $netPayable = $request->amount - $commissionAmount - $withdrawCharge;

        DB::beginTransaction();
        try {
            // Balance deduction deferred until admin approval

            $withdraw = Withdraw::create([
                'seller_id' => $seller->id,
                'bank_id' => $request->bank_id,
                'account_name' => $request->account_name,
                'account_number' => $request->account_number,
                'contact_number' => $request->contact_number,
                'amount' => $request->amount,
                'status' => 'pending',
                'seller_note' => $request->seller_note,
            ]);

            SellerTransaction::create([
                'seller_id' => $seller->id,
                'transaction_id' => 'WITH-' . strtoupper(Str::random(10)),
                'type' => 'withdrawal',
                'amount' => $request->amount,
                'commission' => $commissionAmount + $withdrawCharge,
                'net_amount' => $netPayable,
                'status' => 'pending',
                'description' => 'Withdrawal request to ' . Bank::find($request->bank_id)->name,
            ]);

            DB::commit();
            return redirect()->route('seller.withdraws.index')->with('success', 'Withdraw request submitted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function cancel(Withdraw $withdraw)
    {
        if ($withdraw->seller_id !== Auth::id()) {
            abort(403);
        }

        if ($withdraw->status !== 'pending') {
            return back()->with('error', 'Only pending requests can be cancelled.');
        }

        $withdraw->update(['status' => 'cancelled']);

        return back()->with('success', 'Withdraw request cancelled successfully!');
    }
}
