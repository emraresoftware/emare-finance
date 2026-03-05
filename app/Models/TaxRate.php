<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model
{
    protected $fillable = [
        'name', 'code', 'rate', 'type', 'description',
        'is_default', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'rate' => 'decimal:4',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    // ── Scopes ──

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCode($query, string $code)
    {
        return $query->where('code', $code);
    }

    public function scopeKdv($query)
    {
        return $query->where('code', 'KDV');
    }

    public function scopeOtv($query)
    {
        return $query->where('code', 'OTV');
    }

    public function scopeOiv($query)
    {
        return $query->where('code', 'OIV');
    }

    // ── Accessors ──

    public function getCodeLabelAttribute(): string
    {
        return match ($this->code) {
            'KDV' => 'Katma Değer Vergisi',
            'OTV' => 'Özel Tüketim Vergisi',
            'OIV' => 'Özel İletişim Vergisi',
            'DAMGA' => 'Damga Vergisi',
            'CEVRE' => 'Çevre Temizlik Vergisi',
            'KONAKLAMA' => 'Konaklama Vergisi',
            'BSMV' => 'Banka ve Sigorta Muameleleri Vergisi',
            'SIVI' => 'Sıvılaştırılmış Petrol Gazları Vergisi',
            default => $this->code,
        };
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->type === 'percentage') {
            return "{$this->code} %{$this->rate}";
        }
        return "{$this->code} ₺" . number_format($this->rate, 2, ',', '.');
    }

    // ── Helpers ──

    public function calculateTax(float $amount): float
    {
        if ($this->type === 'percentage') {
            return round($amount * $this->rate / 100, 2);
        }
        return (float) $this->rate;
    }

    /**
     * Varsayılan KDV oranlarını getir
     */
    public static function defaultKdvRates(): array
    {
        return static::active()->kdv()->orderBy('rate')->pluck('rate')->toArray();
    }

    /**
     * Tüm vergi türlerini grupla
     */
    public static function groupedByCode()
    {
        return static::active()
            ->orderBy('sort_order')
            ->orderBy('rate')
            ->get()
            ->groupBy('code');
    }
}
