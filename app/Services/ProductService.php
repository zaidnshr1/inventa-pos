<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Outlet;
use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\ActivityLogService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ProductService
{
    public function __construct(
        protected ProductRepositoryInterface $productRepository,
        protected ActivityLogService $activityLogService
    ){}

    public function getPaginatedProducts(int $perPage = 10, ?string $search = null): LengthAwarePaginator
    {
        return $this->productRepository->getPaginated($perPage, $search);
    }

    public function getProductById(int $id): Product {
        $product = $this->productRepository->findById($id);

        if (! $product) {
            throw new InvalidArgumentException("Produk dengan ID {$id} tidak ditemukan.");
        }
        return $product;
    }

    public function createProduct(array $validatedData): Product {
        return DB::transaction( function () use ($validatedData) {
            
        $product = $this->productRepository->create($validatedData);

        $outlets = Outlet::all();
        foreach ($outlets as $outlet) {
            $product->inventories()->create([
                'outlet_id' => $outlet->id,
                'stock' => 0
            ]);
        }

        $this->activityLogService->log(
            action: 'CREATE',
            entityType: Product::class,
            entityId: $product->id,
            payload: $product->toArray()
        );

        return $product;
        });
    }

    public function updateProduct(int $id, array $validatedData): Product {
        return DB::transaction( function () use ($id, $validatedData) {
            $product = $this->getProductById($id);
            $oldData = $product->toArray();

            $this->productRepository->update($product, $validatedData);

            $this->activityLogService->log(
                action: 'UPDATE',
                entityType: Product::class,
                entityId: $product->id,
                payload: [
                    'before' => $oldData,
                    'after' => $product->fresh()->toArray(),
                ]
            );

            return $product->fresh();
        });
    }

    public function deleteProductById(int $id): bool {
        return DB::transaction( function () use ($id) {
            $product = $this->getProductById($id);
            $deleted = $this->productRepository->delete($product);

            $this->activityLogService->log(
                action: 'DELETE',
                entityType: Product::class,
                entityId: $product->id,
                payload: [
                    'name' => $product->name,
                    'sku' => $product->sku
                ]
            );

            return $deleted;
        });
    }
}
