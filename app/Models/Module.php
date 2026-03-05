<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Module extends Model
{
    protected $fillable = [
        'code', 'name', 'description', 'is_core',
        'scope', 'dependencies', 'sort_order',
    ];

    protected $casts = [
        'is_core'      => 'boolean',
        'dependencies' => 'array',
    ];

    /* ─── İlişkiler ─── */

    public function plans(): BelongsToMany
    {
        return $this->belongsToMany(Plan::class, 'plan_modules')
            ->withPivot('included', 'config')
            ->withTimestamps();
    }

    public function tenantModules(): HasMany
    {
        return $this->hasMany(TenantModule::class);
    }

    public function branchModules(): HasMany
    {
        return $this->hasMany(BranchModule::class);
    }

    /* ─── Yardımcı Metodlar ─── */

    /**
     * Belirli bir branch için modülün aktif olup olmadığını kontrol eder.
     */
    public function isActiveForBranch(?Branch $branch, ?Tenant $tenant = null): bool
    {
        if ($this->is_core) {
            return true;
        }

        // Branch scope
        if (in_array($this->scope, ['branch', 'both']) && $branch) {
            $bm = $this->branchModules()->where('branch_id', $branch->id)->first();
            if ($bm) {
                return $bm->is_active;
            }
        }

        // Tenant scope (fallback)
        if (in_array($this->scope, ['tenant', 'both']) && $tenant) {
            $tm = $this->tenantModules()->where('tenant_id', $tenant->id)->first();
            if ($tm) {
                return $tm->is_active;
            }
        }

        return false;
    }

    /**
     * Bağımlı modüllerin kodlarını döner.
     */
    public function getDependencyCodes(): array
    {
        return $this->dependencies ?? [];
    }

    /**
     * Modül kodu ile bul.
     */
    public static function findByCode(string $code): ?self
    {
        return static::where('code', $code)->first();
    }
}
