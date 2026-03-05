<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantModule extends Model
{
    protected $fillable = [
        'tenant_id', 'module_id', 'is_active',
        'activated_at', 'expires_at', 'config',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'activated_at' => 'datetime',
        'expires_at'   => 'datetime',
        'config'       => 'array',
    ];

    /* ─── İlişkiler ─── */

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }
}
