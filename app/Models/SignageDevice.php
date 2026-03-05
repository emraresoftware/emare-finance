<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SignageDevice extends Model
{
    protected $fillable = [
        'name', 'location', 'resolution', 'orientation', 'template',
        'device_type', 'model', 'os', 'ip_address', 'mac_address',
        'brightness', 'volume', 'auto_power', 'power_on', 'power_off',
        'api_token', 'status', 'last_ping_at', 'meta',
    ];

    protected $casts = [
        'auto_power' => 'boolean',
        'brightness' => 'integer',
        'volume' => 'integer',
        'meta' => 'array',
        'last_ping_at' => 'datetime',
    ];

    protected $appends = ['last_ping', 'cpu_usage', 'memory_usage', 'storage_usage', 'uptime', 'playlist_id'];

    public function playlists(): BelongsToMany
    {
        return $this->belongsToMany(SignagePlaylist::class, 'signage_device_playlist', 'device_id', 'playlist_id')
                    ->withPivot('priority')
                    ->withTimestamps();
    }

    public function isOnline(): bool
    {
        return $this->status === 'online';
    }

    public function getLastPingAttribute(): string
    {
        if (!$this->last_ping_at) return 'Hiç bağlanmadı';
        return $this->last_ping_at->diffForHumans();
    }

    public function getCpuUsageAttribute(): int
    {
        return ($this->meta['cpu_usage'] ?? 0);
    }

    public function getMemoryUsageAttribute(): int
    {
        return ($this->meta['memory_usage'] ?? 0);
    }

    public function getStorageUsageAttribute(): int
    {
        return ($this->meta['storage_usage'] ?? 0);
    }

    public function getUptimeAttribute(): string
    {
        return ($this->meta['uptime'] ?? '-');
    }

    public function getPlaylistIdAttribute(): ?int
    {
        return $this->playlists->first()?->id;
    }
}
