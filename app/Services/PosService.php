<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\SalesTransaction;
use App\Services\ActivityLogService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class PosService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        protected ActivityLogService $activityLogService
    ) {}

    public function processCheckout(
        int $outletId,
        int $cashierId,
        string $customerName,
        string $paymentMethod,
        float $discountAmount,
        float $paidAmount,
        array $cartItems
    ): SalesTransaction {
        return DB::transaction(function () use (
            $outletId,
            $cashierId,
            $customerName,
            $paymentMethod,
            $discountAmount,
            $paidAmount,
            $cartItems
        ) {
            $subtotal = 0.0;
            $itemsToInsert = [];

            foreach ($cartItems as $item) {
                $productId = (int) $item['product_id'];
                $qty = (int) $item['quantity'];

                $product = Product::findOrFail($productId);

                $inventory = Inventory::where('outlet_id', $outletId)
                    ->where('product_id', $productId)
                    ->lockForUpdate()
                    ->first();

                if (! $inventory || $inventory->stock < $qty) {
                    $available = $inventory ? $inventory->stock : 0;
                    throw new Exception("Stok produk '{$product->name}' tidak cukup! Tersedia: {$available}, diminta: {$qty}");
                }

                $inventory->decrement('stock', $qty);

                $unitPrice = (float) $product->selling_price;
                $costPrice = (float) $product->cost_price;
                $lineTotal = $unitPrice * $qty;
                $subtotal += $lineTotal;

                $itemsToInsert[] = [
                    'product_id' => $productId,
                    'unit_price' => $unitPrice,
                    'cost_price' => $costPrice,
                    'quantity' => $qty,
                    'subtotal' => $lineTotal,
                ];
            }

            $taxAmount = round(($subtotal - $discountAmount) * 0.11, 2);
            if ($taxAmount < 0) {
                $taxAmount = 0.0;
            }
            $grandTotal = ($subtotal - $discountAmount) + $taxAmount;

            if ($paidAmount < $grandTotal && $paymentMethod === 'CASH') {
                throw new Exception("Uang tunai yang dibayarkan kurang dari total belanja!");
            }

            $changeAmount = $paymentMethod === 'CASH' ? ($paidAmount - $grandTotal) : 0.0;

            $prefix = 'INV/' . Carbon::now()->format('Ymd') . '/';
            $todayCount = SalesTransaction::where('invoice_number', 'like', "{$prefix}%")->count() + 1;
            $invoiceNumber = $prefix . str_pad((string) $todayCount, 4, '0', STR_PAD_LEFT);

            $transaction = SalesTransaction::create([
                'invoice_number' => $invoiceNumber,
                'outlet_id' => $outletId,
                'user_id' => $cashierId,
                'customer_name' => $customerName ?: 'Pelanggan Umum',
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'tax_amount' => $taxAmount,
                'grand_total' => $grandTotal,
                'payment_method' => $paymentMethod,
                'payment_status' => 'PAID',
                'paid_amount' => $paidAmount,
                'change_amount' => $changeAmount,
            ]);

            foreach ($itemsToInsert as $line) {
                $transaction->items()->create($line);
            }

            $this->activityLogService->log(
                action: 'CHECKOUT',
                entityType: SalesTransaction::class,
                entityId: $transaction->id,
                payload: [
                    'invoice' => $invoiceNumber,
                    'grand_total' => $grandTotal,
                    'payment_method' => $paymentMethod,
                ]
            );

            return $transaction;
        });
    }
}
