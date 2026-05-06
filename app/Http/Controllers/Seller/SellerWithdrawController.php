<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Withdraw;
use App\Models\Bank;
use App\Models\GenaralSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerWithdrawController extends Controller
{
    public function index()
    {
        // Hardcoded balance as requested until order system integration
        $balance = 10000;
        
        $withdraws = Withdraw::where('seller_id', Auth::id())->with('bank')->latest()->paginate(10);
        $banks = Bank::where('status', 'active')->orderBy('name')->get();
        $settings = GenaralSetting::first();
        
        return view('seller.withdraws.index', compact('withdraws', 'banks', 'balance', 'settings'));
    }

    public function store(Request $request)
    {
        $settings = GenaralSetting::first();
        $balance = 10000; // Hardcoded

        $request->validate([
            'amount' => 'required|numeric|min:' . ($settings->min_withdraw ?? 0) . '|max:' . ($settings->max_withdraw ?? 1000000),
            'bank_id' => 'required|exists:banks,id',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'seller_note' => 'nullable|string',
        ]);

        if ($request->amount > $balance) {
            return back()->with('error', 'Insufficient balance!');
        }

        Withdraw::create([
            'seller_id' => Auth::id(),
            'bank_id' => $request->bank_id,
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
            'contact_number' => $request->contact_number,
            'amount' => $request->amount,
            'status' => 'pending',
            'seller_note' => $request->seller_note,
        ]);

        return redirect()->route('seller.withdraws.index')->with('success', 'Withdraw request submitted successfully!');
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
