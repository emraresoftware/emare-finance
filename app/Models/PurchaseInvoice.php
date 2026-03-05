<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseInvoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'external_id', 'invoice_type', 'invoice_no', 'firm_id', 'branch_id',
        'waybill_no', 'document_no', 'payment_type', 'total_items',
        'total_amount', 'invoice_date', 'shipment_date', 'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'invoice_date' => 'date',
        'shipment_date' => 'date',
    ];

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseInvoiceItem::class);
    }
}
