<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SellerTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SellerWithdrawalController extends Controller
{
    public function index()
    {
        $seller = auth()->user();
        $transactions = SellerTransaction::where('seller_id', $seller->id)
            ->where('type', 'withdrawal')
            ->orderBy('id', 'desc')
            ->paginate(15);
            
        return view('seller.withdraw.index', compact('seller', 'transactions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:100', // Minimum 100 withdrawal
        ]);

        $seller = User::find(auth()->id());
        $requestedAmount = $request->amount;

        // 1. Balance Verification
        if ($seller->balance < $requestedAmount) {
            return back()->with('error', 'Insufficient balance! You cannot withdraw more than your available balance.');
        }

        DB::beginTransaction();
        try {
            // 2. Commission Calculation (e.g. 5% withdrawal fee or fixed)
            $commissionPercent = 2; // Example 2% withdrawal fee
            $commissionAmount = ($requestedAmount * $commissionPercent) / 100;
            $netPayable = $requestedAmount - $commissionAmount;

            // 3. Balance Deduction
            $seller->decrement('balance', $requestedAmount);

            // 4. Transaction Logging
            SellerTransaction::create([
                'seller_id'      => $seller->id,
                'transaction_id' => 'WITH-' . strtoupper(str_random(10)),
                'type'           => 'withdrawal',
                'amount'         => $requestedAmount,
                'commission'     => $commissionAmount,
                'net_amount'     => $netPayable,
                'status'         => 'pending',
                'description'    => 'Withdrawal request of ৳' . number_format($requestedAmount, 2),
            ]);

            DB::commit();
            return redirect()->route('seller.withdraw.index')->with('success', 'Withdrawal request submitted successfully! Pending admin approval.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
}
