<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quote extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id', 'branch_id', 'quote_number', 'customer_id',
        'customer_name', 'customer_email', 'customer_phone',
        'customer_company', 'customer_tax_number', 'customer_address',
        'title', 'description', 'status', 'subtotal', 'tax_total',
        'discount_total', 'grand_total', 'currency', 'issue_date',
        'valid_until', 'notes', 'terms', 'created_by',
        'sent_at', 'viewed_at', 'accepted_at', 'rejected_at', 'rejection_reason',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'valid_until' => 'date',
        'sent_at' => 'datetime',
        'viewed_at' => 'datetime',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public function items() { return $this->hasMany(QuoteItem::class)->orderBy('sort_order'); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft' => 'Taslak',
            'sent' => 'Gönderildi',
            'viewed' => 'Görüntülendi',
            'accepted' => 'Kabul Edildi',
            'rejected' => 'Reddedildi',
            'expired' => 'Süresi Doldu',
            'converted' => 'Siparişe Dönüştü',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'gray',
            'sent' => 'blue',
            'viewed' => 'indigo',
            'accepted' => 'green',
            'rejected' => 'red',
            'expired' => 'yellow',
            'converted' => 'emerald',
            default => 'gray',
        };
    }

    public function isExpired(): bool
    {
        return $this->valid_until->isPast() && !in_array($this->status, ['accepted', 'converted']);
    }

    public function recalculate(): void
    {
        $this->subtotal = $this->items->sum(fn($i) => $i->quantity * $i->unit_price);
        $this->tax_total = $this->items->sum('tax_amount');
        $this->discount_total = $this->items->sum('discount_amount');
        $this->grand_total = $this->subtotal + $this->tax_total - $this->discount_total;
        $this->save();
    }

    public static function generateNumber(): string
    {
        $prefix = 'TKL-' . date('Y');
        $last = static::where('quote_number', 'like', $prefix . '%')->max('quote_number');
        $seq = $last ? (int)substr($last, -5) + 1 : 1;
        return $prefix . '-' . str_pad($seq, 5, '0', STR_PAD_LEFT);
    }
}
