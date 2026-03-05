<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SignagePlaylist extends Model
{
    protected $fillable = [
        'name', 'loop', 'schedule_text', 'status', 'meta',
    ];

    protected $casts = [
        'loop' => 'boolean',
        'meta' => 'array',
    ];

    protected $appends = ['schedule', 'content_count', 'device_count', 'total_duration', 'item_ids'];

    public function playlistItems(): HasMany
    {
        return $this->hasMany(SignagePlaylistItem::class, 'playlist_id')->orderBy('sort_order');
    }

    public function contents(): BelongsToMany
    {
        return $this->belongsToMany(SignageContent::class, 'signage_playlist_items', 'playlist_id', 'content_id')
                    ->withPivot('sort_order', 'duration_override')
                    ->withTimestamps()
                    ->orderByPivot('sort_order');
    }

    public function devices(): BelongsToMany
    {
        return $this->belongsToMany(SignageDevice::class, 'signage_device_playlist', 'playlist_id', 'device_id')
                    ->withPivot('priority')
                    ->withTimestamps();
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(SignageSchedule::class, 'playlist_id');
    }

    public function getScheduleAttribute(): string
    {
        return $this->schedule_text ?? 'Zamanlama yok';
    }

    public function getContentCountAttribute(): int
    {
        return $this->contents()->count();
    }

    public function getDeviceCountAttribute(): int
    {
        return $this->devices()->count();
    }

    public function getTotalDurationAttribute(): string
    {
        $seconds = $this->contents->sum('duration');
        if ($seconds <= 0) return 'Sürekli';
        $m = intdiv($seconds, 60);
        $s = $seconds % 60;
        return ($m > 0 ? $m . ' dk ' : '') . ($s > 0 ? $s . ' sn' : '');
    }

    public function getItemIdsAttribute(): array
    {
        return $this->playlistItems->pluck('content_id')->toArray();
    }
}
