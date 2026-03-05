<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        /* ─── Plan Tanımları ─── */

        $starter = Plan::updateOrCreate(
            ['code' => 'starter'],
            [
                'name'          => 'Başlangıç',
                'description'   => 'Tek şube, temel POS özellikleri. Küçük işletmeler için ideal.',
                'price_monthly' => 299.00,
                'price_yearly'  => 2990.00,
                'is_active'     => true,
                'limits'        => [
                    'max_branches'  => 1,
                    'max_users'     => 3,
                    'max_products'  => 500,
                    'max_customers' => 200,
                ],
                'sort_order'    => 1,
            ],
        );

        $business = Plan::updateOrCreate(
            ['code' => 'business'],
            [
                'name'          => 'İşletme',
                'description'   => 'Çoklu şube, donanım ve e-fatura desteği. Büyüyen işletmeler için.',
                'price_monthly' => 599.00,
                'price_yearly'  => 5990.00,
                'is_active'     => true,
                'limits'        => [
                    'max_branches'  => 5,
                    'max_users'     => 15,
                    'max_products'  => 5000,
                    'max_customers' => 2000,
                ],
                'sort_order'    => 2,
            ],
        );

        $enterprise = Plan::updateOrCreate(
            ['code' => 'enterprise'],
            [
                'name'          => 'Kurumsal',
                'description'   => 'Sınırsız şube, tüm modüller, öncelikli destek. Büyük zincirler için.',
                'price_monthly' => 1299.00,
                'price_yearly'  => 12990.00,
                'is_active'     => true,
                'limits'        => [
                    'max_branches'  => null, // sınırsız
                    'max_users'     => null,
                    'max_products'  => null,
                    'max_customers' => null,
                ],
                'sort_order'    => 3,
            ],
        );

        /* ─── Plan-Modül İlişkileri ─── */

        $allModules = Module::all()->keyBy('code');

        // Starter: sadece core_pos
        $starter->modules()->sync([
            $allModules['core_pos']->id => ['included' => true, 'config' => null],
        ]);

        // Business: core_pos + hardware + einvoice + income_expense + staff
        $business->modules()->sync([
            $allModules['core_pos']->id        => ['included' => true,  'config' => null],
            $allModules['hardware']->id         => ['included' => true,  'config' => null],
            $allModules['einvoice']->id         => ['included' => true,  'config' => null],
            $allModules['income_expense']->id   => ['included' => true,  'config' => null],
            $allModules['staff']->id            => ['included' => true,  'config' => null],
        ]);

        // Enterprise: tüm modüller
        $enterpriseModules = [];
        foreach ($allModules as $module) {
            $enterpriseModules[$module->id] = ['included' => true, 'config' => null];
        }
        $enterprise->modules()->sync($enterpriseModules);
    }
}
