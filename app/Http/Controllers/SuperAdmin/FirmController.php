<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Module;
use App\Models\Plan;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\TenantModule;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class FirmController extends Controller
{
    /**
     * Süper Admin Dashboard — genel istatistikler
     */
    public function dashboard()
    {
        $stats = [
            'total_tenants'  => Tenant::count(),
            'active_tenants' => Tenant::where('status', 'active')->count(),
            'total_users'    => User::where('is_super_admin', false)->count(),
            'total_branches' => Branch::count(),
            'trial_tenants'  => Tenant::where('status', 'active')
                ->whereNotNull('trial_ends_at')
                ->where('trial_ends_at', '>', now())
                ->count(),
            'suspended_tenants' => Tenant::where('status', 'suspended')->count(),
        ];

        $recentTenants = Tenant::with(['plan', 'users', 'branches'])
            ->latest()
            ->take(5)
            ->get();

        $plans = Plan::withCount('tenants')->where('is_active', true)->get();

        return view('super-admin.dashboard', compact('stats', 'recentTenants', 'plans'));
    }

    /**
     * Firma listesi
     */
    public function index(Request $request)
    {
        $query = Tenant::with(['plan', 'users', 'branches']);

        // Arama
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('billing_email', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        // Durum filtresi
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Plan filtresi
        if ($planId = $request->input('plan_id')) {
            $query->where('plan_id', $planId);
        }

        $tenants = $query->latest()->paginate(15)->withQueryString();
        $plans = Plan::where('is_active', true)->get();

        return view('super-admin.firms.index', compact('tenants', 'plans'));
    }

    /**
     * Firma oluşturma formu
     */
    public function create()
    {
        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();
        $modules = Module::orderBy('sort_order')->get();

        return view('super-admin.firms.create', compact('plans', 'modules'));
    }

    /**
     * Firma kaydet — Tenant + Branch + Admin User + Modüller
     */
    public function store(Request $request)
    {
        $request->validate([
            'firm_name'     => 'required|string|max:255',
            'billing_email' => 'required|email|max:255',
            'plan_id'       => 'nullable|exists:plans,id',
            'trial_days'    => 'nullable|integer|min:0|max:365',
            'industry'      => 'nullable|string|max:100',
            'branch_name'   => 'required|string|max:255',
            'branch_city'   => 'nullable|string|max:100',
            'branch_phone'  => 'nullable|string|max:30',
            'admin_name'    => 'required|string|max:255',
            'admin_email'   => 'required|email|max:255|unique:users,email',
            'admin_password' => 'required|string|min:6|max:100',
            'modules'       => 'nullable|array',
            'modules.*'     => 'exists:modules,id',
        ], [
            'firm_name.required'    => 'Firma adı zorunludur.',
            'billing_email.required' => 'Fatura e-posta adresi zorunludur.',
            'branch_name.required'  => 'Şube adı zorunludur.',
            'admin_name.required'   => 'Yönetici adı zorunludur.',
            'admin_email.required'  => 'Yönetici e-posta adresi zorunludur.',
            'admin_email.unique'    => 'Bu e-posta adresi zaten kullanımda.',
            'admin_password.required' => 'Yönetici şifresi zorunludur.',
            'admin_password.min'    => 'Şifre en az 6 karakter olmalıdır.',
        ]);

        DB::beginTransaction();
        try {
            // 1) Tenant oluştur
            $tenant = Tenant::create([
                'name'          => $request->firm_name,
                'slug'          => Str::slug($request->firm_name),
                'status'        => 'active',
                'plan_id'       => $request->plan_id,
                'trial_ends_at' => $request->trial_days ? now()->addDays((int) $request->trial_days) : null,
                'billing_email' => $request->billing_email,
                'meta'          => [
                    'industry'      => $request->industry,
                    'registered_at' => now()->toISOString(),
                    'created_by'    => 'super_admin',
                ],
            ]);

            // 2) Merkez şube oluştur
            $branch = Branch::create([
                'tenant_id' => $tenant->id,
                'name'      => $request->branch_name,
                'city'      => $request->branch_city,
                'phone'     => $request->branch_phone,
                'is_active' => true,
            ]);

            // 3) Admin rolü bul
            $adminRole = Role::where('code', 'admin')->first();

            // 4) Firma admin kullanıcısını oluştur
            $admin = User::create([
                'name'      => $request->admin_name,
                'email'     => $request->admin_email,
                'password'  => Hash::make($request->admin_password),
                'tenant_id' => $tenant->id,
                'branch_id' => $branch->id,
                'role_id'   => $adminRole?->id,
            ]);

            // 5) UserRole pivot
            if ($adminRole) {
                UserRole::create([
                    'user_id'    => $admin->id,
                    'role_id'    => $adminRole->id,
                    'tenant_id'  => $tenant->id,
                    'branch_id'  => $branch->id,
                    'created_at' => now(),
                ]);
            }

            // 6) Modülleri aktif et
            $moduleIds = $request->input('modules', []);
            // Core modüller her zaman aktif
            $coreModuleIds = Module::where('is_core', true)->pluck('id')->toArray();
            $allModuleIds = array_unique(array_merge($coreModuleIds, $moduleIds));

            foreach ($allModuleIds as $moduleId) {
                TenantModule::create([
                    'tenant_id' => $tenant->id,
                    'module_id' => $moduleId,
                    'is_active' => true,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('super-admin.firms.show', $tenant)
                ->with('success', "Firma \"{$tenant->name}\" başarıyla oluşturuldu!");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Firma oluşturulurken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Firma detayı
     */
    public function show(Tenant $tenant)
    {
        $tenant->load(['plan', 'users.primaryRole', 'branches', 'tenantModules.module']);

        $stats = [
            'user_count'   => $tenant->users()->count(),
            'branch_count' => $tenant->branches()->count(),
            'module_count' => $tenant->tenantModules()->where('is_active', true)->count(),
        ];

        return view('super-admin.firms.show', compact('tenant', 'stats'));
    }

    /**
     * Firma düzenleme formu
     */
    public function edit(Tenant $tenant)
    {
        $tenant->load(['tenantModules']);
        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();
        $modules = Module::orderBy('sort_order')->get();
        $activeModuleIds = $tenant->tenantModules()->where('is_active', true)->pluck('module_id')->toArray();

        return view('super-admin.firms.edit', compact('tenant', 'plans', 'modules', 'activeModuleIds'));
    }

    /**
     * Firma güncelle
     */
    public function update(Request $request, Tenant $tenant)
    {
        $request->validate([
            'firm_name'     => 'required|string|max:255',
            'billing_email' => 'required|email|max:255',
            'plan_id'       => 'nullable|exists:plans,id',
            'status'        => 'required|in:active,suspended,cancelled',
            'trial_days'    => 'nullable|integer|min:0|max:365',
            'modules'       => 'nullable|array',
            'modules.*'     => 'exists:modules,id',
        ]);

        DB::beginTransaction();
        try {
            $tenant->update([
                'name'          => $request->firm_name,
                'slug'          => Str::slug($request->firm_name),
                'status'        => $request->status,
                'plan_id'       => $request->plan_id,
                'trial_ends_at' => $request->trial_days ? now()->addDays($request->trial_days) : $tenant->trial_ends_at,
                'billing_email' => $request->billing_email,
            ]);

            // Modülleri güncelle
            $selectedModuleIds = $request->input('modules', []);
            $coreModuleIds = Module::where('is_core', true)->pluck('id')->toArray();
            $allModuleIds = array_unique(array_merge($coreModuleIds, $selectedModuleIds));
            $allModules = Module::all();

            foreach ($allModules as $module) {
                TenantModule::updateOrCreate(
                    ['tenant_id' => $tenant->id, 'module_id' => $module->id],
                    ['is_active' => in_array($module->id, $allModuleIds)]
                );
            }

            DB::commit();

            return redirect()
                ->route('super-admin.firms.show', $tenant)
                ->with('success', 'Firma bilgileri güncellendi.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Güncelleme sırasında hata: ' . $e->getMessage());
        }
    }

    /**
     * Firma durumunu değiştir (askıya al / aktif et)
     */
    public function toggleStatus(Tenant $tenant)
    {
        $newStatus = $tenant->status === 'active' ? 'suspended' : 'active';
        $tenant->update(['status' => $newStatus]);

        $message = $newStatus === 'active'
            ? "\"{$tenant->name}\" aktif edildi."
            : "\"{$tenant->name}\" askıya alındı.";

        return back()->with('success', $message);
    }

    /**
     * Firma sil (soft)
     */
    public function destroy(Tenant $tenant)
    {
        $name = $tenant->name;

        // Firmayı iptal et (silmek yerine deaktif)
        $tenant->update(['status' => 'cancelled']);

        // Tüm kullanıcıları deaktif et
        $tenant->users()->update(['is_super_admin' => false]);

        return redirect()
            ->route('super-admin.firms.index')
            ->with('success', "\"{$name}\" firması iptal edildi.");
    }

    /**
     * Firmaya yeni kullanıcı ekle (AJAX/modal için)
     */
    public function addUser(Request $request, Tenant $tenant)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|max:255|unique:users,email',
            'password'  => 'required|string|min:6',
            'role_id'   => 'required|exists:roles,id',
            'branch_id' => 'required|exists:branches,id',
        ]);

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'tenant_id' => $tenant->id,
            'branch_id' => $request->branch_id,
            'role_id'   => $request->role_id,
        ]);

        $role = Role::find($request->role_id);
        if ($role) {
            UserRole::create([
                'user_id'    => $user->id,
                'role_id'    => $role->id,
                'tenant_id'  => $tenant->id,
                'branch_id'  => $request->branch_id,
                'created_at' => now(),
            ]);
        }

        return back()->with('success', "Kullanıcı \"{$user->name}\" başarıyla eklendi.");
    }

    /**
     * Firmaya yeni şube ekle
     */
    public function addBranch(Request $request, Tenant $tenant)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'city'  => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:30',
        ]);

        $branch = Branch::create([
            'tenant_id' => $tenant->id,
            'name'      => $request->name,
            'city'      => $request->city,
            'phone'     => $request->phone,
            'is_active' => true,
        ]);

        return back()->with('success', "Şube \"{$branch->name}\" başarıyla eklendi.");
    }
}
