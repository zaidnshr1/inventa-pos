<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Inventory;
use App\Models\StockMutation;
use App\Services\ActivityLogService;
use Carbon\Carbon;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class StockMutationService
{
    public function __construct(
        protected ActivityLogService $logService
    ) {}

    public function getPaginatedMutations(int $perPage = 10): LengthAwarePaginator
    {
        return StockMutation::with(['sourceOutlet', 'targetOutlet', 'requester', 'items.product'])
            ->latest()
            ->paginate($perPage);
    }

    public function getMutationById(int $id): StockMutation
    {
        return StockMutation::with(['sourceOutlet', 'targetOutlet', 'requester', 'approver', 'items.product'])
            ->findOrFail($id);
    }

    public function createRequest(int $sourceOutletId, int $targetOutletId, int $requesterId, ?string $note, array $items): StockMutation
    {
        if ($sourceOutletId === $targetOutletId) {
            throw new InvalidArgumentException('Outlet tidak boleh sama.');
        }

        return DB::transaction(function () use ($sourceOutletId, $targetOutletId, $requesterId, $note, $items) {
            $prefix = 'TRF/' . Carbon::now()->format('Ymd') . '/';
            $todayCount = StockMutation::where('mutation_number', 'like', "{$prefix}%")->count() + 1;
            $mutationNumber = $prefix . str_pad((string)$todayCount, 4, '0', STR_PAD_LEFT);

            $mutation = StockMutation::create([
                'mutation_number' => $mutationNumber,
                'type' => 'TRANSFER',
                'source_outlet_id' => $sourceOutletId,
                'target_outlet_id' => $targetOutletId,
                'status' => 'PENDING',
                'note' => $note,
                'requested_by' => $requesterId,
            ]);

            foreach ($items as $item) {
                $mutation->items()->create([
                    'product_id' => (int) $item['product_id'],
                    'quantity' => (int) $item['quantity'],
                ]);
            }

            $this->logService->log(
                action: 'MUTATION_REQUESTED',
                entityType: StockMutation::class,
                entityId: $mutation->id,
                payload: ['mutation_number' => $mutationNumber, 'total_items' => count($items)]
            );

            return $mutation;
        });
    }

    public function approve(int $mutationId, int $approverId): StockMutation
    {
        return DB::transaction(function () use ($mutationId, $approverId) {
            $mutation = StockMutation::with('items')->findOrFail($mutationId);

            if ($mutation->status !== 'PENDING') {
                throw new Exception('Hanya mutasi berstatus PENDING yang bisa disetujui.');
            }

            foreach ($mutation->items as $item) {
                $sourceInventory = Inventory::where('outlet_id', $mutation->source_outlet_id)
                    ->where('product_id', $item->product_id)
                    ->lockForUpdate()
                    ->first();

                if (! $sourceInventory || $sourceInventory->stock < $item->quantity) {
                    $available = $sourceInventory ? $sourceInventory->stock : 0;
                    throw new Exception("Stok tidak mencukupi untuk produk ID {$item->product_id}. Tersedia: {$available}, Diminta: {$item->quantity}");
                }

                $sourceInventory->decrement('stock', $item->quantity);
            }

            $mutation->update([
                'status' => 'APPROVED',
                'approved_by' => $approverId,
            ]);

            $this->logService->log(
                action: 'MUTATION_APPROVED',
                entityType: StockMutation::class,
                entityId: $mutation->id,
                payload: ['mutation_number' => $mutation->mutation_number]
            );

            return $mutation;
        });
    }

    public function receive(int $mutationId): StockMutation
    {
        return DB::transaction(function () use ($mutationId) {
            $mutation = StockMutation::with('items')->findOrFail($mutationId);

            if ($mutation->status !== 'APPROVED') {
                throw new Exception("Hanya mutasi yang sudah disetujui (APPROVED) yang dapat diterima.");
            }

            foreach ($mutation->items as $item) {
                $targetInventory = Inventory::firstOrCreate(
                    [
                        'outlet_id' => $mutation->target_outlet_id,
                        'product_id' => $item->product_id,
                    ],
                    ['stock' => 0]
                );

                $targetInventory->increment('stock', $item->quantity);
            }

            $mutation->update([
                'status' => 'RECEIVED',
            ]);

            $this->logService->log(
                action: 'MUTATION_RECEIVED',
                entityType: StockMutation::class,
                entityId: $mutation->id,
                payload: ['mutation_number' => $mutation->mutation_number]
            );

            return $mutation;
        });
    }

    public function reject(int $mutationId, int $userId): StockMutation
    {
        return DB::transaction(function () use ($mutationId, $userId) {
            $mutation = StockMutation::findOrFail($mutationId);

            if ($mutation->status !== 'PENDING') {
                throw new Exception("Hanya mutasi berstatus PENDING yang bisa ditolak.");
            }

            $mutation->update([
                'status' => 'REJECTED',
                'approved_by' => $userId,
            ]);

            $this->logService->log(
                action: 'MUTATION_REJECTED',
                entityType: StockMutation::class,
                entityId: $mutation->id,
                payload: ['mutation_number' => $mutation->mutation_number]
            );

            return $mutation;
        });
    }
}
