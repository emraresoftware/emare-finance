<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedbackMessage extends Model
{
    protected $fillable = [
        'user_id', 'page_url', 'category', 'priority', 'message',
        'admin_reply', 'replied_by', 'replied_at', 'status',
        'screenshot_path', 'meta',
    ];

    protected $casts = [
        'replied_at' => 'datetime',
        'meta' => 'array',
    ];

    // ── Relations ──

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function repliedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'replied_by');
    }

    // ── Accessors ──

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'bug' => 'Hata / Sorun',
            'suggestion' => 'Öneri',
            'question' => 'Soru',
            'other' => 'Diğer',
            default => ucfirst($this->category),
        };
    }

    public function getCategoryIconAttribute(): string
    {
        return match ($this->category) {
            'bug' => 'fa-bug',
            'suggestion' => 'fa-lightbulb',
            'question' => 'fa-question-circle',
            'other' => 'fa-comment',
            default => 'fa-comment',
        };
    }

    public function getCategoryColorAttribute(): string
    {
        return match ($this->category) {
            'bug' => 'red',
            'suggestion' => 'blue',
            'question' => 'purple',
            'other' => 'gray',
            default => 'gray',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'open' => 'Açık',
            'in_progress' => 'İnceleniyor',
            'resolved' => 'Çözüldü',
            'closed' => 'Kapatıldı',
            default => ucfirst($this->status),
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return match ($this->priority) {
            'low' => 'Düşük',
            'normal' => 'Normal',
            'high' => 'Yüksek',
            'critical' => 'Kritik',
            default => 'Normal',
        };
    }

    // ── Scopes ──

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeUnresolved($query)
    {
        return $query->whereIn('status', ['open', 'in_progress']);
    }
}
