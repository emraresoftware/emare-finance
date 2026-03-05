<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecurringInvoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 'description', 'customer_id', 'branch_id', 'service_category_id',
        'frequency', 'frequency_day', 'currency',
        'subtotal', 'tax_total', 'discount_total', 'grand_total',
        'payment_method', 'status',
        'start_date', 'end_date', 'next_invoice_date', 'last_invoice_date',
        'invoices_generated', 'max_invoices', 'auto_send', 'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'next_invoice_date' => 'date',
        'last_invoice_date' => 'date',
        'auto_send' => 'boolean',
        'frequency_day' => 'integer',
        'invoices_generated' => 'integer',
        'max_invoices' => 'integer',
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

    public function serviceCategory(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(RecurringInvoiceItem::class);
    }

    // ── Accessors ──

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active' => 'Aktif',
            'paused' => 'Duraklatıldı',
            'cancelled' => 'İptal Edildi',
            'completed' => 'Tamamlandı',
            default => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active' => 'green',
            'paused' => 'yellow',
            'cancelled' => 'red',
            'completed' => 'blue',
            default => 'gray',
        };
    }

    public function getFrequencyLabelAttribute(): string
    {
        return match ($this->frequency) {
            'weekly' => 'Haftalık',
            'monthly' => 'Aylık',
            'bimonthly' => '2 Aylık',
            'quarterly' => '3 Aylık',
            'semiannual' => '6 Aylık',
            'annual' => 'Yıllık',
            default => ucfirst($this->frequency),
        };
    }

    // ── Scopes ──

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeDue($query)
    {
        return $query->active()
            ->whereNotNull('next_invoice_date')
            ->where('next_invoice_date', '<=', now()->toDateString());
    }

    // ── Business Logic ──

    /**
     * Sonraki fatura tarihini hesapla
     */
    public function calculateNextInvoiceDate(): ?\Carbon\Carbon
    {
        $base = $this->last_invoice_date ?? $this->start_date;

        $next = match ($this->frequency) {
            'weekly' => $base->copy()->addWeek(),
            'monthly' => $base->copy()->addMonth()->day(min($this->frequency_day, $base->copy()->addMonth()->daysInMonth)),
            'bimonthly' => $base->copy()->addMonths(2)->day(min($this->frequency_day, $base->copy()->addMonths(2)->daysInMonth)),
            'quarterly' => $base->copy()->addMonths(3)->day(min($this->frequency_day, $base->copy()->addMonths(3)->daysInMonth)),
            'semiannual' => $base->copy()->addMonths(6)->day(min($this->frequency_day, $base->copy()->addMonths(6)->daysInMonth)),
            'annual' => $base->copy()->addYear()->day(min($this->frequency_day, $base->copy()->addYear()->daysInMonth)),
            default => null,
        };

        // Bitiş tarihi kontrolü
        if ($next && $this->end_date && $next->gt($this->end_date)) {
            return null;
        }

        // Maksimum fatura sayısı kontrolü
        if ($next && $this->max_invoices && $this->invoices_generated >= $this->max_invoices) {
            return null;
        }

        return $next;
    }

    /**
     * Bu tekrarlayan faturanın hala aktif olup olmadığını kontrol et
     */
    public function isStillActive(): bool
    {
        if ($this->status !== 'active') return false;
        if ($this->end_date && now()->gt($this->end_date)) return false;
        if ($this->max_invoices && $this->invoices_generated >= $this->max_invoices) return false;
        return true;
    }

    /**
     * Toplamları yeniden hesapla
     */
    public function recalculateTotals(): void
    {
        $subtotal = 0;
        $taxTotal = 0;
        $discountTotal = 0;

        foreach ($this->items as $item) {
            $lineSubtotal = $item->quantity * $item->unit_price;
            $subtotal += $lineSubtotal;
            $discountTotal += $item->discount;
            $taxTotal += $item->tax_amount;
        }

        $this->update([
            'subtotal' => $subtotal,
            'tax_total' => $taxTotal,
            'discount_total' => $discountTotal,
            'grand_total' => $subtotal - $discountTotal + $taxTotal,
        ]);
    }
}
