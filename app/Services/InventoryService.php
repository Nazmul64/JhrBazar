<?php

namespace App\Services;

use App\Models\Product;
use App\Models\InventoryLedger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryService
{
    /**
     * Record initial stock entry when creating a new product.
     */
    public static function recordInitialStock(Product $product, int $quantity, ?int $adminId = null): ?InventoryLedger
    {
        if ($product->is_unlimited || $quantity <= 0) {
            return null;
        }

        return InventoryLedger::create([
            'product_id'     => $product->id,
            'order_id'       => null,
            'type'           => 'stock_in',
            'quantity'       => $quantity,
            'previous_stock' => 0,
            'current_stock'  => $quantity,
            'note'           => 'Initial product stock creation',
            'created_by'     => $adminId ?? auth()->id(),
        ]);
    }

    /**
     * Add stock to an existing product (Stock In).
     */
    public static function stockIn(Product|int $product, int $quantity, ?string $note = null, ?int $adminId = null): InventoryLedger
    {
        return DB::transaction(function () use ($product, $quantity, $note, $adminId) {
            $productId = $product instanceof Product ? $product->id : $product;
            $prod = Product::where('id', $productId)->lockForUpdate()->firstOrFail();

            $prevStock = (int) $prod->stock_quantity;
            $newStock  = $prevStock + $quantity;

            $prod->update([
                'stock_quantity' => $newStock,
                'total_in'       => (int) $prod->total_in + $quantity,
            ]);

            return InventoryLedger::create([
                'product_id'     => $prod->id,
                'order_id'       => null,
                'type'           => 'stock_in',
                'quantity'       => $quantity,
                'previous_stock' => $prevStock,
                'current_stock'  => $newStock,
                'note'           => $note ?? 'Manual stock in / purchase added',
                'created_by'     => $adminId ?? auth()->id(),
            ]);
        });
    }

    /**
     * Set a new stock quantity directly (Manual Adjustment).
     */
    public static function manualAdjustment(Product|int $product, int $newStock, ?string $note = null, ?int $adminId = null): InventoryLedger
    {
        return DB::transaction(function () use ($product, $newStock, $note, $adminId) {
            $productId = $product instanceof Product ? $product->id : $product;
            $prod = Product::where('id', $productId)->lockForUpdate()->firstOrFail();

            $prevStock = (int) $prod->stock_quantity;
            $diff      = $newStock - $prevStock;

            $totalIn = (int) $prod->total_in;
            if ($diff > 0) {
                $totalIn += $diff;
            }

            $prod->update([
                'stock_quantity' => max(0, $newStock),
                'total_in'       => $totalIn,
            ]);

            return InventoryLedger::create([
                'product_id'     => $prod->id,
                'order_id'       => null,
                'type'           => 'manual_adjustment',
                'quantity'       => $diff,
                'previous_stock' => $prevStock,
                'current_stock'  => max(0, $newStock),
                'note'           => $note ?? 'Manual stock adjustment',
                'created_by'     => $adminId ?? auth()->id(),
            ]);
        });
    }

    /**
     * Deduct stock when an order is delivered or completed.
     */
    public static function deductForOrder(int $orderId, array $items, ?int $adminId = null): bool
    {
        return DB::transaction(function () use ($orderId, $items, $adminId) {
            // Avoid duplicate deduction for this order
            $alreadyDeducted = InventoryLedger::where('order_id', $orderId)
                ->where('type', 'sale_deduction')
                ->exists();

            if ($alreadyDeducted) {
                return true;
            }

            foreach ($items as $item) {
                $productType = strtolower($item['product_type'] ?? 'admin');
                $productId   = $item['id'] ?? ($item['product_id'] ?? null);
                $qty         = (int) ($item['qty'] ?? ($item['quantity'] ?? 1));

                if (!$productId || $qty <= 0 || $productType === 'seller' || $productType === 'digital') {
                    continue;
                }

                $prod = Product::where('id', $productId)->lockForUpdate()->first();
                if (!$prod || $prod->is_unlimited) {
                    continue;
                }

                $prevStock = (int) $prod->stock_quantity;
                $newStock  = max(0, $prevStock - $qty);

                $prod->update([
                    'stock_quantity' => $newStock,
                    'total_sold'     => (int) $prod->total_sold + $qty,
                ]);

                InventoryLedger::create([
                    'product_id'     => $prod->id,
                    'order_id'       => $orderId,
                    'type'           => 'sale_deduction',
                    'quantity'       => -$qty,
                    'previous_stock' => $prevStock,
                    'current_stock'  => $newStock,
                    'note'           => "Deducted for Order #{$orderId}",
                    'created_by'     => $adminId ?? auth()->id(),
                ]);
            }

            return true;
        });
    }

    /**
     * Restore stock when a previously delivered order is cancelled or returned.
     */
    public static function restoreForOrder(int $orderId, array $items, string $type = 'order_cancelled', ?string $note = null, ?int $adminId = null): bool
    {
        return DB::transaction(function () use ($orderId, $items, $type, $note, $adminId) {
            // Check if order was deducted
            $wasDeducted = InventoryLedger::where('order_id', $orderId)
                ->where('type', 'sale_deduction')
                ->exists();

            if (!$wasDeducted) {
                return false;
            }

            // Check if already restored
            $alreadyRestored = InventoryLedger::where('order_id', $orderId)
                ->whereIn('type', ['order_cancelled', 'order_returned'])
                ->exists();

            if ($alreadyRestored) {
                return false;
            }

            foreach ($items as $item) {
                $productType = strtolower($item['product_type'] ?? 'admin');
                $productId   = $item['id'] ?? ($item['product_id'] ?? null);
                $qty         = (int) ($item['qty'] ?? ($item['quantity'] ?? 1));

                if (!$productId || $qty <= 0 || $productType === 'seller' || $productType === 'digital') {
                    continue;
                }

                $prod = Product::where('id', $productId)->lockForUpdate()->first();
                if (!$prod || $prod->is_unlimited) {
                    continue;
                }

                $prevStock = (int) $prod->stock_quantity;
                $newStock  = $prevStock + $qty;
                $newSold   = max(0, (int) $prod->total_sold - $qty);

                $prod->update([
                    'stock_quantity' => $newStock,
                    'total_sold'     => $newSold,
                ]);

                $defaultNote = $type === 'order_returned'
                    ? "Restocked from Returned Order #{$orderId}"
                    : "Restocked from Cancelled Order #{$orderId}";

                InventoryLedger::create([
                    'product_id'     => $prod->id,
                    'order_id'       => $orderId,
                    'type'           => $type,
                    'quantity'       => $qty,
                    'previous_stock' => $prevStock,
                    'current_stock'  => $newStock,
                    'note'           => $note ?? $defaultNote,
                    'created_by'     => $adminId ?? auth()->id(),
                ]);
            }

            return true;
        });
    }
}
