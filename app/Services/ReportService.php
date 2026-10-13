<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\SalesTransaction;
use App\Models\SalesTransactionItem;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportService
{
    /**
     * Create a new class instance.
     */
    public function getFinancialSummary(?int $outletId, ?string $startDate, ?string $endDate): array
    {
        $query = $this->buildFilteredQuery($outletId, $startDate, $endDate);

        $totalTransactions = (clone $query)->count();
        $totalGrossRevenue = (float) (clone $query)->sum('grand_total');
        $totalTax = (float) (clone $query)->sum('tax_amount');
        $totalDiscounts = (float) (clone $query)->sum('discount_amount');

        $transactionIds = (clone $query)->pluck('id');
        $totalCost = (float) SalesTransactionItem::whereIn('sales_transaction_id', $transactionIds)
            ->selectRaw('SUM(cost_price * quantity) as total_cogs')
            ->value('total_cogs');

        $netSales = $totalGrossRevenue - $totalTax;
        $grossProfit = $netSales - $totalCost;

        return [
            'total_transactions' => $totalTransactions,
            'total_gross_revenue' => $totalGrossRevenue,
            'total_tax' => $totalTax,
            'total_discounts' => $totalDiscounts,
            'total_cost' => $totalCost,
            'gross_profit' => $grossProfit,
        ];
    }

    public function exportSalesCsv(?int $outletId, ?string $startDate, ?string $endDate): StreamedResponse
    {
        $query = $this->buildFilteredQuery($outletId, $startDate, $endDate)
            ->with(['outlet', 'user', 'items.product'])
            ->latest();

        $filename = 'sales_report_' . Carbon::now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($query) {
            $handle = fopen('php://output', 'w');

            fputs($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'No. Invoice',
                'Tanggal',
                'Cabang',
                'Kasir',
                'Metode Pembayaran',
                'Subtotal (Rp)',
                'Diskon (Rp)',
                'PPN (Rp)',
                'Total (Rp)',
            ]);

            $query->chunk(200, function ($transactions) use ($handle) {
                foreach ($transactions as $t) {
                    fputcsv($handle, [
                        $t->invoice_number,
                        $t->created_at->format('Y-m-d H:i'),
                        $t->outlet->name,
                        $t->user->name,
                        $t->payment_method,
                        $t->subtotal,
                        $t->discount_amount,
                        $t->tax_amount,
                        $t->grand_total,
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }

    public function buildFilteredQuery(?int $outletId, ?string $startDate, ?string $endDate): Builder
    {
        return SalesTransaction::query()
            ->when($outletId, fn($q) => $q->where('outlet_id', $outletId))
            ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate));
    }
}
