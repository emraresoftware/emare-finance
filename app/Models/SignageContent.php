<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SignageContent extends Model
{
    protected $fillable = [
        'name', 'type', 'file_path', 'file_url', 'url',
        'resolution', 'file_size', 'duration', 'tags', 'meta', 'status',
    ];

    protected $casts = [
        'tags' => 'array',
        'meta' => 'array',
        'duration' => 'integer',
    ];

    protected $appends = ['type_label', 'icon', 'thumbnail_color'];

    public function playlistItems(): HasMany
    {
        return $this->hasMany(SignagePlaylistItem::class, 'content_id');
    }

    public function playlists(): BelongsToMany
    {
        return $this->belongsToMany(SignagePlaylist::class, 'signage_playlist_items', 'content_id', 'playlist_id')
                    ->withPivot('sort_order', 'duration_override')
                    ->withTimestamps();
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'image' => 'Görsel',
            'video' => 'Video',
            'template' => 'Şablon',
            'widget' => 'Widget',
            'url' => 'Web Sayfası',
            default => $this->type ?? '-',
        };
    }

    public function getIconAttribute(): string
    {
        return match($this->type) {
            'image' => 'fa-image',
            'video' => 'fa-video',
            'template' => 'fa-palette',
            'widget' => 'fa-code',
            'url' => 'fa-globe',
            default => 'fa-file',
        };
    }

    public function getThumbnailColorAttribute(): string
    {
        return match($this->type) {
            'image' => 'from-amber-400 to-orange-500',
            'video' => 'from-rose-400 to-pink-500',
            'template' => 'from-violet-400 to-purple-500',
            'widget' => 'from-cyan-400 to-blue-500',
            'url' => 'from-emerald-400 to-green-500',
            default => 'from-gray-400 to-gray-500',
        };
    }
}
