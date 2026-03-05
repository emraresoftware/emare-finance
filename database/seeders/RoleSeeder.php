<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        /* ────────────────────────── İzinler ────────────────────────── */

        $permissions = [
            // Satış
            ['code' => 'sales.view',        'name' => 'Satışları Görüntüle',       'module_code' => 'core_pos', 'group' => 'Satış'],
            ['code' => 'sales.create',      'name' => 'Satış Oluştur',             'module_code' => 'core_pos', 'group' => 'Satış'],
            ['code' => 'sales.cancel',      'name' => 'Satış İptal Et',            'module_code' => 'core_pos', 'group' => 'Satış'],
            ['code' => 'sales.refund',      'name' => 'İade İşlemi',               'module_code' => 'core_pos', 'group' => 'Satış'],
            ['code' => 'sales.discount',    'name' => 'İndirim Uygula',            'module_code' => 'core_pos', 'group' => 'Satış'],

            // Ürün
            ['code' => 'products.view',     'name' => 'Ürünleri Görüntüle',        'module_code' => 'core_pos', 'group' => 'Ürün'],
            ['code' => 'products.create',   'name' => 'Ürün Ekle',                 'module_code' => 'core_pos', 'group' => 'Ürün'],
            ['code' => 'products.edit',     'name' => 'Ürün Düzenle',              'module_code' => 'core_pos', 'group' => 'Ürün'],
            ['code' => 'products.delete',   'name' => 'Ürün Sil',                  'module_code' => 'core_pos', 'group' => 'Ürün'],

            // Müşteri
            ['code' => 'customers.view',    'name' => 'Müşterileri Görüntüle',     'module_code' => 'core_pos', 'group' => 'Müşteri'],
            ['code' => 'customers.create',  'name' => 'Müşteri Ekle',              'module_code' => 'core_pos', 'group' => 'Müşteri'],
            ['code' => 'customers.edit',    'name' => 'Müşteri Düzenle',           'module_code' => 'core_pos', 'group' => 'Müşteri'],
            ['code' => 'customers.delete',  'name' => 'Müşteri Sil',              'module_code' => 'core_pos', 'group' => 'Müşteri'],

            // Stok
            ['code' => 'stock.view',        'name' => 'Stok Görüntüle',            'module_code' => 'core_pos', 'group' => 'Stok'],
            ['code' => 'stock.adjust',      'name' => 'Stok Düzenleme',            'module_code' => 'core_pos', 'group' => 'Stok'],
            ['code' => 'stock.count',       'name' => 'Sayım Yap',                 'module_code' => 'core_pos', 'group' => 'Stok'],
            ['code' => 'stock.transfer',    'name' => 'Stok Transfer',             'module_code' => 'core_pos', 'group' => 'Stok'],

            // Raporlar
            ['code' => 'reports.basic',     'name' => 'Temel Raporlar',            'module_code' => 'core_pos',         'group' => 'Rapor'],
            ['code' => 'reports.advanced',  'name' => 'Gelişmiş Raporlar',         'module_code' => 'advanced_reports', 'group' => 'Rapor'],
            ['code' => 'reports.export',    'name' => 'Rapor Dışa Aktar',          'module_code' => 'advanced_reports', 'group' => 'Rapor'],

            // Gelir-Gider
            ['code' => 'income.view',       'name' => 'Gelir Görüntüle',           'module_code' => 'income_expense', 'group' => 'Gelir-Gider'],
            ['code' => 'income.create',     'name' => 'Gelir Ekle',                'module_code' => 'income_expense', 'group' => 'Gelir-Gider'],
            ['code' => 'expense.view',      'name' => 'Gider Görüntüle',           'module_code' => 'income_expense', 'group' => 'Gelir-Gider'],
            ['code' => 'expense.create',    'name' => 'Gider Ekle',                'module_code' => 'income_expense', 'group' => 'Gelir-Gider'],

            // E-Fatura
            ['code' => 'einvoice.view',     'name' => 'E-Fatura Görüntüle',        'module_code' => 'einvoice', 'group' => 'E-Fatura'],
            ['code' => 'einvoice.create',   'name' => 'E-Fatura Oluştur',          'module_code' => 'einvoice', 'group' => 'E-Fatura'],
            ['code' => 'einvoice.cancel',   'name' => 'E-Fatura İptal',            'module_code' => 'einvoice', 'group' => 'E-Fatura'],

            // Personel
            ['code' => 'staff.view',        'name' => 'Personel Görüntüle',        'module_code' => 'staff', 'group' => 'Personel'],
            ['code' => 'staff.create',      'name' => 'Personel Ekle',             'module_code' => 'staff', 'group' => 'Personel'],
            ['code' => 'staff.edit',        'name' => 'Personel Düzenle',          'module_code' => 'staff', 'group' => 'Personel'],

            // Donanım
            ['code' => 'hardware.view',     'name' => 'Donanım Görüntüle',         'module_code' => 'hardware', 'group' => 'Donanım'],
            ['code' => 'hardware.manage',   'name' => 'Donanım Yönet',             'module_code' => 'hardware', 'group' => 'Donanım'],

            // Şube
            ['code' => 'branches.view',     'name' => 'Şubeleri Görüntüle',        'module_code' => 'core_pos', 'group' => 'Şube'],
            ['code' => 'branches.create',   'name' => 'Şube Ekle',                 'module_code' => 'core_pos', 'group' => 'Şube'],
            ['code' => 'branches.edit',     'name' => 'Şube Düzenle',              'module_code' => 'core_pos', 'group' => 'Şube'],

            // Kullanıcı / Yetki Yönetimi
            ['code' => 'users.view',        'name' => 'Kullanıcıları Görüntüle',   'module_code' => 'core_pos', 'group' => 'Yönetim'],
            ['code' => 'users.create',      'name' => 'Kullanıcı Ekle',            'module_code' => 'core_pos', 'group' => 'Yönetim'],
            ['code' => 'users.edit',        'name' => 'Kullanıcı Düzenle',         'module_code' => 'core_pos', 'group' => 'Yönetim'],
            ['code' => 'roles.manage',      'name' => 'Rol Yönetimi',              'module_code' => 'core_pos', 'group' => 'Yönetim'],
            ['code' => 'modules.manage',    'name' => 'Modül Yönetimi',            'module_code' => 'core_pos', 'group' => 'Yönetim'],
            ['code' => 'settings.manage',   'name' => 'Ayar Yönetimi',             'module_code' => 'core_pos', 'group' => 'Yönetim'],
        ];

        foreach ($permissions as $data) {
            Permission::updateOrCreate(
                ['code' => $data['code']],
                $data,
            );
        }

        /* ────────────────────────── Roller ────────────────────────── */

        // Admin — tüm yetkiler
        $admin = Role::updateOrCreate(
            ['code' => 'admin'],
            [
                'name'        => 'Yönetici',
                'description' => 'Tüm modüllere ve yetkilere tam erişim.',
                'scope'       => 'tenant',
                'is_system'   => true,
            ],
        );
        $admin->permissions()->sync(Permission::all());

        // Müdür — yönetim hariç çoğu yetki
        $manager = Role::updateOrCreate(
            ['code' => 'manager'],
            [
                'name'        => 'Şube Müdürü',
                'description' => 'Şube düzeyinde tüm operasyonel yetkiler.',
                'scope'       => 'branch',
                'is_system'   => true,
            ],
        );
        $manager->permissions()->sync(
            Permission::whereNotIn('code', [
                'roles.manage', 'modules.manage', 'settings.manage',
                'users.create', 'users.edit',
                'branches.create', 'branches.edit',
            ])->pluck('id'),
        );

        // Kasiyer
        $cashier = Role::updateOrCreate(
            ['code' => 'cashier'],
            [
                'name'        => 'Kasiyer',
                'description' => 'Satış işlemleri ve temel stok görüntüleme.',
                'scope'       => 'branch',
                'is_system'   => true,
            ],
        );
        $cashier->permissions()->sync(
            Permission::whereIn('code', [
                'sales.view', 'sales.create',
                'products.view',
                'customers.view', 'customers.create',
                'stock.view',
                'reports.basic',
            ])->pluck('id'),
        );

        // Muhasebe
        $accounting = Role::updateOrCreate(
            ['code' => 'accounting'],
            [
                'name'        => 'Muhasebe',
                'description' => 'Finansal işlemler, e-fatura ve raporlama.',
                'scope'       => 'tenant',
                'is_system'   => true,
            ],
        );
        $accounting->permissions()->sync(
            Permission::whereIn('code', [
                'sales.view',
                'income.view', 'income.create',
                'expense.view', 'expense.create',
                'einvoice.view', 'einvoice.create', 'einvoice.cancel',
                'reports.basic', 'reports.advanced', 'reports.export',
                'customers.view',
            ])->pluck('id'),
        );

        // Depocu
        $warehouse = Role::updateOrCreate(
            ['code' => 'warehouse'],
            [
                'name'        => 'Depo Sorumlusu',
                'description' => 'Stok yönetimi, sayım ve transfer işlemleri.',
                'scope'       => 'branch',
                'is_system'   => true,
            ],
        );
        $warehouse->permissions()->sync(
            Permission::whereIn('code', [
                'products.view',
                'stock.view', 'stock.adjust', 'stock.count', 'stock.transfer',
                'reports.basic',
            ])->pluck('id'),
        );
    }
}
