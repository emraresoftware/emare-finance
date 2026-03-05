<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SmsScenario extends Model
{
    protected $fillable = [
        'tenant_id', 'name', 'trigger_event', 'template_id',
        'target_type', 'customer_type_filter', 'segment_id',
        'conditions', 'schedule_type', 'delay_minutes',
        'cron_expression', 'send_time', 'is_active', 'priority',
    ];

    protected $casts = [
        'conditions'    => 'array',
        'is_active'     => 'boolean',
        'send_time'     => 'datetime:H:i',
        'delay_minutes' => 'integer',
        'priority'      => 'integer',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(SmsTemplate::class, 'template_id');
    }

    public function segment(): BelongsTo
    {
        return $this->belongsTo(CustomerSegment::class, 'segment_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(SmsLog::class, 'scenario_id');
    }

    public static function getTriggerEvents(): array
    {
        return [
            'sale_completed'    => 'Satış Tamamlandı',
            'payment_received'  => 'Ödeme Alındı',
            'customer_birthday' => 'Müşteri Doğum Günü',
            'customer_register' => 'Yeni Müşteri Kaydı',
            'loyalty_earned'    => 'Sadakat Puanı Kazanıldı',
            'loyalty_redeemed'  => 'Sadakat Puanı Harcandı',
            'campaign_start'    => 'Kampanya Başladı',
            'campaign_end'      => 'Kampanya Bitiyor',
            'invoice_created'   => 'Fatura Oluşturuldu',
            'payment_reminder'  => 'Ödeme Hatırlatma',
            'inactivity'        => 'Uzun Süredir Gelmemiş',
            'welcome'           => 'Hoş Geldin Mesajı',
            'scheduled'         => 'Zamanlanmış',
            'manual'            => 'Manuel Gönderim',
        ];
    }

    public function getTriggerEventLabelAttribute(): string
    {
        return self::getTriggerEvents()[$this->trigger_event] ?? $this->trigger_event;
    }

    public static function getTargetTypes(): array
    {
        return [
            'all'           => 'Tüm Müşteriler',
            'customer'      => 'Belirli Müşteri',
            'customer_type' => 'Müşteri Tipine Göre',
            'segment'       => 'Müşteri Segmentine Göre',
            'manual'        => 'Manuel Seçim',
        ];
    }

    public function getTargetTypeLabelAttribute(): string
    {
        return self::getTargetTypes()[$this->target_type] ?? $this->target_type;
    }

    public static function getScheduleTypes(): array
    {
        return [
            'immediate' => 'Anında',
            'delayed'   => 'Gecikmeli',
            'scheduled' => 'Zamanlanmış',
            'recurring' => 'Tekrarlayan',
        ];
    }

    public function getScheduleTypeLabelAttribute(): string
    {
        return self::getScheduleTypes()[$this->schedule_type] ?? $this->schedule_type;
    }

    public static function getCustomerTypes(): array
    {
        return [
            'bireysel'  => 'Bireysel',
            'kurumsal'  => 'Kurumsal',
            'vip'       => 'VIP',
            'toptan'    => 'Toptan',
            'perakende' => 'Perakende',
        ];
    }
}
