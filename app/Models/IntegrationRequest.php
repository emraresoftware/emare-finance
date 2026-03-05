<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IntegrationRequest extends Model
{
    protected $fillable = [
        'tenant_id',
        'user_id',
        'integration_type',
        'integration_name',
        'message',
        'status',
        'admin_note',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    /* ─── Sabitler ─── */

    const STATUS_PENDING  = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    const STATUS_LABELS = [
        'pending'  => 'Beklemede',
        'approved' => 'Onaylandı',
        'rejected' => 'Reddedildi',
    ];

    const STATUS_COLORS = [
        'pending'  => 'yellow',
        'approved' => 'green',
        'rejected' => 'red',
    ];

    /* ─── İlişkiler ─── */

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /* ─── Scope'lar ─── */

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeForTenant($query, ?int $tenantId = null)
    {
        if ($tenantId) {
            return $query->where('tenant_id', $tenantId);
        }
        return $query;
    }

    /* ─── Yardımcılar ─── */

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'gray';
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }
}
