<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountTransaction extends Model
{
    protected $fillable = [
        'external_id', 'customer_id', 'type', 'amount',
        'balance_after', 'description', 'reference', 'transaction_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'transaction_date' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'sale' => 'Satış',
            'payment' => 'Ödeme',
            'refund' => 'İade',
            'adjustment' => 'Düzeltme',
            default => $this->type,
        };
    }
}
