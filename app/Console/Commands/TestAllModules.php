<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestAllModules extends Command
{
    protected $signature = 'test:all-modules';
    protected $description = 'Test all modules for errors';

    private $errors = [];
    private $successes = [];

    public function handle()
    {
        $user = \App\Models\User::where('email', 'emre@emareas.com')->first();
        if (!$user) {
            $this->error('User not found!');
            return 1;
        }

        Auth::login($user);
        $this->info("User: {$user->name} (ID: {$user->id}, Super Admin: " . ($user->is_super_admin ? 'Yes' : 'No') . ")");
        $this->info("   tenant_id: " . ($user->tenant_id ?? 'NULL') . ", branch_id: " . ($user->branch_id ?? 'NULL'));

        // Table counts
        $this->newLine();
        $this->info("=== TABLO KAYIT SAYILARI ===");
        $tables = [
            'Sales' => \App\Models\Sale::class,
            'SaleItems' => \App\Models\SaleItem::class,
            'Products' => \App\Models\Product::class,
            'Categories' => \App\Models\Category::class,
            'Customers' => \App\Models\Customer::class,
            'Branches' => \App\Models\Branch::class,
            'Staff' => \App\Models\Staff::class,
            'Expenses' => \App\Models\Expense::class,
            'Incomes' => \App\Models\Income::class,
            'PurchaseInvoices' => \App\Models\PurchaseInvoice::class,
            'PurchaseInvoiceItems' => \App\Models\PurchaseInvoiceItem::class,
            'StockMovements' => \App\Models\StockMovement::class,
            'AccountTransactions' => \App\Models\AccountTransaction::class,
            'StaffMotions' => \App\Models\StaffMotion::class,
            'Tasks' => \App\Models\Task::class,
            'StockCounts' => \App\Models\StockCount::class,
            'StockCountItems' => \App\Models\StockCountItem::class,
            'PaymentTypes' => \App\Models\PaymentType::class,
        ];

        foreach ($tables as $name => $model) {
            try {
                $count = $model::count();
                $this->line("  {$name}: {$count}");
            } catch (\Exception $e) {
                $this->error("  {$name}: " . $e->getMessage());
            }
        }

        // HTTP tests
        $this->newLine();
        $this->info("=== HTTP ENDPOINT TESTLERi ===");

        $kernel = app(\Illuminate\Contracts\Http\Kernel::class);

        $endpoints = [
            ['GET', '/panel', 'Dashboard'],
            ['GET', '/satislar', 'Satislar Listesi'],
            ['GET', '/urunler', 'Urunler Listesi'],
            ['GET', '/urunler/ekle', 'Urun Olustur Form'],
            ['GET', '/urunler/gruplar', 'Urun Gruplari'],
            ['GET', '/urunler/alt-urunler', 'Alt Urunler'],
            ['GET', '/urunler/varyantlar', 'Varyantlar'],
            ['GET', '/urunler/iadeler', 'Iadeler'],
            ['GET', '/urunler/iade-talepleri', 'Iade Talepleri'],
            ['GET', '/urunler/etiket', 'Etiketler'],
            ['GET', '/urunler/etiket-tasarla', 'Etiket Tasarla'],
            ['GET', '/urunler/terazi-barkod', 'Terazi Barkod'],
            ['GET', '/cariler', 'Cariler Listesi'],
            ['GET', '/cariler/ekle', 'Cari Ekle Formu'],
            ['GET', '/firmalar', 'Firmalar Listesi'],
            ['GET', '/alis-faturalari', 'Alis Faturalari'],
            ['GET', '/stok/hareketler', 'Stok Hareketleri'],
            ['GET', '/stok/sayim', 'Stok Sayimlari'],
            ['GET', '/personeller', 'Personel Listesi'],
            ['GET', '/personeller/hareketler', 'Personel Hareketleri'],
            ['GET', '/gelir-gider/gelirler', 'Gelirler'],
            ['GET', '/gelir-gider/giderler', 'Giderler'],
            ['GET', '/gelir-gider/turler', 'Gelir/Gider Turleri'],
            ['GET', '/raporlar', 'Raporlar Ana Sayfa'],
            ['GET', '/raporlar/gunluk', 'Gunluk Rapor'],
            ['GET', '/raporlar/tarihsel', 'Tarihsel Rapor'],
            ['GET', '/raporlar/urunsel', 'Urunsel Rapor'],
            ['GET', '/raporlar/grupsal', 'Grupsal Rapor'],
            ['GET', '/raporlar/satislar', 'Satislar Raporu'],
            ['GET', '/raporlar/kar', 'Kar Raporu'],
            ['GET', '/raporlar/stok-hareket', 'Stok Hareket Raporu'],
            ['GET', '/raporlar/personel-hareket', 'Personel Hareket Raporu'],
            ['GET', '/raporlar/korelasyon', 'Korelasyon Raporu'],
            ['GET', '/gorevler', 'Gorevler'],
            ['GET', '/odeme-tipleri', 'Odeme Tipleri'],
            ['GET', '/e-faturalar', 'E-Faturalar'],
            ['GET', '/e-faturalar/olustur', 'E-Fatura Olustur'],
            ['GET', '/e-faturalar/giden', 'Giden E-Faturalar'],
            ['GET', '/e-faturalar/gelen', 'Gelen E-Faturalar'],
            ['GET', '/e-faturalar/ayarlar', 'E-Fatura Ayarlar'],
            ['GET', '/vergi-oranlari', 'Vergi Oranlari'],
            ['GET', '/hizmet-kategorileri', 'Hizmet Kategorileri'],
            ['GET', '/tekrarlayan-faturalar', 'Tekrarlayan Faturalar'],
            ['GET', '/donanim', 'Donanim'],
            ['GET', '/entegrasyonlar', 'Entegrasyonlar'],
            ['GET', '/admin/moduller', 'Admin Moduller'],
            ['GET', '/admin/roller', 'Admin Roller'],
            ['GET', '/admin/kullanicilar', 'Admin Kullanicilar'],
            ['GET', '/admin/kullanicilar/ekle', 'Admin Kullanici Ekle'],
            ['GET', '/admin/entegrasyon-basvurulari', 'Entegrasyon Basvurulari'],
            ['GET', '/pazarlama', 'Pazarlama Dashboard'],
            ['GET', '/pazarlama/teklifler', 'Teklifler'],
            ['GET', '/pazarlama/teklifler/olustur', 'Teklif Olustur'],
            ['GET', '/pazarlama/kampanyalar', 'Kampanyalar'],
            ['GET', '/pazarlama/kampanyalar/olustur', 'Kampanya Olustur'],
            ['GET', '/pazarlama/segmentler', 'Segmentler'],
            ['GET', '/pazarlama/mesajlar', 'Mesajlar'],
            ['GET', '/pazarlama/mesajlar/olustur', 'Mesaj Olustur'],
            ['GET', '/pazarlama/sadakat', 'Sadakat'],
            ['GET', '/sms', 'SMS Dashboard'],
            ['GET', '/sms/ayarlar', 'SMS Ayarlar'],
            ['GET', '/sms/sablonlar', 'SMS Sablonlar'],
            ['GET', '/sms/sablonlar/olustur', 'SMS Sablon Olustur'],
            ['GET', '/sms/senaryolar', 'SMS Senaryolar'],
            ['GET', '/sms/senaryolar/olustur', 'SMS Senaryo Olustur'],
            ['GET', '/sms/loglar', 'SMS Loglar'],
            ['GET', '/sms/kara-liste', 'SMS Kara Liste'],
            ['GET', '/sms/gonder', 'SMS Gonder'],
            ['GET', '/dijital-ekran', 'Dijital Ekran'],
            ['GET', '/ekranlar', 'Ekranlar Menu'],
            ['GET', '/ekranlar/pos', 'POS Ekrani'],
            ['GET', '/ekranlar/siparis', 'Siparis Ekrani'],
            ['GET', '/ekranlar/terminal', 'Terminal Ekrani'],
            ['GET', '/mobil', 'Mobil Index'],
            ['GET', '/mobil/kamera-ekle', 'Mobil Kamera Ekle'],
            ['GET', '/mobil/barkod-tara', 'Mobil Barkod Tara'],
            ['GET', '/mobil/hizli-siparis', 'Mobil Hizli Siparis'],
            ['GET', '/sohbet', 'Sohbet/AI'],
            ['GET', '/super-admin', 'Super Admin Dashboard'],
            ['GET', '/super-admin/firmalar', 'Super Admin Firmalar'],
            ['GET', '/super-admin/firmalar/olustur', 'Super Admin Firma Olustur'],
        ];

        foreach ($endpoints as [$method, $uri, $name]) {
            $this->testEndpoint($kernel, $method, $uri, $name, $user);
        }

        // Test detail pages
        $this->newLine();
        $this->info("=== DETAY SAYFA TESTLERi ===");

        $sale = \App\Models\Sale::first();
        if ($sale) {
            $this->testEndpoint($kernel, 'GET', "/satislar/{$sale->id}", "Satis Detay #{$sale->id}", $user);
        }

        $product = \App\Models\Product::first();
        if ($product) {
            $this->testEndpoint($kernel, 'GET', "/urunler/{$product->id}", "Urun Detay #{$product->id}", $user);
            $this->testEndpoint($kernel, 'GET', "/urunler/{$product->id}/duzenle", "Urun Duzenle #{$product->id}", $user);
        }

        $customer = \App\Models\Customer::first();
        if ($customer) {
            $this->testEndpoint($kernel, 'GET', "/cariler/{$customer->id}", "Cari Detay #{$customer->id}", $user);
            $this->testEndpoint($kernel, 'GET', "/cariler/{$customer->id}/duzenle", "Cari Duzenle #{$customer->id}", $user);
        }

        $staff = \App\Models\Staff::first();
        if ($staff) {
            $this->testEndpoint($kernel, 'GET', "/personeller/{$staff->id}", "Personel Detay #{$staff->id}", $user);
        }

        $invoice = \App\Models\PurchaseInvoice::first();
        if ($invoice) {
            $this->testEndpoint($kernel, 'GET', "/alis-faturalari/{$invoice->id}", "Alis Faturasi #{$invoice->id}", $user);
        }

        $firm = \App\Models\Firm::first();
        if ($firm) {
            $this->testEndpoint($kernel, 'GET', "/firmalar/{$firm->id}", "Firma Detay #{$firm->id}", $user);
        }

        $stockCount = \App\Models\StockCount::first();
        if ($stockCount) {
            $this->testEndpoint($kernel, 'GET', "/stok/sayim/{$stockCount->id}", "Stok Sayim Detay #{$stockCount->id}", $user);
        }

        // Summary
        $this->newLine();
        $this->info("=============================");
        $this->info("=== TEST SONUCLARI ===");
        $this->info("=============================");
        $this->info("Basarili: " . count($this->successes));
        $this->error("Hatali: " . count($this->errors));

        if (count($this->errors) > 0) {
            $this->newLine();
            $this->info("=== HATALAR ===");
            foreach ($this->errors as $i => $err) {
                $this->newLine();
                $this->error(($i + 1) . ". [{$err['status']}] {$err['module']} ({$err['method']} {$err['uri']})");
                $this->line("   Hata: {$err['error']}");
                if (!empty($err['file'])) {
                    $this->line("   Dosya: {$err['file']}");
                }
            }
        }

        return count($this->errors) > 0 ? 1 : 0;
    }

    private function testEndpoint($kernel, $method, $uri, $name, $user)
    {
        try {
            $request = Request::create($uri, $method);
            $request->setLaravelSession(app('session.store'));
            Auth::login($user);

            $response = $kernel->handle($request);
            $status = $response->getStatusCode();

            $kernel->terminate($request, $response);

            if ($status >= 200 && $status < 400) {
                $this->successes[] = $name;
                $this->line("  OK [{$status}] {$name} ({$method} {$uri})");
            } elseif ($status == 500) {
                $body = $response->getContent();
                $errorMsg = 'HTTP 500';
                if (preg_match('/"message"\s*:\s*"([^"]+)"/', $body, $m2)) {
                    $errorMsg = $m2[1];
                } elseif (preg_match('/<title>(.*?)<\/title>/s', $body, $m)) {
                    $errorMsg = strip_tags($m[1]);
                }

                $fileInfo = '';
                if (preg_match('/"file"\s*:\s*"([^"]+)"/', $body, $fm)) {
                    $fileInfo = str_replace('\\/', '/', $fm[1]);
                    if (preg_match('/"line"\s*:\s*(\d+)/', $body, $lm)) {
                        $fileInfo .= ':' . $lm[1];
                    }
                }

                $this->errors[] = [
                    'module' => $name,
                    'method' => $method,
                    'uri' => $uri,
                    'status' => $status,
                    'error' => $errorMsg,
                    'file' => $fileInfo,
                ];
                $this->error("  FAIL [{$status}] {$name} ({$method} {$uri}) - {$errorMsg}");
                if ($fileInfo) $this->line("     at {$fileInfo}");
            } else {
                $this->errors[] = [
                    'module' => $name,
                    'method' => $method,
                    'uri' => $uri,
                    'status' => $status,
                    'error' => "Unexpected status code: {$status}",
                ];
                $this->warn("  WARN [{$status}] {$name} ({$method} {$uri})");
            }
        } catch (\Throwable $e) {
            $errorMsg = get_class($e) . ': ' . $e->getMessage();
            $file = $e->getFile() . ':' . $e->getLine();
            $this->errors[] = [
                'module' => $name,
                'method' => $method,
                'uri' => $uri,
                'status' => 'EXC',
                'error' => $errorMsg,
                'file' => $file,
            ];
            $this->error("  FAIL [EXC] {$name} ({$method} {$uri})");
            $this->line("     {$errorMsg}");
            $this->line("     at {$file}");
        }
    }
}
