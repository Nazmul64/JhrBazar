<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use App\Models\Payment; // Assuming a Payment model exists
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Process a payment request.
     * This is a stub implementation – integrate with actual gateway as needed.
     */
    public function pay(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'gateway'  => 'required|string',
            'amount'   => 'required|numeric|min:0',
        ]);

        // Create a payment record (you may replace with gateway SDK call)
        $payment = Payment::create([
            'order_id' => $validated['order_id'],
            'gateway'  => $validated['gateway'],
            'amount'   => $validated['amount'],
            'status'   => 'pending',
        ]);

        // Return a simple JSON response – in real implementation, redirect to gateway
        return response()->json([
            'success' => true,
            'message' => 'Payment initiated',
            'data'    => new PaymentResource($payment),
        ]);
    }
}
