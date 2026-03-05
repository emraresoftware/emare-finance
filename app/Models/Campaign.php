<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campaign extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id', 'branch_id', 'name', 'description', 'type', 'status',
        'discount_type', 'discount_value', 'min_purchase_amount', 'max_discount_amount',
        'usage_limit', 'usage_count', 'per_customer_limit', 'coupon_code',
        'target_products', 'target_categories', 'target_segments',
        'starts_at', 'ends_at', 'created_by',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'target_products' => 'array',
        'target_categories' => 'array',
        'target_segments' => 'array',
        'discount_value' => 'decimal:2',
        'min_purchase_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
    ];

    public function usages() { return $this->hasMany(CampaignUsage::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function messages() { return $this->hasMany(MarketingMessage::class); }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'discount' => 'İndirim',
            'bogo' => 'Al 1 Öde 1',
            'bundle' => 'Paket',
            'loyalty_bonus' => 'Sadakat Bonusu',
            'free_shipping' => 'Ücretsiz Kargo',
            'gift' => 'Hediye',
            'seasonal' => 'Sezonluk',
            'flash_sale' => 'Flaş İndirim',
            default => $this->type,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft' => 'Taslak',
            'scheduled' => 'Planlandı',
            'active' => 'Aktif',
            'paused' => 'Duraklatıldı',
            'ended' => 'Sona Erdi',
            'cancelled' => 'İptal Edildi',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'gray',
            'scheduled' => 'blue',
            'active' => 'green',
            'paused' => 'yellow',
            'ended' => 'gray',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    public function isActive(): bool
    {
        return $this->status === 'active'
            && $this->starts_at->isPast()
            && ($this->ends_at === null || $this->ends_at->isFuture())
            && ($this->usage_limit === null || $this->usage_count < $this->usage_limit);
    }
}
