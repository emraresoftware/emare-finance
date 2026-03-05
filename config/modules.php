<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Modül Tanımları
    |--------------------------------------------------------------------------
    |
    | Sistemdeki tüm modüllerin tanımları. Bu yapılandırma, modül seeder'ı
    | ve ModuleService tarafından referans olarak kullanılır.
    |
    */

    'definitions' => [

        'core_pos' => [
            'name'         => 'Temel POS',
            'description'  => 'Satış, ürün, stok yönetimi, müşteri ve temel raporlama.',
            'is_core'      => true,
            'scope'        => 'tenant',
            'dependencies' => [],
            'routes'       => ['sales.*', 'products.*', 'customers.*', 'stock.*'],
        ],

        'hardware' => [
            'name'         => 'Donanım Sürücüleri',
            'description'  => 'Yazarkasa, barkod okuyucu, terazi, fiş yazıcı entegrasyonu.',
            'is_core'      => false,
            'scope'        => 'branch',
            'dependencies' => ['core_pos'],
            'routes'       => ['hardware.*'],
        ],

        'einvoice' => [
            'name'         => 'E-Fatura / E-Arşiv',
            'description'  => 'E-fatura, e-arşiv fatura kesme ve GİB entegrasyonu.',
            'is_core'      => false,
            'scope'        => 'tenant',
            'dependencies' => ['core_pos'],
            'routes'       => ['einvoice.*'],
        ],

        'income_expense' => [
            'name'         => 'Gelir-Gider Yönetimi',
            'description'  => 'Detaylı gelir-gider takibi, kategorilendirme, cari hesaplar.',
            'is_core'      => false,
            'scope'        => 'tenant',
            'dependencies' => ['core_pos'],
            'routes'       => ['income.*', 'expense.*'],
        ],

        'staff' => [
            'name'         => 'Personel Yönetimi',
            'description'  => 'Personel takibi, maaş, izin, prim ve hareket kayıtları.',
            'is_core'      => false,
            'scope'        => 'both',
            'dependencies' => ['core_pos'],
            'routes'       => ['staff.*'],
        ],

        'advanced_reports' => [
            'name'         => 'Gelişmiş Raporlar',
            'description'  => 'Detaylı analiz, karşılaştırmalı raporlar, grafik ve dışa aktarma.',
            'is_core'      => false,
            'scope'        => 'tenant',
            'dependencies' => ['core_pos'],
            'routes'       => ['reports.advanced.*'],
        ],

        'api_access' => [
            'name'         => 'API Erişimi',
            'description'  => 'REST API üzerinden dış sistemlerle entegrasyon.',
            'is_core'      => false,
            'scope'        => 'tenant',
            'dependencies' => ['core_pos'],
            'routes'       => ['api.*'],
        ],

        'mobile_premium' => [
            'name'         => 'Mobil Premium',
            'description'  => 'Gelişmiş mobil uygulama özellikleri: çevrimdışı mod, push bildirimler.',
            'is_core'      => false,
            'scope'        => 'tenant',
            'dependencies' => ['core_pos', 'api_access'],
            'routes'       => [],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Modül Kontrol Ayarları
    |--------------------------------------------------------------------------
    */

    // Modül kontrolü devre dışı bırakılırsa tüm modüller aktif sayılır (geliştirme ortamı)
    'check_enabled' => env('MODULE_CHECK_ENABLED', true),

    // Tenant olmadan modül kontrolü yapılsın mı
    'require_tenant' => env('MODULE_REQUIRE_TENANT', true),

];
