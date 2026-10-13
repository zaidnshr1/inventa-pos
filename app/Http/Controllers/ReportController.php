<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    public function index(Request $request): View
    {
        $outletId = $request->query('outlet_id') ? (int) $request->query('outlet_id') : null;
        $startDate = $request->query('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::now()->toDateString());

        $outlets = Outlet::all();
        $summary = $this->reportService->getFinancialSummary($outletId, (string)$startDate, (string)$endDate);
        
        $transactions = $this->reportService->buildFilteredQuery($outletId, (string)$startDate, (string)$endDate)
            ->with(['outlet', 'user'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('reports.index', compact('outlets', 'summary', 'transactions', 'outletId', 'startDate', 'endDate'));
    }

    public function export(Request $request): StreamedResponse
    {
        $outletId = $request->query('outlet_id') ? (int) $request->query('outlet_id') : null;
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        return $this->reportService->exportSalesCsv(
            $outletId,
            is_string($startDate) ? $startDate : null,
            is_string($endDate) ? $endDate : null
        );
    }
}
