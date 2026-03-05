<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsLog extends Model
{
    protected $fillable = [
        'tenant_id', 'scenario_id', 'template_id', 'customer_id',
        'phone', 'content', 'status', 'provider_message_id',
        'error_message', 'cost', 'trigger_event', 'meta',
        'sent_at', 'delivered_at',
    ];

    protected $casts = [
        'cost'         => 'decimal:4',
        'meta'         => 'array',
        'sent_at'      => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function scenario(): BelongsTo
    {
        return $this->belongsTo(SmsScenario::class, 'scenario_id');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(SmsTemplate::class, 'template_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public static function getStatusOptions(): array
    {
        return [
            'pending'   => 'Bekliyor',
            'sent'      => 'Gönderildi',
            'delivered' => 'İletildi',
            'failed'    => 'Başarısız',
            'rejected'  => 'Reddedildi',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::getStatusOptions()[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'yellow',
            'sent'      => 'blue',
            'delivered' => 'green',
            'failed'    => 'red',
            'rejected'  => 'gray',
            default     => 'gray',
        };
    }
}
