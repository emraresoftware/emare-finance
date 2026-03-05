<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SignagePlaylistItem extends Model
{
    protected $fillable = [
        'playlist_id', 'content_id', 'sort_order', 'duration_override',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'duration_override' => 'integer',
    ];

    public function playlist(): BelongsTo
    {
        return $this->belongsTo(SignagePlaylist::class, 'playlist_id');
    }

    public function content(): BelongsTo
    {
        return $this->belongsTo(SignageContent::class, 'content_id');
    }
}
