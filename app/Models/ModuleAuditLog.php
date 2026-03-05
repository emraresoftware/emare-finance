<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModuleAuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'actor_user_id', 'tenant_id', 'branch_id',
        'module_id', 'action', 'before', 'after',
        'ip', 'created_at',
    ];

    protected $casts = [
        'before'     => 'array',
        'after'      => 'array',
        'created_at' => 'datetime',
    ];

    /* ─── İlişkiler ─── */

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    /* ─── Factory ─── */

    public static function logAction(
        string $action,
        Module $module,
        ?array $before = null,
        ?array $after = null,
        ?int $tenantId = null,
        ?int $branchId = null,
    ): self {
        return static::create([
            'actor_user_id' => auth()->id(),
            'tenant_id'     => $tenantId,
            'branch_id'     => $branchId,
            'module_id'     => $module->id,
            'action'        => $action,
            'before'        => $before,
            'after'         => $after,
            'ip'            => request()->ip(),
            'created_at'    => now(),
        ]);
    }
}
