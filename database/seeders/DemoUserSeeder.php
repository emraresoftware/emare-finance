<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Module;
use App\Models\Plan;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\TenantModule;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    /**
     * Demo tenant, şube ve admin kullanıcı oluştur.
     */
    public function run(): void
    {
        // 1) Starter plan
        $plan = Plan::where('code', 'starter')->first();

        // 2) Tenant oluştur
        $tenant = Tenant::firstOrCreate(
            ['billing_email' => 'demo@emarefinance.com'],
            [
                'name'          => 'Demo İşletme',
                'status'        => 'active',
                'plan_id'       => $plan?->id,
                'trial_ends_at' => now()->addDays(14),
                'meta'          => ['industry' => 'market', 'registered_at' => now()->toISOString()],
            ]
        );

        // 3) Mevcut şubeyi tenant'a bağla veya yeni oluştur
        $branch = Branch::first();
        if ($branch) {
            $branch->update(['tenant_id' => $tenant->id, 'is_active' => true]);
        } else {
            $branch = Branch::create([
                'tenant_id' => $tenant->id,
                'name'      => 'Merkez Şube',
                'is_active' => true,
            ]);
        }

        // 4) Admin rolü
        $adminRole = Role::where('code', 'admin')->first();

        // 5) Demo admin kullanıcı
        $admin = User::firstOrCreate(
            ['email' => 'admin@emarefinance.com'],
            [
                'name'           => 'Admin Kullanıcı',
                'password'       => Hash::make('password'),
                'tenant_id'      => $tenant->id,
                'branch_id'      => $branch->id,
                'role_id'        => $adminRole?->id,
                'is_super_admin' => true,
            ]
        );

        // Mevcut admin'i super admin yap (eğer zaten varsa)
        if (!$admin->is_super_admin) {
            $admin->update(['is_super_admin' => true]);
        }

        // user_roles pivot
        if ($adminRole) {
            UserRole::firstOrCreate([
                'user_id'   => $admin->id,
                'role_id'   => $adminRole->id,
                'tenant_id' => $tenant->id,
                'branch_id' => $branch->id,
            ], [
                'created_at' => now(),
            ]);
        }

        // 6) Tüm modülleri tenant için aktif et
        $modules = Module::all();
        foreach ($modules as $module) {
            TenantModule::firstOrCreate(
                ['tenant_id' => $tenant->id, 'module_id' => $module->id],
                ['is_active' => true]
            );
        }

        // 7) Demo kasiyer kullanıcı
        $cashierRole = Role::where('code', 'cashier')->first();
        $cashier = User::firstOrCreate(
            ['email' => 'kasiyer@emarefinance.com'],
            [
                'name'      => 'Kasiyer Kullanıcı',
                'password'  => Hash::make('password'),
                'tenant_id' => $tenant->id,
                'branch_id' => $branch->id,
                'role_id'   => $cashierRole?->id,
            ]
        );

        if ($cashierRole) {
            UserRole::firstOrCreate([
                'user_id'   => $cashier->id,
                'role_id'   => $cashierRole->id,
                'tenant_id' => $tenant->id,
                'branch_id' => $branch->id,
            ], [
                'created_at' => now(),
            ]);
        }

        $this->command->info('✅ Demo kullanıcılar oluşturuldu:');
        $this->command->info('   Admin  → admin@emarefinance.com / password');
        $this->command->info('   Kasiyer → kasiyer@emarefinance.com / password');
    }
}
