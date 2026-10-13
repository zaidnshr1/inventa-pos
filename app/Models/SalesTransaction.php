<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Outlet;
use App\Models\SalesTransactionItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesTransaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'outlet_id',
        'user_id',
        'customer_name',
        'customer_phone',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'grand_total',
        'payment_method',
        'payment_status',
        'paid_amount',
        'change_amount',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'grand_total' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'change_amount' => 'decimal:2',
        ];
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SalesTransactionItem::class);
    }
}
