<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchModule extends Model
{
    protected $fillable = [
        'branch_id', 'module_id', 'is_active',
        'activated_at', 'config',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'activated_at' => 'datetime',
        'config'       => 'array',
    ];

    /* ─── İlişkiler ─── */

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }
}
