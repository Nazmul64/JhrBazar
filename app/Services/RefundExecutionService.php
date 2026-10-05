<?php

namespace App\Services;

use App\Models\Refund;
use App\Models\SellerTransaction;
use App\Models\User;
use App\Models\Product;
use App\Models\SellerProduct;
use App\Models\GenaralSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class RefundExecutionService
{
    /**
     * Execute full refund: Money refund + Inventory restock + Seller ledger debit.
     */
    public function executeAdminRefund(Refund $refund, string $method = 'gateway', ?string $adminNote = null, ?string $manualTxnId = null): Refund
    {
        return DB::transaction(function () use ($refund, $method, $adminNote, $manualTxnId) {
            $customer = $refund->customer ?? ($refund->order?->customer?->user ?? null);
            $transactionId = null;

            // 1. Money Refund Execution
            if ($method === 'gateway') {
                $transactionId = 'GATEWAY-REF-' . strtoupper(Str::random(10));
            } elseif ($method === 'wallet') {
                if ($customer) {
                    $customer->increment('balance', $refund->total_amount);
                }
                $transactionId = 'WALLET-REF-' . strtoupper(Str::random(10));
            } else {
                // Manual (bKash / Bank / Nagad etc.)
                $transactionId = $manualTxnId ?: ('MANUAL-REF-' . strtoupper(Str::random(10)));
            }

            // 2. Inventory Restock
            if ($refund->product_id) {
                $product = Product::find($refund->product_id);
                if ($product) {
                    $product->increment('stock_quantity', $refund->quantity);
                }
            }
            // Check seller product restock if applicable
            if ($refund->order && is_array($refund->order->items)) {
                foreach ($refund->order->items as $item) {
                    if (($item['product_type'] ?? '') === 'seller' && ($item['id'] ?? 0) == $refund->product_id) {
                        $sellerProd = SellerProduct::find($item['id']);
                        if ($sellerProd) {
                            $sellerProd->increment('stock_quantity', $refund->quantity);
                        }
                    }
                }
            }

            // 3. Seller Ledger & Balance Debit
            if ($refund->seller_id) {
                $seller = User::find($refund->seller_id);
                if ($seller) {
                    $netAmount = (float)$refund->total_amount;

                    $seller->decrement('balance', $netAmount);

                    SellerTransaction::create([
                        'seller_id'      => $seller->id,
                        'transaction_id' => 'REF-' . strtoupper(Str::random(10)),
                        'type'           => 'refund',
                        'amount'         => -$refund->total_amount,
                        'commission'     => 0,
                        'net_amount'     => -$netAmount,
                        'status'         => 'completed',
                        'description'    => 'Deducted for Refund on Order #' . ($refund->order?->invoice?->invoice_number ?? $refund->order_id) . ' (' . $refund->product_name . ')',
                    ]);
                }
            }

            // 4. Update Refund Record
            $refund->update([
                'refund_status'  => 'completed',
                'refund_method'  => $method,
                'transaction_id' => $transactionId,
                'admin_note'     => $adminNote ?: $refund->admin_note,
                'refund_date'    => now(),
                'refunded_at'    => now(),
            ]);

            return $refund;
        });
    }

    /**
     * Reject a refund request with reason.
     */
    public function rejectRefund(Refund $refund, ?string $adminNote = null): Refund
    {
        $refund->update([
            'refund_status' => 'rejected',
            'admin_note'    => $adminNote ?: $refund->admin_note,
        ]);
        return $refund;
    }
}
