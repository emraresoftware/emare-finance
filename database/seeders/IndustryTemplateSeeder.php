<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Sektörel şablonlar seed eder.
 * Her şablon, sektöre özel varsayılan kategoriler, ürünler ve ayarlar içerir.
 */
class IndustryTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = config('industry.templates', []);

        foreach ($templates as $code => $template) {
            $this->seedTemplate($code, $template);
        }
    }

    private function seedTemplate(string $code, array $template): void
    {
        // Sektörel kategori ve ürünlerin seed edilmesi
        // Gerçek uygulama sırasında tenant oluşturulurken kullanılır
        // Burada sadece config/industry.php'deki şablonları doğruluyoruz

        $requiredKeys = ['name', 'description', 'modules', 'default_categories'];
        foreach ($requiredKeys as $key) {
            if (!isset($template[$key])) {
                $this->command->warn("Sektörel şablon '{$code}' içinde '{$key}' anahtarı eksik.");
            }
        }

        $this->command->info("Sektörel şablon yüklendi: {$template['name']} ({$code})");
    }
}
