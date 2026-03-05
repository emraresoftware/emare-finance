<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BranchModule;
use App\Models\Module;
use App\Models\ModuleAuditLog;
use App\Models\TenantModule;
use App\Services\ModuleService;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function __construct(
        private ModuleService $moduleService,
    ) {}

    /**
     * Modül listesi — tenant düzeyinde aktiflik durumu
     */
    public function index(Request $request)
    {
        $tenant = $request->user()->tenant;
        $modules = Module::orderBy('is_core', 'desc')->orderBy('name')->get();

        // Super admin tenant'sız olabilir — tüm modülleri aktif göster
        if (!$tenant) {
            $tenantModules = [];
            return view('admin.modules.index', compact('modules', 'tenantModules', 'tenant'));
        }

        $tenantModules = TenantModule::where('tenant_id', $tenant->id)
            ->pluck('is_active', 'module_id')
            ->toArray();

        return view('admin.modules.index', compact('modules', 'tenantModules', 'tenant'));
    }

    /**
     * Tenant düzeyinde modül aktif/pasif toggle
     */
    public function toggle(Request $request, Module $module)
    {
        $tenant = $request->user()->tenant;
        $tenantId = $tenant?->id ?? $request->user()->tenant_id;

        if (!$tenantId) {
            return back()->with('error', 'Modül toggle için bir tenant seçili olmalıdır.');
        }

        // Core modüller kapatılamaz
        if ($module->is_core) {
            return back()->with('error', 'Çekirdek modüller devre dışı bırakılamaz.');
        }

        $tenantModule = TenantModule::firstOrCreate(
            ['tenant_id' => $tenantId, 'module_id' => $module->id],
            ['is_active' => false]
        );

        $newStatus = !$tenantModule->is_active;
        $tenantModule->update(['is_active' => $newStatus]);

        // Audit log
        ModuleAuditLog::logAction(
            action: $newStatus ? 'activated' : 'deactivated',
            module: $module,
            tenantId: $tenantId,
        );

        $statusText = $newStatus ? 'aktif edildi' : 'devre dışı bırakıldı';
        return back()->with('success', "{$module->name} modülü başarıyla {$statusText}.");
    }

    /**
     * Şube bazlı modül durumları
     */
    public function branchModules(Request $request, Branch $branch)
    {
        $tenant = $request->user()->tenant;
        $tenantId = $tenant?->id ?? $request->user()->tenant_id;

        // Yetkisiz şubeye erişim engeli
        if ($tenantId && $branch->tenant_id !== $tenantId) {
            abort(403);
        }

        $modules = Module::orderBy('is_core', 'desc')->orderBy('name')->get();

        $branchModules = BranchModule::where('branch_id', $branch->id)
            ->pluck('is_active', 'module_id')
            ->toArray();

        $effectiveTenantId = $tenantId ?? $branch->tenant_id;
        $tenantModules = $effectiveTenantId
            ? TenantModule::where('tenant_id', $effectiveTenantId)
                ->pluck('is_active', 'module_id')
                ->toArray()
            : [];

        return view('admin.modules.branch', compact('modules', 'branchModules', 'tenantModules', 'branch', 'tenant'));
    }

    /**
     * Şube düzeyinde modül toggle
     */
    public function branchToggle(Request $request, Branch $branch, Module $module)
    {
        $tenant = $request->user()->tenant;
        $tenantId = $tenant?->id ?? $request->user()->tenant_id;

        if ($tenantId && $branch->tenant_id !== $tenantId) {
            abort(403);
        }

        if ($module->is_core) {
            return back()->with('error', 'Çekirdek modüller devre dışı bırakılamaz.');
        }

        $effectiveTenantId = $tenantId ?? $branch->tenant_id;

        // Tenant düzeyinde aktif değilse şubede de aktif edilemez
        if ($effectiveTenantId) {
            $tenantModule = TenantModule::where('tenant_id', $effectiveTenantId)
                ->where('module_id', $module->id)
                ->first();

            if (!$tenantModule || !$tenantModule->is_active) {
                return back()->with('error', 'Bu modül tenant düzeyinde aktif değil. Önce tenant düzeyinde aktif edin.');
            }
        }

        $branchModule = BranchModule::firstOrCreate(
            ['branch_id' => $branch->id, 'module_id' => $module->id],
            ['is_active' => false]
        );

        $newStatus = !$branchModule->is_active;
        $branchModule->update(['is_active' => $newStatus]);

        ModuleAuditLog::logAction(
            action: $newStatus ? 'activated' : 'deactivated',
            module: $module,
            tenantId: $effectiveTenantId,
            branchId: $branch->id,
        );

        $statusText = $newStatus ? 'aktif edildi' : 'devre dışı bırakıldı';
        return back()->with('success', "{$module->name} modülü {$branch->name} şubesi için {$statusText}.");
    }
}
