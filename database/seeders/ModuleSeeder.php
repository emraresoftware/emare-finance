<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            [
                'code'         => 'core_pos',
                'name'         => 'Temel POS',
                'description'  => 'Satış, ürün, stok yönetimi, müşteri ve temel raporlama. Her planda dahildir.',
                'is_core'      => true,
                'scope'        => 'tenant',
                'dependencies' => [],
                'sort_order'   => 1,
            ],
            [
                'code'         => 'hardware',
                'name'         => 'Donanım Sürücüleri',
                'description'  => 'Yazarkasa, barkod okuyucu, terazi, fiş yazıcı entegrasyonu.',
                'is_core'      => false,
                'scope'        => 'branch',
                'dependencies' => ['core_pos'],
                'sort_order'   => 2,
            ],
            [
                'code'         => 'einvoice',
                'name'         => 'E-Fatura / E-Arşiv',
                'description'  => 'E-fatura, e-arşiv fatura kesme ve GİB entegrasyonu.',
                'is_core'      => false,
                'scope'        => 'tenant',
                'dependencies' => ['core_pos'],
                'sort_order'   => 3,
            ],
            [
                'code'         => 'income_expense',
                'name'         => 'Gelir-Gider Yönetimi',
                'description'  => 'Detaylı gelir-gider takibi, kategorilendirme, cari hesaplar.',
                'is_core'      => false,
                'scope'        => 'tenant',
                'dependencies' => ['core_pos'],
                'sort_order'   => 4,
            ],
            [
                'code'         => 'staff',
                'name'         => 'Personel Yönetimi',
                'description'  => 'Personel takibi, maaş, izin, prim ve hareket kayıtları.',
                'is_core'      => false,
                'scope'        => 'both',
                'dependencies' => ['core_pos'],
                'sort_order'   => 5,
            ],
            [
                'code'         => 'advanced_reports',
                'name'         => 'Gelişmiş Raporlar',
                'description'  => 'Detaylı analiz, karşılaştırmalı raporlar, grafik ve dışa aktarma.',
                'is_core'      => false,
                'scope'        => 'tenant',
                'dependencies' => ['core_pos'],
                'sort_order'   => 6,
            ],
            [
                'code'         => 'api_access',
                'name'         => 'API Erişimi',
                'description'  => 'REST API üzerinden dış sistemlerle entegrasyon.',
                'is_core'      => false,
                'scope'        => 'tenant',
                'dependencies' => ['core_pos'],
                'sort_order'   => 7,
            ],
            [
                'code'         => 'mobile_premium',
                'name'         => 'Mobil Premium',
                'description'  => 'Gelişmiş mobil uygulama özellikleri: çevrimdışı mod, push bildirimler.',
                'is_core'      => false,
                'scope'        => 'tenant',
                'dependencies' => ['core_pos', 'api_access'],
                'sort_order'   => 8,
            ],
            [
                'code'         => 'marketing',
                'name'         => 'Pazarlama',
                'description'  => 'Teklif oluşturma, kampanya yönetimi, müşteri segmentasyonu, mesajlaşma ve sadakat programı.',
                'is_core'      => false,
                'scope'        => 'tenant',
                'dependencies' => ['core_pos'],
                'sort_order'   => 9,
            ],
            [
                'code'         => 'sms',
                'name'         => 'SMS Yönetimi',
                'description'  => 'SMS entegrasyonu, şablonlar, otomatik senaryolar, toplu gönderim ve gönderim logları.',
                'is_core'      => false,
                'scope'        => 'tenant',
                'dependencies' => ['core_pos'],
                'sort_order'   => 10,
            ],
        ];

        foreach ($modules as $data) {
            Module::updateOrCreate(
                ['code' => $data['code']],
                $data,
            );
        }
    }
}
