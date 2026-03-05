<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Sektörel Şablonlar
    |--------------------------------------------------------------------------
    |
    | Her sektörel şablon, o sektöre özel varsayılan kategoriler, ürünler,
    | aktif modüller ve ayarları tanımlar. Yeni tenant oluşturulurken
    | seçilen sektöre göre bu şablon uygulanır.
    |
    */

    'templates' => [

        'market' => [
            'name'        => 'Market / Bakkal',
            'description' => 'Süpermarket, bakkal, şarküteri ve gıda perakende işletmeleri.',
            'icon'        => 'storefront-outline',   // Ionicons
            'emoji'       => '🏪',
            'color'       => '#4ade80',               // yeşil pastel
            'bg_color'    => '#f0fdf4',               // yeşil açık arka plan
            'gradient'    => ['#86efac', '#4ade80'],   // pastel gradient
            'modules'     => ['core_pos', 'hardware', 'einvoice', 'income_expense'],
            'default_categories' => [
                'Gıda & İçecek',
                'Süt Ürünleri',
                'Et & Şarküteri',
                'Meyve & Sebze',
                'Temizlik',
                'Kişisel Bakım',
                'Atıştırmalık',
                'Dondurulmuş Gıda',
                'Temel Gıda',
                'İçecekler',
            ],
            'settings' => [
                'tax_included'      => true,
                'barcode_required'  => true,
                'weight_products'   => true,
                'default_tax_rate'  => 10,
                'receipt_type'      => 'thermal',
            ],
        ],

        'cafe' => [
            'name'        => 'Kafe / Restoran',
            'description' => 'Kafe, restoran, pastane ve yiyecek-içecek işletmeleri.',
            'icon'        => 'cafe-outline',          // Ionicons
            'emoji'       => '☕',
            'color'       => '#f59e0b',               // amber pastel
            'bg_color'    => '#fffbeb',               // amber açık arka plan
            'gradient'    => ['#fcd34d', '#f59e0b'],   // pastel gradient
            'modules'     => ['core_pos', 'hardware', 'staff'],
            'default_categories' => [
                'Sıcak İçecekler',
                'Soğuk İçecekler',
                'Tatlılar',
                'Kahvaltı',
                'Ana Yemekler',
                'Salatalar',
                'Atıştırmalıklar',
                'Özel Menü',
            ],
            'settings' => [
                'tax_included'      => true,
                'barcode_required'  => false,
                'weight_products'   => false,
                'default_tax_rate'  => 10,
                'receipt_type'      => 'thermal',
                'table_management'  => true,
            ],
        ],

        'butik' => [
            'name'        => 'Butik / Giyim',
            'description' => 'Giyim mağazası, butik, ayakkabı ve aksesuar mağazaları.',
            'icon'        => 'shirt-outline',         // Ionicons
            'emoji'       => '👗',
            'color'       => '#f472b6',               // pembe pastel
            'bg_color'    => '#fdf2f8',               // pembe açık arka plan
            'gradient'    => ['#f9a8d4', '#f472b6'],   // pastel gradient
            'modules'     => ['core_pos', 'einvoice', 'income_expense'],
            'default_categories' => [
                'Kadın Giyim',
                'Erkek Giyim',
                'Çocuk Giyim',
                'Ayakkabı',
                'Çanta',
                'Aksesuar',
                'İç Giyim',
                'Spor Giyim',
            ],
            'settings' => [
                'tax_included'      => true,
                'barcode_required'  => true,
                'weight_products'   => false,
                'default_tax_rate'  => 20,
                'receipt_type'      => 'a4',
                'size_tracking'     => true,
                'color_tracking'    => true,
            ],
        ],

        'toptan' => [
            'name'        => 'Toptan Satış',
            'description' => 'Toptan gıda, toptancı, distribütör ve B2B satış işletmeleri.',
            'icon'        => 'cube-outline',          // Ionicons
            'emoji'       => '📦',
            'color'       => '#818cf8',               // indigo pastel
            'bg_color'    => '#eef2ff',               // indigo açık arka plan
            'gradient'    => ['#a5b4fc', '#818cf8'],   // pastel gradient
            'modules'     => ['core_pos', 'hardware', 'einvoice', 'income_expense', 'advanced_reports', 'api_access'],
            'default_categories' => [
                'Gıda',
                'İçecek',
                'Temizlik',
                'Kırtasiye',
                'Ambalaj',
                'Endüstriyel',
            ],
            'settings' => [
                'tax_included'        => false,
                'barcode_required'    => true,
                'weight_products'     => true,
                'default_tax_rate'    => 20,
                'receipt_type'        => 'a4',
                'bulk_pricing'        => true,
                'minimum_order'       => true,
                'credit_limit'        => true,
            ],
        ],

        'hizmet' => [
            'name'        => 'Hizmet Sektörü',
            'description' => 'Kuaför, güzellik salonu, oto yıkama, tamirhane ve diğer hizmet işletmeleri.',
            'icon'        => 'cut-outline',           // Ionicons
            'emoji'       => '✂️',
            'color'       => '#2dd4bf',               // teal pastel
            'bg_color'    => '#f0fdfa',               // teal açık arka plan
            'gradient'    => ['#5eead4', '#2dd4bf'],   // pastel gradient
            'modules'     => ['core_pos', 'staff', 'income_expense'],
            'default_categories' => [
                'Saç Bakım',
                'Cilt Bakım',
                'Tırnak Bakım',
                'Masaj',
                'Epilasyon',
                'Ürün Satışı',
                'Paket Hizmetler',
            ],
            'settings' => [
                'tax_included'         => true,
                'barcode_required'     => false,
                'weight_products'      => false,
                'default_tax_rate'     => 20,
                'receipt_type'         => 'thermal',
                'appointment_based'    => true,
                'service_duration'     => true,
            ],
        ],

    ],

];
