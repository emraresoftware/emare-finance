<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class JournalEntry extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'entry_no', 'date', 'description', 'type',
        'reference_type', 'reference_id',
        'is_posted', 'branch_id', 'created_by',
    ];

    protected $casts = [
        'date'      => 'date',
        'is_posted' => 'boolean',
    ];

    // ── İlişkiler ────────────────────────────────────────────────

    public function lines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class)->orderBy('line_order');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Toplam Borç / Alacak ─────────────────────────────────────

    public function getTotalDebitAttribute(): float
    {
        return (float) $this->lines->sum('debit');
    }

    public function getTotalCreditAttribute(): float
    {
        return (float) $this->lines->sum('credit');
    }

    public function isBalanced(): bool
    {
        return abs($this->total_debit - $this->total_credit) < 0.01;
    }

    // ── Tür Etiketi ──────────────────────────────────────────────

    public function getTypeLabel(): string
    {
        return [
            'opening'    => 'Açılış',
            'sale'       => 'Satış',
            'purchase'   => 'Alış',
            'expense'    => 'Gider',
            'income'     => 'Tahsilat',
            'payroll'    => 'Bordro',
            'adjustment' => 'Düzeltme',
            'closing'    => 'Kapanış',
            'manual'     => 'Manuel',
        ][$this->type] ?? $this->type;
    }

    // ── Otomatik Fiş Numarası ─────────────────────────────────────

    public static function nextEntryNo(): string
    {
        $year  = date('Y');
        $count = static::whereYear('created_at', $year)->withTrashed()->count() + 1;
        return 'YV-' . $year . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }
}
