<?php

namespace App\Console\Commands;

use App\Services\AccountingService;
use App\Models\AccountPlan;
use Illuminate\Console\Command;

class AccountingSeedSamples extends Command
{
    protected $signature   = 'accounting:seed-samples {--branch_id=1} {--user_id=1} {--force : Mevcut örnek fişleri sil ve yeniden oluştur}';
    protected $description  = 'Muhasebe modülü için örnek yevmiye fişleri oluşturur';

    public function handle(): int
    {
        if (!AccountPlan::exists()) {
            $this->error('Hesap Planı bulunamadı. Önce çalıştırın: php artisan db:seed --class=AccountPlanSeeder');
            return 1;
        }

        if ($this->option('force')) {
            \App\Models\JournalEntry::where('description', 'like', '%Örnek%')
                ->orWhere('description', 'like', '%Açılış Fişi%')
                ->orWhere('description', 'like', '%Örnek Satış%')
                ->delete();
            $this->info('Eski örnek fişler silindi.');
        }

        $this->info('Örnek yevmiye fişleri oluşturuluyor...');

        $entries = AccountingService::createSampleEntries(
            (int) $this->option('branch_id'),
            (int) $this->option('user_id')
        );

        $this->info(count($entries) . ' örnek fiş oluşturuldu:');
        foreach ($entries as $e) {
            $this->line("  ✓ {$e->entry_no} — {$e->description} ({$e->date->format('d.m.Y')})");
        }

        return 0;
    }
}
