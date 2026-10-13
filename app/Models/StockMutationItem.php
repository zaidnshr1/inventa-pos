<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Product;
use App\Models\StockMutation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMutationItem extends Model
{
    protected $fillable = [
        'stock_mutation_id',
        'product_id',
        'quantity',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function mutation(): BelongsTo
    {
        return $this->belongsTo(StockMutation::class, 'stock_mutation_id');
    }
}
