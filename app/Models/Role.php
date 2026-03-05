<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = [
        'code', 'name', 'description', 'scope', 'is_system',
    ];

    protected $casts = [
        'is_system' => 'boolean',
    ];

    /* ─── İlişkiler ─── */

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    public function userRoles(): HasMany
    {
        return $this->hasMany(UserRole::class);
    }

    /* ─── Yardımcı Metodlar ─── */

    public function hasPermission(string $permissionCode): bool
    {
        return $this->permissions()->where('code', $permissionCode)->exists();
    }

    public function givePermission(string ...$permissionCodes): void
    {
        $permissions = Permission::whereIn('code', $permissionCodes)->get();
        $this->permissions()->syncWithoutDetaching($permissions);
    }

    public function revokePermission(string ...$permissionCodes): void
    {
        $permissions = Permission::whereIn('code', $permissionCodes)->get();
        $this->permissions()->detach($permissions);
    }

    public static function findByCode(string $code): ?self
    {
        return static::where('code', $code)->first();
    }
}
