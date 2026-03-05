<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MarketingMessage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id', 'title', 'content', 'channel', 'status',
        'segment_id', 'campaign_id', 'recipient_filters',
        'total_recipients', 'sent_count', 'delivered_count',
        'opened_count', 'clicked_count', 'bounced_count',
        'scheduled_at', 'sent_at', 'created_by',
    ];

    protected $casts = [
        'recipient_filters' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function segment() { return $this->belongsTo(CustomerSegment::class, 'segment_id'); }
    public function campaign() { return $this->belongsTo(Campaign::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function logs() { return $this->hasMany(MarketingMessageLog::class, 'message_id'); }

    public function getChannelLabelAttribute(): string
    {
        return match($this->channel) {
            'email' => 'E-posta',
            'sms' => 'SMS',
            'whatsapp' => 'WhatsApp',
            'push' => 'Bildirim',
            default => $this->channel,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft' => 'Taslak',
            'scheduled' => 'Planlandı',
            'sending' => 'Gönderiliyor',
            'sent' => 'Gönderildi',
            'failed' => 'Başarısız',
            'cancelled' => 'İptal',
            default => $this->status,
        };
    }

    public function getOpenRateAttribute(): float
    {
        return $this->delivered_count > 0 ? round(($this->opened_count / $this->delivered_count) * 100, 1) : 0;
    }

    public function getClickRateAttribute(): float
    {
        return $this->delivered_count > 0 ? round(($this->clicked_count / $this->delivered_count) * 100, 1) : 0;
    }
}
