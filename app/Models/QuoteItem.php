<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteItem extends Model
{
    protected $fillable = [
        'quote_id', 'product_id', 'name', 'description', 'quantity',
        'unit', 'unit_price', 'tax_rate', 'tax_amount',
        'discount_rate', 'discount_amount', 'total', 'sort_order',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_rate' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function quote() { return $this->belongsTo(Quote::class); }
    public function product() { return $this->belongsTo(Product::class); }

    public function calculateTotals(): void
    {
        $lineTotal = $this->quantity * $this->unit_price;
        $this->discount_amount = $this->discount_rate > 0 ? $lineTotal * ($this->discount_rate / 100) : $this->discount_amount;
        $afterDiscount = $lineTotal - $this->discount_amount;
        $this->tax_amount = $afterDiscount * ($this->tax_rate / 100);
        $this->total = $afterDiscount + $this->tax_amount;
    }
}
