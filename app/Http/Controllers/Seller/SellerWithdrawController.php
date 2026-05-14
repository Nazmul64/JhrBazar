<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Withdraw;
use App\Models\Bank;
use App\Models\GenaralSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SellerWithdrawController extends Controller
{
    public function index()
    {
        $seller = Auth::user();
        $balance = $seller->balance;
        
        $withdraws = Withdraw::where('seller_id', $seller->id)->with('bank')->latest()->paginate(10);
        $banks = Bank::where('status', 'active')->orderBy('name')->get();
        $settings = GenaralSetting::first();
        
        return view('seller.withdraws.index', compact('withdraws', 'banks', 'balance', 'settings'));
    }

    public function store(Request $request)
    {
        $settings = GenaralSetting::first();
        $seller = Auth::user();
        $balance = $seller->balance;

        $min = $settings->min_withdraw ?? 0;
        $max = $settings->max_withdraw ?? 1000000;

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

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // Calculate Commission (if any)
            $commissionPercent = $settings->withdraw_commission ?? 0;
            $commissionAmount = ($request->amount * $commissionPercent) / 100;
            $netPayable = $request->amount - $commissionAmount;

            // Deduct Balance
            $seller->decrement('balance', $request->amount);

            // Create Withdraw Record
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

            // Create Transaction Log
            \App\Models\SellerTransaction::create([
                'seller_id'      => $seller->id,
                'transaction_id' => 'WITH-' . strtoupper(Str::random(10)),
                'type'           => 'withdrawal',
                'amount'         => $request->amount,
                'commission'     => $commissionAmount,
                'net_amount'     => $netPayable,
                'status'         => 'pending',
                'description'    => 'Withdrawal request to ' . Bank::find($request->bank_id)->name,
            ]);

            \Illuminate\Support\Facades\DB::commit();
            return redirect()->route('seller.withdraws.index')->with('success', 'Withdraw request submitted successfully!');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
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
