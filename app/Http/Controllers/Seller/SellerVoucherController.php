<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\SellerVoucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerVoucherController extends Controller
{
    public function index()
    {
        $vouchers = SellerVoucher::where('seller_id', Auth::id())->latest()->get();
        return view('seller.promocode.index', compact('vouchers'));
    }

    public function create()
    {
        return view('seller.promocode.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'voucher_code'            => 'required|string|unique:seller_vouchers,voucher_code',
            'discount_type'           => 'required|in:amount,percentage',
            'discount'                => 'required|numeric|min:0',
            'minimum_order_amount'    => 'nullable|numeric|min:0',
            'limit_for_single_user'   => 'nullable|integer|min:1',
            'maximum_discount_amount' => 'nullable|numeric|min:0',
            'start_date'              => 'required|date',
            'start_time'              => 'required',
            'expired_date'            => 'required|date|after_or_equal:start_date',
            'expired_time'            => 'required',
        ]);

        SellerVoucher::create([
            'seller_id'               => Auth::id(),
            'voucher_code'            => $request->voucher_code,
            'discount_type'           => $request->discount_type,
            'discount'                => $request->discount,
            'minimum_order_amount'    => $request->minimum_order_amount ?? 0,
            'limit_for_single_user'   => $request->limit_for_single_user,
            'maximum_discount_amount' => $request->maximum_discount_amount,
            'start_date'              => $request->start_date,
            'start_time'              => $request->start_time,
            'expired_date'            => $request->expired_date,
            'expired_time'            => $request->expired_time,
            'status'                  => true,
        ]);

        return redirect()->route('seller.promocode.index')->with('success', 'Promo Code Created Successfully');
    }

    public function edit($id)
    {
        $voucher = SellerVoucher::where('seller_id', Auth::id())->findOrFail($id);
        return view('seller.promocode.edit', compact('voucher'));
    }

    public function update(Request $request, $id)
    {
        $voucher = SellerVoucher::where('seller_id', Auth::id())->findOrFail($id);

        $request->validate([
            'voucher_code'            => 'required|string|unique:seller_vouchers,voucher_code,' . $id,
            'discount_type'           => 'required|in:amount,percentage',
            'discount'                => 'required|numeric|min:0',
            'minimum_order_amount'    => 'nullable|numeric|min:0',
            'limit_for_single_user'   => 'nullable|integer|min:1',
            'maximum_discount_amount' => 'nullable|numeric|min:0',
            'start_date'              => 'required|date',
            'start_time'              => 'required',
            'expired_date'            => 'required|date|after_or_equal:start_date',
            'expired_time'            => 'required',
        ]);

        $voucher->update([
            'voucher_code'            => $request->voucher_code,
            'discount_type'           => $request->discount_type,
            'discount'                => $request->discount,
            'minimum_order_amount'    => $request->minimum_order_amount ?? 0,
            'limit_for_single_user'   => $request->limit_for_single_user,
            'maximum_discount_amount' => $request->maximum_discount_amount,
            'start_date'              => $request->start_date,
            'start_time'              => $request->start_time,
            'expired_date'            => $request->expired_date,
            'expired_time'            => $request->expired_time,
        ]);

        return redirect()->route('seller.promocode.index')->with('success', 'Promo Code Updated Successfully');
    }

    public function destroy($id)
    {
        $voucher = SellerVoucher::where('seller_id', Auth::id())->findOrFail($id);
        $voucher->delete();

        return redirect()->back()->with('success', 'Promo Code Deleted Successfully');
    }

    public function toggleStatus($id)
    {
        $voucher = SellerVoucher::where('seller_id', Auth::id())->findOrFail($id);
        $voucher->update(['status' => !$voucher->status]);

        return redirect()->back()->with('success', 'Promo Code Status Updated');
    }
}
