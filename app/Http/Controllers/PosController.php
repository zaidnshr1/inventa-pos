<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Category;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\SalesTransaction;
use App\Services\PosService;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PosController extends Controller
{
    public function __construct(
        protected PosService $posService
    ) {}

    public function index(): View
    {
        $user = Auth::user();
        $outletId = $user?->outlet_id ?? Outlet::first()?->id;
        $currentOutlet = Outlet::findOrFail($outletId);

        $categories = Category::all();

        $products = Product::where('is_active', true)
            ->with(['category', 'inventories' => fn($q) => $q->where('outlet_id', $outletId)])
            ->get()
            ->map(function ($p) use ($outletId) {
                $stock = $p->inventories->first()?->stock ?? 0;
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'sku' => $p->sku,
                    'category_id' => $p->category_id,
                    'selling_price' => (float) $p->selling_price,
                    'stock' => $stock,
                ];
            });

        return view('pos.index', compact('currentOutlet', 'categories', 'products'));
    }

    public function checkout(CheckoutRequest $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $outletId = (int) ($user?->outlet_id ?? Outlet::first()?->id);

            $transaction = $this->posService->processCheckout(
                outletId: $outletId,
                cashierId: (int) Auth::id(),
                customerName: (string) ($request->validated('customer_name') ?? 'Pelanggan Umum'),
                paymentMethod: (string) $request->validated('payment_method'),
                discountAmount: (float) ($request->validated('discount_amount') ?? 0),
                paidAmount: (float) $request->validated('paid_amount'),
                cartItems: $request->validated('cart_items')
            );

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil!',
                'transaction_id' => $transaction->id,
                'invoice_number' => $transaction->invoice_number,
                'print_url' => route('pos.receipt', $transaction->id),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function printReceipt(int $id): Response
    {
        $transaction = SalesTransaction::with(['outlet', 'user', 'items.product'])->findOrFail($id);

        $pdf = Pdf::loadView('pos.receipt', compact('transaction'))
            ->setPaper([0, 0, 226.77, 600], 'portrait');

        $safeInvoiceNumber = str_replace('/', '-', $transaction->invoice_number);

        return $pdf->stream("receipt-{$safeInvoiceNumber}.pdf");
    }
}
