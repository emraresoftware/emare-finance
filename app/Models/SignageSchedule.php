<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SignageSchedule extends Model
{
    protected $fillable = [
        'name', 'playlist_id', 'time_start', 'time_end',
        'days', 'priority', 'is_active',
    ];

    protected $casts = [
        'days' => 'array',
        'priority' => 'integer',
        'is_active' => 'boolean',
    ];

    protected $appends = ['playlist_name'];

    public function playlist(): BelongsTo
    {
        return $this->belongsTo(SignagePlaylist::class, 'playlist_id');
    }

    public function getPlaylistNameAttribute(): string
    {
        return $this->playlist?->name ?? '-';
    }
}
