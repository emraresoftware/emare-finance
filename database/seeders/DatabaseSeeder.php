<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Modüller (diğer seeder'lar buna bağlı)
        $this->call(ModuleSeeder::class);

        // 2. Planlar & plan-modül ilişkileri
        $this->call(PlanSeeder::class);

        // 3. Roller & izinler
        $this->call(RoleSeeder::class);

        // 4. Donanım sürücüleri
        $this->call(HardwareDriverSeeder::class);

        // 5. Vergi oranları
        $this->call(TaxRateSeeder::class);

        // 6. Sektörel şablonlar
        $this->call(IndustryTemplateSeeder::class);

        // 7. Test kullanıcısı
        User::factory()->create([
            'name'  => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
