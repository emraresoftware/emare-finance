<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Tenant extends Model
{
    protected $fillable = [
        'name', 'slug', 'status', 'plan_id',
        'trial_ends_at', 'billing_email', 'meta',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'meta'          => 'array',
    ];

    /* ─── İlişkiler ─── */

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function tenantModules(): HasMany
    {
        return $this->hasMany(TenantModule::class);
    }

    /* ─── Yardımcı Metodlar ─── */

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isOnTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    /**
     * Belirli bir modülün tenant düzeyinde aktif olup olmadığını kontrol eder.
     */
    public function hasModule(string $moduleCode): bool
    {
        $module = Module::findByCode($moduleCode);
        if (!$module) {
            return false;
        }

        if ($module->is_core) {
            return true;
        }

        return $this->tenantModules()
            ->where('module_id', $module->id)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Aktif modül kodlarını döner.
     */
    public function activeModuleCodes(): array
    {
        $tenantModules = $this->tenantModules()
            ->where('is_active', true)
            ->with('module')
            ->get()
            ->pluck('module.code')
            ->toArray();

        $coreModules = Module::where('is_core', true)->pluck('code')->toArray();

        return array_unique(array_merge($coreModules, $tenantModules));
    }
}
