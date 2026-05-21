<?php

namespace App\Http\Controllers\Seller;

use App\Models\Refund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RefundController extends \App\Http\Controllers\Controller
{
    /**
     * Display seller's refunds
     */
    public function index(Request $request)
    {
        $query = Refund::where('seller_id', Auth::id())
            ->with(['order', 'product', 'courier'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->status) {
            $query->where('refund_status', $request->status);
        }

        $refunds = $query->paginate(20);
        $statuses = ['pending', 'approved', 'processing', 'completed', 'rejected'];

        return view('seller.refunds.index', compact('refunds', 'statuses'));
    }

    /**
     * Show refund details
     */
    public function show(Refund $refund)
    {
        // Check if refund belongs to current seller
        if ($refund->seller_id !== Auth::id()) {
            abort(403);
        }

        $refund->load(['order', 'product', 'courier', 'items']);
        return view('seller.refunds.show', compact('refund'));
    }

    /**
     * Add seller note to refund
     */
    public function addNote(Request $request, Refund $refund)
    {
        if ($refund->seller_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'note' => 'required_without:seller_note|string|max:1000',
            'seller_note' => 'required_without:note|string|max:1000',
        ]);

        $note = $request->input('note') ?? $request->input('seller_note');

        $refund->update([
            'seller_note' => $note
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Note added successfully!'
            ]);
        }

        return redirect()->back()->with('success', 'Note added successfully!');
    }
}
