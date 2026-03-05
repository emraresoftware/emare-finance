<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsAutomationQueue extends Model
{
    protected $table = 'sms_automation_queue';

    protected $fillable = [
        'tenant_id', 'scenario_id', 'customer_id', 'template_id',
        'phone', 'content', 'trigger_event', 'variables',
        'scheduled_at', 'sent_at', 'status', 'error_message', 'meta',
    ];

    protected $casts = [
        'variables'    => 'array',
        'meta'         => 'array',
        'scheduled_at' => 'datetime',
        'sent_at'      => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(SmsTemplate::class, 'template_id');
    }

    public function scenario(): BelongsTo
    {
        return $this->belongsTo(SmsScenario::class, 'scenario_id');
    }

    public static function getStatusOptions(): array
    {
        return [
            'pending'   => 'Bekliyor',
            'sent'      => 'Gönderildi',
            'failed'    => 'Başarısız',
            'cancelled' => 'İptal Edildi',
        ];
    }

    /**
     * Bekleyen ve zamanı gelmiş SMS'leri getir
     */
    public static function getReadyToSend()
    {
        return static::where('status', 'pending')
            ->where('scheduled_at', '<=', now())
            ->orderBy('scheduled_at')
            ->get();
    }
}
