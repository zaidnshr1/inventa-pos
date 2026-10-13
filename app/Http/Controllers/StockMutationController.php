<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreStockMutationRequest;
use App\Models\Outlet;
use App\Models\Product;
use App\Services\StockMutationService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StockMutationController extends Controller
{
    public function __construct(
        protected StockMutationService $mutationService
    ) {}

    public function index(): View
    {
        $mutations = $this->mutationService->getPaginatedMutations(10);
        return view('mutations.index', compact('mutations'));
    }

    public function create(): View
    {
        $outlets = Outlet::where('is_active', true)->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('mutations.create', compact('outlets', 'products'));
    }

    public function store(StoreStockMutationRequest $request): RedirectResponse
    {
        try {
            $mutation = $this->mutationService->createRequest(
                sourceOutletId: (int) $request->validated('source_outlet_id'),
                targetOutletId: (int) $request->validated('target_outlet_id'),
                requesterId: (int) Auth::id(),
                note: $request->validated('note'),
                items: $request->validated('items')
            );

            return redirect()->route('mutations.show', $mutation->id)
                ->with('success', "Permintaan transfer {$mutation->mutation_number} berhasil dibuat!");
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(int $id): View
    {
        $mutation = $this->mutationService->getMutationById($id);
        return view('mutations.show', compact('mutation'));
    }

    public function approve(int $id): RedirectResponse
    {
        try {
            $this->mutationService->approve($id, (int) Auth::id());
            return back()->with('success', 'Mutasi stok berhasil disetujui! Stok asal telah dikurangi.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function receive(int $id): RedirectResponse
    {
        try {
            $this->mutationService->receive($id);
            return back()->with('success', 'Barang berhasil diterima! Stok tujuan telah ditambahkan.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(int $id): RedirectResponse
    {
        try {
            $this->mutationService->reject($id, (int) Auth::id());
            return back()->with('success', 'Mutasi stok telah ditolak.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
