<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Sale;
use App\Models\SmsAutomationConfig;
use App\Models\SmsAutomationQueue;
use App\Models\SmsTemplate;
use App\Services\SmsService;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ProcessSmsAutomations extends Command
{
    protected $signature = 'sms:process-automations {--dry-run : Sadece simüle et, SMS gönderme}';
    protected $description = 'Zamanlanmış SMS otomasyonlarını işle (doğum günü, pasif müşteri, kargo vb.)';

    protected SmsService $smsService;
    protected int $processed = 0;
    protected int $queued = 0;
    protected int $sent = 0;
    protected int $failed = 0;

    public function handle(): int
    {
        $this->smsService = new SmsService();
        $isDryRun = $this->option('dry-run');

        $this->info('═══════════════════════════════════════════════');
        $this->info('  SMS Otomasyon İşlemci - ' . now()->format('d.m.Y H:i'));
        $this->info('═══════════════════════════════════════════════');

        if ($isDryRun) {
            $this->warn('  ⚠ DRY-RUN modu aktif — SMS gönderilmeyecek');
        }

        // 1) Doğum günü otomasyonu
        $this->processBirthday($isDryRun);

        // 2) Pasif müşteri hatırlatma
        $this->processInactivity($isDryRun);

        // 3) Ödeme hatırlatma
        $this->processPaymentReminder($isDryRun);

        // 4) Bekleyen kuyruk mesajlarını gönder
        $this->processQueue($isDryRun);

        // Sonuç raporu
        $this->newLine();
        $this->info('═══════════════════════════════════════════════');
        $this->info("  Sonuç: İşlenen={$this->processed} | Kuyruğa Eklenen={$this->queued} | Gönderilen={$this->sent} | Başarısız={$this->failed}");
        $this->info('═══════════════════════════════════════════════');

        return self::SUCCESS;
    }

    /**
     * Doğum günü SMS'i
     */
    protected function processBirthday(bool $isDryRun): void
    {
        $this->newLine();
        $this->info('🎂 Doğum Günü Otomasyonu');

        $config = SmsAutomationConfig::where('automation_type', 'birthday')
            ->where('is_active', true)
            ->first();

        if (!$config) {
            $this->line('   ⏭ Pasif veya tanımsız — atlanıyor');
            return;
        }

        $template = SmsTemplate::find($config->template_id);
        if (!$template) {
            $this->error('   ❌ Şablon bulunamadı (ID: ' . $config->template_id . ')');
            return;
        }

        $today = now();
        $daysBefore = $config->days_before ?? 0;
        $targetDate = $today->copy()->addDays($daysBefore);

        // Doğum günü bugüne (veya days_before gün sonraya) denk gelen müşteriler
        $customers = Customer::whereNotNull('birth_date')
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->where('is_active', true)
            ->whereRaw('MONTH(birth_date) = ? AND DAY(birth_date) = ?', [
                $targetDate->month,
                $targetDate->day,
            ])
            ->get();

        $this->line("   📊 Hedef tarih: {$targetDate->format('d.m')} — {$customers->count()} müşteri bulundu");

        foreach ($customers as $customer) {
            $this->processed++;

            // Bugün zaten gönderildi mi?
            $alreadySent = SmsAutomationQueue::where('customer_id', $customer->id)
                ->where('trigger_event', 'birthday')
                ->whereDate('scheduled_at', $today->toDateString())
                ->exists();

            if ($alreadySent) {
                $this->line("   ⏭ {$customer->name} — zaten gönderildi");
                continue;
            }

            $variables = $this->buildCustomerVariables($customer);
            $content = $template->render($variables);

            $sendTime = $this->getSendTime($config);

            if ($isDryRun) {
                $this->line("   📱 [DRY] {$customer->name} ({$customer->phone}) → \"{$content}\"");
                $this->queued++;
                continue;
            }

            SmsAutomationQueue::create([
                'tenant_id'     => $config->tenant_id,
                'customer_id'   => $customer->id,
                'template_id'   => $template->id,
                'phone'         => $customer->phone,
                'content'       => $content,
                'trigger_event' => 'birthday',
                'variables'     => $variables,
                'scheduled_at'  => $sendTime,
                'status'        => 'pending',
                'meta'          => ['automation_type' => 'birthday', 'config_id' => $config->id],
            ]);

            $this->queued++;
            $this->line("   📱 {$customer->name} kuyruğa eklendi — gönderim: {$sendTime->format('H:i')}");
        }

        $config->update(['last_run_at' => now()]);
    }

    /**
     * Pasif müşteri hatırlatma
     */
    protected function processInactivity(bool $isDryRun): void
    {
        $this->newLine();
        $this->info('⏰ Pasif Müşteri Otomasyonu');

        $config = SmsAutomationConfig::where('automation_type', 'inactivity')
            ->where('is_active', true)
            ->first();

        if (!$config) {
            $this->line('   ⏭ Pasif veya tanımsız — atlanıyor');
            return;
        }

        $template = SmsTemplate::find($config->template_id);
        if (!$template) {
            $this->error('   ❌ Şablon bulunamadı');
            return;
        }

        $inactiveDays = $config->inactive_days ?? 60;
        $cutoffDate = now()->subDays($inactiveDays);

        // Son X gün alışveriş yapmayan ama daha önce en az 1 alışverişi olan müşteriler
        $customers = Customer::whereNotNull('phone')
            ->where('phone', '!=', '')
            ->where('is_active', true)
            ->whereHas('sales', function ($q) use ($cutoffDate) {
                $q->where('sale_date', '<', $cutoffDate);
            })
            ->whereDoesntHave('sales', function ($q) use ($cutoffDate) {
                $q->where('sale_date', '>=', $cutoffDate);
            })
            ->get();

        $this->line("   📊 {$inactiveDays} gün pasif — {$customers->count()} müşteri bulundu");

        foreach ($customers as $customer) {
            $this->processed++;

            // Son 30 günde bu otomasyondan gönderildi mi? (spam engeli)
            $recentlySent = SmsAutomationQueue::where('customer_id', $customer->id)
                ->where('trigger_event', 'inactivity')
                ->where('created_at', '>=', now()->subDays(30))
                ->exists();

            if ($recentlySent) {
                continue;
            }

            $variables = $this->buildCustomerVariables($customer);
            $content = $template->render($variables);
            $sendTime = $this->getSendTime($config);

            if ($isDryRun) {
                $this->line("   📱 [DRY] {$customer->name} ({$customer->phone})");
                $this->queued++;
                continue;
            }

            SmsAutomationQueue::create([
                'tenant_id'     => $config->tenant_id,
                'customer_id'   => $customer->id,
                'template_id'   => $template->id,
                'phone'         => $customer->phone,
                'content'       => $content,
                'trigger_event' => 'inactivity',
                'variables'     => $variables,
                'scheduled_at'  => $sendTime,
                'status'        => 'pending',
                'meta'          => ['automation_type' => 'inactivity', 'config_id' => $config->id, 'inactive_days' => $inactiveDays],
            ]);

            $this->queued++;
        }

        $config->update(['last_run_at' => now()]);
    }

    /**
     * Ödeme hatırlatma
     */
    protected function processPaymentReminder(bool $isDryRun): void
    {
        $this->newLine();
        $this->info('💰 Ödeme Hatırlatma Otomasyonu');

        $config = SmsAutomationConfig::where('automation_type', 'payment_reminder')
            ->where('is_active', true)
            ->first();

        if (!$config) {
            $this->line('   ⏭ Pasif veya tanımsız — atlanıyor');
            return;
        }

        $template = SmsTemplate::find($config->template_id);
        if (!$template) {
            $this->error('   ❌ Şablon bulunamadı');
            return;
        }

        // Bakiyesi negatif (borçlu) müşteriler
        $customers = Customer::whereNotNull('phone')
            ->where('phone', '!=', '')
            ->where('is_active', true)
            ->where('balance', '<', 0)
            ->get();

        $this->line("   📊 Borçlu müşteri sayısı: {$customers->count()}");

        foreach ($customers as $customer) {
            $this->processed++;

            // Son 7 günde hatırlatma gönderildi mi?
            $recentlySent = SmsAutomationQueue::where('customer_id', $customer->id)
                ->where('trigger_event', 'payment_reminder')
                ->where('created_at', '>=', now()->subDays(7))
                ->exists();

            if ($recentlySent) {
                continue;
            }

            $variables = $this->buildCustomerVariables($customer);
            $variables['tutar'] = '₺' . number_format(abs($customer->balance), 2, ',', '.');
            $variables['odeme_tarihi'] = now()->addDays($config->days_after ?? 7)->format('d.m.Y');
            $content = $template->render($variables);
            $sendTime = $this->getSendTime($config);

            if ($isDryRun) {
                $this->line("   📱 [DRY] {$customer->name} — Borç: {$variables['tutar']}");
                $this->queued++;
                continue;
            }

            SmsAutomationQueue::create([
                'tenant_id'     => $config->tenant_id,
                'customer_id'   => $customer->id,
                'template_id'   => $template->id,
                'phone'         => $customer->phone,
                'content'       => $content,
                'trigger_event' => 'payment_reminder',
                'variables'     => $variables,
                'scheduled_at'  => $sendTime,
                'status'        => 'pending',
                'meta'          => ['automation_type' => 'payment_reminder', 'config_id' => $config->id, 'balance' => $customer->balance],
            ]);

            $this->queued++;
        }

        $config->update(['last_run_at' => now()]);
    }

    /**
     * Bekleyen kuyruk mesajlarını gönder
     */
    protected function processQueue(bool $isDryRun): void
    {
        $this->newLine();
        $this->info('📤 Kuyruk İşleniyor');

        $pendingMessages = SmsAutomationQueue::getReadyToSend();
        $this->line("   📊 Bekleyen mesaj: {$pendingMessages->count()}");

        foreach ($pendingMessages as $queueItem) {
            if ($isDryRun) {
                $this->line("   📱 [DRY] {$queueItem->phone} → \"{$queueItem->content}\"");
                $this->sent++;
                continue;
            }

            try {
                $log = $this->smsService->send($queueItem->phone, $queueItem->content, [
                    'trigger_event' => $queueItem->trigger_event,
                    'customer_id'   => $queueItem->customer_id,
                    'template_id'   => $queueItem->template_id,
                    'scenario_id'   => $queueItem->scenario_id,
                ]);

                if ($log && in_array($log->status, ['sent', 'delivered'])) {
                    $queueItem->update([
                        'status'  => 'sent',
                        'sent_at' => now(),
                    ]);
                    $this->sent++;

                    // Config'teki sent_count güncelle
                    $configId = $queueItem->meta['config_id'] ?? null;
                    if ($configId) {
                        SmsAutomationConfig::where('id', $configId)->increment('sent_count');
                    }
                } else {
                    $queueItem->update([
                        'status'        => 'failed',
                        'error_message' => $log?->error_message ?? 'Bilinmeyen hata',
                    ]);
                    $this->failed++;
                }
            } catch (\Exception $e) {
                $queueItem->update([
                    'status'        => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
                $this->failed++;
                Log::error("SMS otomasyon hatası: {$e->getMessage()}", [
                    'queue_id' => $queueItem->id,
                    'phone'    => $queueItem->phone,
                ]);
            }
        }
    }

    /**
     * Müşteri değişkenlerini hazırla
     */
    protected function buildCustomerVariables(Customer $customer): array
    {
        return [
            'musteri_adi'  => $customer->name ?? '',
            'telefon'      => $customer->phone ?? '',
            'firma_adi'    => config('app.name', 'Emare Finance'),
            'tarih'        => now()->format('d.m.Y'),
            'yil'          => now()->format('Y'),
            'bakiye'       => '₺' . number_format(abs($customer->balance ?? 0), 2, ',', '.'),
        ];
    }

    /**
     * Otomasyon config'inden gönderim zamanını hesapla
     */
    protected function getSendTime(SmsAutomationConfig $config): Carbon
    {
        $sendTime = $config->send_time;

        if ($sendTime) {
            $hour = (int) Carbon::parse($sendTime)->format('H');
            $minute = (int) Carbon::parse($sendTime)->format('i');

            $scheduled = now()->setTime($hour, $minute, 0);

            // Eğer zaman geçtiyse, hemen gönder
            if ($scheduled->isPast()) {
                return now();
            }

            return $scheduled;
        }

        return now();
    }
}
