<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EInvoice extends Model
{
    use SoftDeletes;

    protected $table = 'e_invoices';

    protected $fillable = [
        'external_id', 'invoice_no', 'uuid', 'direction', 'document_type', 'type', 'scenario', 'status',
        'customer_id', 'receiver_name', 'receiver_tax_number', 'tc_kimlik_no', 'receiver_tax_office', 'receiver_address',
        'recipient_type', 'is_internet_sale', 'internet_sale_platform', 'internet_sale_url',
        'branch_id', 'sale_id', 'currency', 'exchange_rate',
        'subtotal', 'vat_total', 'discount_total', 'grand_total', 'withholding_total',
        'vat_rate', 'notes', 'payment_method', 'invoice_date', 'payment_date', 'payment_platform',
        'sent_at', 'received_at', 'meta',
        'waybill_no', 'shipment_date', 'delivery_address',
        'vehicle_plate', 'driver_name', 'driver_tc',
        'shipping_company', 'tracking_no', 'earsiv_report_no',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'vat_total' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'withholding_total' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'invoice_date' => 'date',
        'payment_date' => 'date',
        'shipment_date' => 'date',
        'is_internet_sale' => 'boolean',
        'sent_at' => 'datetime',
        'received_at' => 'datetime',
        'meta' => 'array',
        'vat_rate' => 'integer',
    ];

    // ── Relations ──

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(EInvoiceItem::class);
    }

    // ── Accessors ──

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Taslak',
            'sent' => 'Gönderildi',
            'accepted' => 'Kabul Edildi',
            'rejected' => 'Reddedildi',
            'cancelled' => 'İptal Edildi',
            default => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'gray',
            'sent' => 'blue',
            'accepted' => 'green',
            'rejected' => 'red',
            'cancelled' => 'orange',
            default => 'gray',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'invoice' => 'Satış Faturası',
            'return' => 'İade Faturası',
            'withholding' => 'Tevkifatlı Fatura',
            'exception' => 'İstisna Faturası',
            'special' => 'Özel Matrah Faturası',
            'waybill' => 'Sevk İrsaliyesi',
            default => ucfirst($this->type),
        };
    }

    public function getDocumentTypeLabelAttribute(): string
    {
        return match ($this->document_type) {
            'fatura' => 'Fatura',
            'irsaliye' => 'İrsaliye',
            default => 'Fatura',
        };
    }

    public function getScenarioLabelAttribute(): string
    {
        return match ($this->scenario) {
            'basic' => 'Temel Fatura',
            'commercial' => 'Ticari Fatura',
            'export' => 'İhracat Faturası',
            'e_arsiv' => 'e-Arşiv Fatura',
            default => ucfirst($this->scenario),
        };
    }

    public function getRecipientTypeLabelAttribute(): string
    {
        return match ($this->recipient_type) {
            'individual' => 'Bireysel (Gerçek Kişi)',
            'corporate' => 'Kurumsal (Tüzel Kişi)',
            default => 'Kurumsal',
        };
    }

    public function getIsEarsivAttribute(): bool
    {
        return $this->scenario === 'e_arsiv';
    }

    public function getDirectionLabelAttribute(): string
    {
        return $this->direction === 'outgoing' ? 'Giden' : 'Gelen';
    }

    // ── Scopes ──

    public function scopeOutgoing($query)
    {
        return $query->where('direction', 'outgoing');
    }

    public function scopeIncoming($query)
    {
        return $query->where('direction', 'incoming');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeFatura($query)
    {
        return $query->where('document_type', 'fatura');
    }

    public function scopeIrsaliye($query)
    {
        return $query->where('document_type', 'irsaliye');
    }

    public function scopeEarsiv($query)
    {
        return $query->where('scenario', 'e_arsiv');
    }

    public function scopeNotEarsiv($query)
    {
        return $query->where('scenario', '!=', 'e_arsiv');
    }
}
