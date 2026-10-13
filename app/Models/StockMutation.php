<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockMutation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'mutation_number',
        'type',
        'source_outlet_id',
        'target_outlet_id',
        'status',
        'note',
        'requested_by',
        'approved_by',
    ];

    public function sourceOutlet(): BelongsTo {
        return $this->belongsTo(Outlet::class, 'source_outlet_id');
    }

    public function targetOutlet(): BelongsTo {
        return $this->belongsTo(Outlet::class, 'target_outlet_id');
    }

    public function requester(): BelongsTo {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver(): BelongsTo {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany {
        return $this->hasMany(StockMutationItem::class);
    }
}
