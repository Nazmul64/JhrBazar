<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Refund;
use App\Services\RefundExecutionService;
use Illuminate\Http\Request;

class RefundController extends Controller
{
    // 1. Admin Refund Requests List
    public function index(Request $request)
    {
        $refunds = Refund::with([
            'order.invoice',
            'product:id,name',
            'customer:id,name,email',
            'seller:id,name,email'
        ])
        ->when($request->status, fn($q) => $q->where('refund_status', $request->status))
        ->latest()
        ->paginate(15);

        return response()->json($refunds);
    }

    // 2. Admin Final Approve / Reject Execution
    public function handleRefund(Request $request, $id, RefundExecutionService $service)
    {
        $validated = $request->validate([
            'action'         => 'required|in:approved,rejected',
            'refund_method'  => 'required_if:action,approved|in:gateway,wallet,manual',
            'admin_note'     => 'nullable|string',
            'transaction_id' => 'nullable|string',
        ]);

        $refund = Refund::findOrFail($id);

        if ($refund->refund_status === 'completed') {
            return response()->json(['message' => 'This refund request has already been completed.'], 422);
        }

        if ($validated['action'] === 'approved') {
            $service->executeAdminRefund($refund, $validated['refund_method'], $validated['admin_note'] ?? null, $validated['transaction_id'] ?? null);
            return response()->json([
                'message' => 'Refund completed successfully. Stock restocked & seller balance debited.',
                'refund'  => $refund
            ]);
        } else {
            $service->rejectRefund($refund, $validated['admin_note'] ?? null);
            return response()->json([
                'message' => 'Refund request rejected.',
                'refund'  => $refund
            ]);
        }
    }
}
