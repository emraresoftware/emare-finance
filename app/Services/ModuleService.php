<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Module;
use App\Models\Tenant;
use Illuminate\Support\Collection;

/**
 * Modül yönetim servisi.
 * Modül aktivasyonu, kontrol ve yapılandırma işlemlerini yönetir.
 */
class ModuleService
{
    public function __construct(
        private TenantContext $tenantContext,
    ) {}

    /**
     * Belirli bir modülün aktif olup olmadığını kontrol eder.
     */
    public function isActive(string $moduleCode, ?Branch $branch = null): bool
    {
        $module = Module::findByCode($moduleCode);
        if (!$module) {
            return false;
        }

        if ($module->is_core) {
            return true;
        }

        $tenant = $this->tenantContext->getTenant();

        if ($branch) {
            return $module->isActiveForBranch($branch, $tenant);
        }

        if ($tenant) {
            return $tenant->hasModule($moduleCode);
        }

        return false;
    }

    /**
     * Tenant düzeyinde aktif modülleri döner.
     */
    public function getActiveModules(?Tenant $tenant = null): Collection
    {
        $tenant = $tenant ?? $this->tenantContext->getTenant();

        if (!$tenant) {
            return Module::where('is_core', true)->get();
        }

        $coreModules = Module::where('is_core', true)->get();

        $activeModules = Module::whereHas('tenantModules', function ($query) use ($tenant) {
            $query->where('tenant_id', $tenant->id)->where('is_active', true);
        })->get();

        return $coreModules->merge($activeModules)->unique('id');
    }

    /**
     * Branch düzeyinde aktif modülleri döner.
     */
    public function getActiveBranchModules(Branch $branch): Collection
    {
        $tenantModules = $this->getActiveModules($branch->tenant);

        $branchModules = Module::whereHas('branchModules', function ($query) use ($branch) {
            $query->where('branch_id', $branch->id)->where('is_active', true);
        })->get();

        return $tenantModules->merge($branchModules)->unique('id');
    }

    /**
     * Tenant için modül aktifleştirir.
     */
    public function activateForTenant(Tenant $tenant, string $moduleCode, array $config = []): bool
    {
        $module = Module::findByCode($moduleCode);
        if (!$module) {
            return false;
        }

        // Bağımlılık kontrolü
        foreach ($module->getDependencyCodes() as $depCode) {
            if (!$tenant->hasModule($depCode)) {
                return false;
            }
        }

        $tenant->tenantModules()->updateOrCreate(
            ['module_id' => $module->id],
            [
                'is_active'    => true,
                'activated_at' => now(),
                'config'       => $config,
            ]
        );

        return true;
    }

    /**
     * Branch için modül aktifleştirir.
     */
    public function activateForBranch(Branch $branch, string $moduleCode, array $config = []): bool
    {
        $module = Module::findByCode($moduleCode);
        if (!$module || !in_array($module->scope, ['branch', 'both'])) {
            return false;
        }

        $branch->branchModules()->updateOrCreate(
            ['module_id' => $module->id],
            [
                'is_active'    => true,
                'activated_at' => now(),
                'config'       => $config,
            ]
        );

        return true;
    }
}
