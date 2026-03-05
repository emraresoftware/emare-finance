<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountPlan extends Model
{
    protected $table = 'account_plan';

    protected $fillable = [
        'code', 'name', 'type', 'normal_balance', 'level', 'parent_code', 'is_active', 'is_system',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_system' => 'boolean',
    ];

    // ── İlişkiler ────────────────────────────────────────────────

    public function lines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class, 'account_code', 'code');
    }

    public function parent(): ?self
    {
        return $this->parent_code ? static::where('code', $this->parent_code)->first() : null;
    }

    public function children(): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('parent_code', $this->code)->orderBy('code')->get();
    }

    // ── Bakiye Hesaplama ─────────────────────────────────────────

    /**
     * Belirli tarih aralığında hesabın bakiyesini hesapla.
     * Doğal bakiyesi "debit" olan hesaplar: Borç - Alacak
     * Doğal bakiyesi "credit" olan hesaplar: Alacak - Borç
     */
    public function balance(?string $startDate = null, ?string $endDate = null): float
    {
        $query = JournalEntryLine::where('account_code', $this->code)
            ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                $q->where('is_posted', true);
                if ($startDate) $q->where('date', '>=', $startDate);
                if ($endDate)   $q->where('date', '<=', $endDate);
            });

        $debit  = (float) $query->sum('debit');
        $credit = (clone $query)->sum('credit');

        return $this->normal_balance === 'debit'
            ? $debit - $credit
            : $credit - $debit;
    }

    // ── Etiket Yardımcıları ──────────────────────────────────────

    public function getTypeLabel(): string
    {
        return [
            'asset'     => 'Varlık',
            'liability' => 'Yükümlülük',
            'equity'    => 'Öz Kaynak',
            'revenue'   => 'Gelir',
            'cost'      => 'Satış Maliyeti',
            'expense'   => 'Gider',
        ][$this->type] ?? $this->type;
    }

    // ── Tiplerine göre scope ─────────────────────────────────────

    public function scopeAssets($q)     { return $q->where('type', 'asset'); }
    public function scopeLiabilities($q){ return $q->where('type', 'liability'); }
    public function scopeEquity($q)     { return $q->where('type', 'equity'); }
    public function scopeRevenue($q)    { return $q->where('type', 'revenue'); }
    public function scopeExpenses($q)   { return $q->whereIn('type', ['expense', 'cost']); }
    public function scopeActive($q)     { return $q->where('is_active', true); }
    public function scopeLevel($q, int $level) { return $q->where('level', $level); }
}
