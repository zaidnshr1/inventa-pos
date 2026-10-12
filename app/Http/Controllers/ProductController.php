<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService
    ) {}

    public function index(Request $request): View
    {
        $search = $request->query('search');
        $products = $this->productService->getPaginatedProducts(
                10,
                is_string($search) ? $search : null
            );
        return view('products.index', compact('products', 'search'));
    }

    public function create(): View {
        $categories = Category::orderBy('name')->get();
        return view('products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request): RedirectResponse {
        $this->productService->createProduct($request->validated());

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan!');
    }

    public function edit(int $id): View {
        $product = $this->productService->getProductById($id);
        $categories = Category::orderBy('name')->get();

        return view('products.edit', compact('product', 'categories'));
    }

    public function update(UpdateProductRequest $request, int $id): RedirectResponse {
        $this->productService->updateProduct($id, $request->validated());

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui!');
    }

    public function destroy(int $id): RedirectResponse {
        $this->productService->deleteProductById($id);
        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }
}
