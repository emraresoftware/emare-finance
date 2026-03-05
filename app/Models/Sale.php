<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'external_id', 'receipt_no', 'branch_id', 'customer_id', 'user_id',
        'payment_method', 'subtotal', 'vat_total', 'discount_total',
        'grand_total', 'discount', 'cash_amount', 'card_amount',
        'total_items', 'status', 'notes', 'staff_name', 'application', 'note', 'sold_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'vat_total' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'discount' => 'decimal:2',
        'cash_amount' => 'decimal:2',
        'card_amount' => 'decimal:2',
        'sold_at' => 'datetime',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'cash' => 'Nakit',
            'card' => 'Kart',
            'mixed' => 'Karışık',
            'credit' => 'Veresiye',
            default => $this->payment_method,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'completed' => 'Tamamlandı',
            'cancelled' => 'İptal Edildi',
            'refunded' => 'İade Edildi',
            default => $this->status,
        };
    }
}
