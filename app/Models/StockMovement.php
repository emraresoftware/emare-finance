<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'type', 'barcode', 'product_id', 'product_name',
        'transaction_code', 'note', 'firm_customer', 'payment_type',
        'quantity', 'remaining', 'unit_price', 'total', 'movement_date',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'remaining' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2',
        'movement_date' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
