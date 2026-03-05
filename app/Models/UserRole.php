<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UserRole extends Pivot
{
    protected $table = 'user_roles';

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = [
        'user_id', 'role_id', 'tenant_id',
        'branch_id', 'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /* ─── İlişkiler ─── */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
