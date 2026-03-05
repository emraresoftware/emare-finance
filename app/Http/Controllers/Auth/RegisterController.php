<?php

namespace App\Http\Controllers\Auth;

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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    /**
     * Kayıt formunu göster
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Kayıt işlemi — Tenant + Branch + User + Modül aktivasyonu
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'business_name' => ['required', 'string', 'max:255'],
            'email'         => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'      => ['required', 'confirmed', Password::min(8)],
            'industry'      => ['nullable', 'string', 'in:market,cafe,boutique,wholesale,service'],
        ], [
            'name.required'          => 'Ad soyad gereklidir.',
            'business_name.required' => 'İşletme adı gereklidir.',
            'email.required'         => 'E-posta adresi gereklidir.',
            'email.email'            => 'Geçerli bir e-posta adresi girin.',
            'email.unique'           => 'Bu e-posta adresi zaten kullanılıyor.',
            'password.required'      => 'Şifre gereklidir.',
            'password.confirmed'     => 'Şifreler eşleşmiyor.',
            'password.min'           => 'Şifre en az 8 karakter olmalıdır.',
        ]);

        $user = DB::transaction(function () use ($request) {
            // 1) Starter planı bul (varsayılan deneme paketi)
            $starterPlan = Plan::where('code', 'starter')->first();

            // 2) Tenant oluştur
            $tenant = Tenant::create([
                'name'          => $request->business_name,
                'status'        => 'active',
                'plan_id'       => $starterPlan?->id,
                'trial_ends_at' => now()->addDays(14), // 14 gün deneme
                'billing_email' => $request->email,
                'meta'          => [
                    'industry'      => $request->industry,
                    'registered_at' => now()->toISOString(),
                ],
            ]);

            // 3) Varsayılan şube oluştur
            $branch = Branch::create([
                'tenant_id' => $tenant->id,
                'name'      => 'Merkez Şube',
                'is_active' => true,
            ]);

            // 4) Kullanıcı oluştur (admin rolü ile)
            $adminRole = Role::where('code', 'admin')->first();

            $user = User::create([
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'tenant_id' => $tenant->id,
                'branch_id' => $branch->id,
                'role_id'   => $adminRole?->id,
            ]);

            // 5) user_roles pivot kaydı
            if ($adminRole) {
                UserRole::create([
                    'user_id'    => $user->id,
                    'role_id'    => $adminRole->id,
                    'tenant_id'  => $tenant->id,
                    'branch_id'  => $branch->id,
                    'created_at' => now(),
                ]);
            }

            // 6) Core modülleri tenant için aktif et
            $coreModules = Module::where('is_core', true)->get();
            foreach ($coreModules as $module) {
                TenantModule::create([
                    'tenant_id' => $tenant->id,
                    'module_id' => $module->id,
                    'is_active' => true,
                ]);
            }

            // 7) Starter plan modüllerini de aktif et
            if ($starterPlan) {
                $planModuleIds = $starterPlan->modules()->pluck('modules.id');
                foreach ($planModuleIds as $moduleId) {
                    TenantModule::firstOrCreate([
                        'tenant_id' => $tenant->id,
                        'module_id' => $moduleId,
                    ], [
                        'is_active' => true,
                    ]);
                }
            }

            return $user;
        });

        // Otomatik giriş
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Hoş geldiniz! Hesabınız başarıyla oluşturuldu. 14 günlük deneme süreniz başladı.');
    }
}
