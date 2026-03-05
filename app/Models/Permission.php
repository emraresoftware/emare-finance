<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $fillable = [
        'code', 'name', 'module_code', 'group',
    ];

    /* ─── İlişkiler ─── */

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    /* ─── Scopelar ─── */

    public function scopeForModule($query, string $moduleCode)
    {
        return $query->where('module_code', $moduleCode);
    }

    public function scopeInGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    public static function findByCode(string $code): ?self
    {
        return static::where('code', $code)->first();
    }
}
