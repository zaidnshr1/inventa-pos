<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentProductRepository implements ProductRepositoryInterface
{
    public function getPaginated(int $perPage = 15, ?string $search = null): LengthAwarePaginator
    {
        return Product::query()
            ->with(['category', 'inventories.outlet'])
            ->when($search, function ($query, string $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'ilike', "%{$search}%")
                      ->orWhere('sku', 'ilike', "%{$search}%")
                      ->orWhere('barcode', 'ilike', "%{$search}%");
                });
            })
            ->latest()
            ->paginate($perPage);
    }

    public function findById(int $id): Product 
    {
        return Product::with(['category', 'inventories.outlet'])->find($id);
    }

    public function create(array $data): Product {
        return Product::create($data);
    }

    public function update(Product $product, array $data): bool {
        return $product->update($data);
    }

    public function delete(Product $product): bool {
        return (bool) $product->delete();
    }
}
