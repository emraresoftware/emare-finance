<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecurringInvoiceItem extends Model
{
    protected $fillable = [
        'recurring_invoice_id', 'product_id', 'product_name', 'product_code',
        'unit', 'quantity', 'unit_price', 'discount',
        'taxes', 'tax_amount', 'total',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'taxes' => 'array',
    ];

    // ── Relations ──

    public function recurringInvoice(): BelongsTo
    {
        return $this->belongsTo(RecurringInvoice::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // ── Helpers ──

    /**
     * Kalem toplamlarını hesapla
     */
    public function calculateTotals(): void
    {
        $lineTotal = ($this->quantity * $this->unit_price) - $this->discount;
        $taxAmount = 0;

        if (!empty($this->taxes)) {
            foreach ($this->taxes as $tax) {
                $rate = $tax['rate'] ?? 0;
                $taxAmount += round($lineTotal * $rate / 100, 2);
            }
        }

        $this->tax_amount = $taxAmount;
        $this->total = round($lineTotal + $taxAmount, 2);
    }
}
