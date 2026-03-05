<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EInvoiceItem extends Model
{
    protected $table = 'e_invoice_items';

    protected $fillable = [
        'e_invoice_id', 'product_id', 'product_name', 'product_code',
        'unit', 'quantity', 'unit_price', 'discount',
        'vat_rate', 'vat_amount', 'total',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'vat_rate' => 'integer',
    ];

    public function eInvoice(): BelongsTo
    {
        return $this->belongsTo(EInvoice::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
