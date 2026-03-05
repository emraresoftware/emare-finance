<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ArchiveSeeder extends Seeder
{
    // ── Türkçe gerçekçi isimler ──
    private array $maleNames = ['Ahmet','Mehmet','Mustafa','Ali','Hüseyin','Hasan','İbrahim','İsmail','Yusuf','Osman','Murat','Ömer','Emre','Burak','Cem','Can','Serkan','Kemal','Fatih','Barış','Onur','Uğur','Selim','Tarık','Deniz','Kaan','Efe','Berk','Tolga','Erdem'];
    private array $femaleNames = ['Ayşe','Fatma','Emine','Hatice','Zeynep','Elif','Merve','Büşra','Esra','Derya','Seda','Gül','Pınar','Sibel','Özlem','Sevgi','Melek','Nurgül','Aysun','Canan','Gamze','İrem','Şeyma','Beyza','Nil','Aslı','Burcu','Didem','Ece','Fulya'];
    private array $surnames = ['Yılmaz','Kaya','Demir','Çelik','Şahin','Yıldız','Yıldırım','Öztürk','Aydın','Özdemir','Arslan','Doğan','Kılıç','Aslan','Çetin','Kara','Koç','Kurt','Özkan','Şimşek','Polat','Korkmaz','Aktaş','Ünal','Güneş','Acar','Tan','Erdem','Bayrak','Tekin'];

    private array $cities = ['İstanbul','Ankara','İzmir','Bursa','Antalya','Adana','Konya','Gaziantep','Mersin','Kayseri','Eskişehir','Denizli','Trabzon','Samsun','Manisa'];
    private array $districts = [
        'İstanbul' => ['Kadıköy','Beşiktaş','Şişli','Bakırköy','Ataşehir','Üsküdar','Fatih','Beyoğlu','Maltepe','Kartal'],
        'Ankara' => ['Çankaya','Kızılay','Keçiören','Yenimahalle','Etimesgut','Mamak'],
        'İzmir' => ['Konak','Bornova','Karşıyaka','Buca','Alsancak','Çeşme'],
    ];

    // ── Ürün veritabanı (gerçek market/kafe ürünleri) ──
    private array $productCategories = [
        'Yiyecek' => [
            ['name' => 'Tost (Kaşarlı)', 'price_range' => [25, 75], 'cost_ratio' => 0.35, 'vat' => 10],
            ['name' => 'Tost (Karışık)', 'price_range' => [35, 95], 'cost_ratio' => 0.35, 'vat' => 10],
            ['name' => 'Sandviç (Tavuk)', 'price_range' => [40, 110], 'cost_ratio' => 0.32, 'vat' => 10],
            ['name' => 'Sandviç (Ton Balıklı)', 'price_range' => [45, 120], 'cost_ratio' => 0.30, 'vat' => 10],
            ['name' => 'Hamburger (Klasik)', 'price_range' => [55, 150], 'cost_ratio' => 0.30, 'vat' => 10],
            ['name' => 'Hamburger (Cheese)', 'price_range' => [65, 170], 'cost_ratio' => 0.30, 'vat' => 10],
            ['name' => 'Pizza (Karışık)', 'price_range' => [60, 180], 'cost_ratio' => 0.28, 'vat' => 10],
            ['name' => 'Pizza (Margarita)', 'price_range' => [50, 150], 'cost_ratio' => 0.28, 'vat' => 10],
            ['name' => 'Lahmacun', 'price_range' => [20, 65], 'cost_ratio' => 0.25, 'vat' => 10],
            ['name' => 'Pide (Kıymalı)', 'price_range' => [40, 120], 'cost_ratio' => 0.28, 'vat' => 10],
            ['name' => 'Pide (Kaşarlı)', 'price_range' => [35, 100], 'cost_ratio' => 0.28, 'vat' => 10],
            ['name' => 'Döner Dürüm', 'price_range' => [35, 110], 'cost_ratio' => 0.30, 'vat' => 10],
            ['name' => 'İskender Porsiyon', 'price_range' => [70, 220], 'cost_ratio' => 0.35, 'vat' => 10],
            ['name' => 'Mercimek Çorbası', 'price_range' => [15, 55], 'cost_ratio' => 0.20, 'vat' => 10],
            ['name' => 'Salata (Mevsim)', 'price_range' => [25, 75], 'cost_ratio' => 0.22, 'vat' => 10],
            ['name' => 'Patates Kızartması', 'price_range' => [15, 50], 'cost_ratio' => 0.18, 'vat' => 10],
            ['name' => 'Simit', 'price_range' => [3, 15], 'cost_ratio' => 0.30, 'vat' => 1],
            ['name' => 'Poğaça', 'price_range' => [5, 20], 'cost_ratio' => 0.30, 'vat' => 1],
            ['name' => 'Börek (Porsiyon)', 'price_range' => [20, 65], 'cost_ratio' => 0.28, 'vat' => 10],
            ['name' => 'Künefe', 'price_range' => [40, 120], 'cost_ratio' => 0.30, 'vat' => 10],
        ],
        'İçecek' => [
            ['name' => 'Çay (Bardak)', 'price_range' => [3, 15], 'cost_ratio' => 0.10, 'vat' => 10],
            ['name' => 'Türk Kahvesi', 'price_range' => [15, 50], 'cost_ratio' => 0.15, 'vat' => 10],
            ['name' => 'Filtre Kahve', 'price_range' => [20, 65], 'cost_ratio' => 0.12, 'vat' => 10],
            ['name' => 'Latte', 'price_range' => [25, 80], 'cost_ratio' => 0.15, 'vat' => 10],
            ['name' => 'Cappuccino', 'price_range' => [25, 80], 'cost_ratio' => 0.15, 'vat' => 10],
            ['name' => 'Espresso', 'price_range' => [18, 55], 'cost_ratio' => 0.12, 'vat' => 10],
            ['name' => 'Americano', 'price_range' => [20, 60], 'cost_ratio' => 0.12, 'vat' => 10],
            ['name' => 'Soğuk Kahve (Ice)', 'price_range' => [30, 90], 'cost_ratio' => 0.15, 'vat' => 10],
            ['name' => 'Ayran', 'price_range' => [5, 20], 'cost_ratio' => 0.25, 'vat' => 10],
            ['name' => 'Kola (33cl)', 'price_range' => [8, 30], 'cost_ratio' => 0.40, 'vat' => 10],
            ['name' => 'Fanta (33cl)', 'price_range' => [8, 30], 'cost_ratio' => 0.40, 'vat' => 10],
            ['name' => 'Sprite (33cl)', 'price_range' => [8, 30], 'cost_ratio' => 0.40, 'vat' => 10],
            ['name' => 'Su (0.5L)', 'price_range' => [2, 10], 'cost_ratio' => 0.20, 'vat' => 1],
            ['name' => 'Meyve Suyu', 'price_range' => [8, 30], 'cost_ratio' => 0.35, 'vat' => 10],
            ['name' => 'Taze Portakal Suyu', 'price_range' => [20, 65], 'cost_ratio' => 0.30, 'vat' => 10],
            ['name' => 'Limonata', 'price_range' => [15, 50], 'cost_ratio' => 0.15, 'vat' => 10],
            ['name' => 'Smoothie', 'price_range' => [30, 90], 'cost_ratio' => 0.25, 'vat' => 10],
            ['name' => 'Sıcak Çikolata', 'price_range' => [20, 65], 'cost_ratio' => 0.18, 'vat' => 10],
            ['name' => 'Bitki Çayı', 'price_range' => [10, 35], 'cost_ratio' => 0.12, 'vat' => 10],
            ['name' => 'Milkshake', 'price_range' => [30, 85], 'cost_ratio' => 0.22, 'vat' => 10],
        ],
        'Market' => [
            ['name' => 'Ekmek', 'price_range' => [2, 12], 'cost_ratio' => 0.60, 'vat' => 1],
            ['name' => 'Süt (1L)', 'price_range' => [7, 30], 'cost_ratio' => 0.65, 'vat' => 1],
            ['name' => 'Yumurta (10lu)', 'price_range' => [15, 65], 'cost_ratio' => 0.70, 'vat' => 1],
            ['name' => 'Peynir (Beyaz 500g)', 'price_range' => [30, 120], 'cost_ratio' => 0.65, 'vat' => 1],
            ['name' => 'Zeytin (500g)', 'price_range' => [20, 80], 'cost_ratio' => 0.60, 'vat' => 1],
            ['name' => 'Tereyağı (250g)', 'price_range' => [20, 90], 'cost_ratio' => 0.68, 'vat' => 10],
            ['name' => 'Makarna (500g)', 'price_range' => [5, 22], 'cost_ratio' => 0.62, 'vat' => 1],
            ['name' => 'Pirinç (1kg)', 'price_range' => [10, 45], 'cost_ratio' => 0.65, 'vat' => 1],
            ['name' => 'Şeker (1kg)', 'price_range' => [8, 35], 'cost_ratio' => 0.70, 'vat' => 1],
            ['name' => 'Çay (1kg)', 'price_range' => [30, 130], 'cost_ratio' => 0.60, 'vat' => 10],
            ['name' => 'Deterjan (Sıvı 1L)', 'price_range' => [25, 90], 'cost_ratio' => 0.55, 'vat' => 20],
            ['name' => 'Kağıt Havlu (3lü)', 'price_range' => [15, 55], 'cost_ratio' => 0.55, 'vat' => 20],
            ['name' => 'Cips (Paket)', 'price_range' => [5, 25], 'cost_ratio' => 0.50, 'vat' => 10],
            ['name' => 'Çikolata (Tablet)', 'price_range' => [8, 40], 'cost_ratio' => 0.55, 'vat' => 10],
            ['name' => 'Bisküvi (Paket)', 'price_range' => [5, 22], 'cost_ratio' => 0.55, 'vat' => 10],
        ],
        'Teknoloji' => [
            ['name' => 'USB Kablo', 'price_range' => [15, 60], 'cost_ratio' => 0.40, 'vat' => 20],
            ['name' => 'Kulaklık (Kablolu)', 'price_range' => [30, 120], 'cost_ratio' => 0.40, 'vat' => 20],
            ['name' => 'Powerbank 10000mAh', 'price_range' => [100, 400], 'cost_ratio' => 0.45, 'vat' => 20],
            ['name' => 'Mouse Pad', 'price_range' => [15, 50], 'cost_ratio' => 0.35, 'vat' => 20],
            ['name' => 'Telefon Kılıfı', 'price_range' => [20, 80], 'cost_ratio' => 0.30, 'vat' => 20],
        ],
        'Hizmet' => [
            ['name' => 'Teslimat Ücreti', 'price_range' => [10, 40], 'cost_ratio' => 0.60, 'vat' => 20],
            ['name' => 'Paketleme', 'price_range' => [2, 10], 'cost_ratio' => 0.50, 'vat' => 20],
            ['name' => 'Danışmanlık (Saat)', 'price_range' => [200, 800], 'cost_ratio' => 0.20, 'vat' => 20],
            ['name' => 'Teknik Servis', 'price_range' => [100, 500], 'cost_ratio' => 0.30, 'vat' => 20],
            ['name' => 'Kurulum Hizmeti', 'price_range' => [150, 600], 'cost_ratio' => 0.25, 'vat' => 20],
        ],
    ];

    // ── Tedarikçiler ──
    private array $firmNames = [
        'Anadolu Gıda A.Ş.','Ege Tedarik Ltd.','Marmara Dağıtım','Karadeniz İthalat','Akdeniz Toptan',
        'Yıldız Holding','Ülker Dağıtım','Coca-Cola İçecek','Pepsi Türkiye','Nestle Türkiye',
        'Unilever Gıda','Tat Gıda','Dimes İçecek','Pınar Süt','Sütaş Dağıtım',
        'Metro Gross Market','Bizim Toptan','Makro Market Toptan','Hamidiye Su','Erikli Su',
    ];

    // ── Gelir/Gider Türleri ──
    private array $incomeTypes = ['Satış Geliri','Kira Geliri','Faiz Geliri','Komisyon Geliri','Hizmet Geliri','Diğer Gelir'];
    private array $expenseTypes = ['Kira Gideri','Elektrik','Su','Doğalgaz','İnternet','Telefon','Personel Maaşı','SGK Primi','Vergi Ödemesi','Muhasebe','Temizlik','Kırtasiye','Reklam','Bakım/Onarım','Nakliye','Sigorta','Diğer Gider'];

    // Enflasyon çarpanları (yıla göre fiyat artışı — Türkiye gerçekçi)
    private function getInflationMultiplier(int $year): float
    {
        $multipliers = [
            2016 => 1.00, 2017 => 1.12, 2018 => 1.35, 2019 => 1.55,
            2020 => 1.75, 2021 => 2.10, 2022 => 3.60, 2023 => 5.20,
            2024 => 6.80, 2025 => 8.20, 2026 => 9.50,
        ];
        return $multipliers[$year] ?? 1.00;
    }

    // Mevsimsellik çarpanı (ay bazında satış hacmi)
    private function getSeasonMultiplier(int $month): float
    {
        return match($month) {
            1 => 0.75,   // Ocak — yılbaşı sonrası düşük
            2 => 0.80,
            3 => 0.90,
            4 => 0.95,
            5 => 1.05,
            6 => 1.15,   // Yaz başlangıcı
            7 => 1.20,   // Yaz zirve
            8 => 1.15,
            9 => 1.00,   // Okul açılışı
            10 => 1.05,
            11 => 1.10,
            12 => 1.25,  // Yılbaşı alışverişi
        };
    }

    // İşletme büyüme çarpanı (3 yılda büyüme)
    private function getGrowthMultiplier(int $year): float
    {
        $yearIndex = $year - 2023;
        // 2023: 0.85x, 2026: 1.10x — kademeli büyüme
        return 0.85 + ($yearIndex * 0.083);
    }

    public function run(): void
    {
        $this->command->info('🗂️  3 yıllık arşiv verisi oluşturuluyor (2023-2026)...');
        $startTime = microtime(true);

        $isSqlite = DB::connection()->getDriverName() === 'sqlite';
        if ($isSqlite) {
            DB::statement('PRAGMA foreign_keys = OFF');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }

        // Mevcut verileri temizle
        $this->command->info('🧹 Mevcut veriler temizleniyor...');
        DB::table('staff_motions')->truncate();
        DB::table('stock_movements')->truncate();
        DB::table('stock_count_items')->truncate();
        DB::table('stock_counts')->truncate();
        DB::table('account_transactions')->truncate();
        DB::table('sale_items')->truncate();
        DB::table('sales')->truncate();
        DB::table('purchase_invoice_items')->truncate();
        DB::table('purchase_invoices')->truncate();
        DB::table('incomes')->truncate();
        DB::table('expenses')->truncate();
        DB::table('income_expense_types')->truncate();
        DB::table('products')->truncate();
        DB::table('categories')->truncate();
        DB::table('customers')->truncate();
        DB::table('firms')->truncate();
        DB::table('staff')->truncate();
        DB::table('branches')->truncate();
        DB::table('payment_types')->truncate();
        // Ek modüller
        DB::table('tasks')->truncate();
        DB::table('hardware_devices')->truncate();
        DB::table('quote_items')->truncate();
        DB::table('quotes')->truncate();
        DB::table('recurring_invoice_items')->truncate();
        DB::table('recurring_invoices')->truncate();
        DB::table('signage_schedules')->truncate();
        DB::table('signage_device_playlist')->truncate();
        DB::table('signage_playlist_items')->truncate();
        DB::table('signage_playlists')->truncate();
        DB::table('signage_devices')->truncate();
        DB::table('signage_contents')->truncate();
        DB::table('sms_logs')->truncate();
        DB::table('sms_blacklist')->truncate();
        DB::table('sms_scenarios')->truncate();
        DB::table('sms_templates')->truncate();
        DB::table('marketing_message_logs')->truncate();
        DB::table('marketing_messages')->truncate();
        DB::table('loyalty_points')->truncate();
        DB::table('loyalty_programs')->truncate();
        DB::table('customer_segment_members')->truncate();
        DB::table('customer_segments')->truncate();
        DB::table('campaign_usages')->truncate();
        DB::table('campaigns')->truncate();
        DB::table('e_invoice_items')->truncate();
        DB::table('e_invoices')->truncate();
        DB::table('e_invoice_settings')->truncate();

        if ($isSqlite) {
            DB::statement('PRAGMA foreign_keys = ON');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        // ════════════════════════════════════════════════════════
        // 1. ŞUBELER
        // ════════════════════════════════════════════════════════
        $this->command->info('🏢 Şubeler oluşturuluyor...');
        $branches = [
            ['name' => 'Merkez Şube',    'code' => 'MRK', 'city' => 'İstanbul', 'district' => 'Kadıköy',   'address' => 'Caferağa Mah. Moda Cad. No:42',  'phone' => '0216 345 67 89', 'created_at' => '2023-01-01'],
            ['name' => 'Beşiktaş Şube',  'code' => 'BSK', 'city' => 'İstanbul', 'district' => 'Beşiktaş',  'address' => 'Sinanpaşa Mah. Çarşı Cad. No:18', 'phone' => '0212 234 56 78', 'created_at' => '2023-01-01'],
            ['name' => 'Ankara Şube',    'code' => 'ANK', 'city' => 'Ankara',   'district' => 'Çankaya',   'address' => 'Tunalı Hilmi Cad. No:115',        'phone' => '0312 456 78 90', 'created_at' => '2023-01-01'],
            ['name' => 'İzmir Şube',     'code' => 'IZM', 'city' => 'İzmir',    'district' => 'Alsancak',  'address' => 'Kıbrıs Şehitleri Cad. No:55',     'phone' => '0232 567 89 01', 'created_at' => '2023-06-01'],
            ['name' => 'Antalya Şube',   'code' => 'ANT', 'city' => 'Antalya',  'district' => 'Muratpaşa', 'address' => 'Atatürk Cad. No:78',              'phone' => '0242 678 90 12', 'created_at' => '2024-01-01'],
        ];

        $branchIds = [];
        foreach ($branches as $b) {
            $branchIds[] = DB::table('branches')->insertGetId([
                'name' => $b['name'],
                'code' => $b['code'],
                'city' => $b['city'],
                'district' => $b['district'],
                'address' => $b['address'],
                'phone' => $b['phone'],
                'is_active' => true,
                'created_at' => $b['created_at'],
                'updated_at' => $b['created_at'],
            ]);
        }
        $branchDates = array_column($branches, 'created_at');

        // ════════════════════════════════════════════════════════
        // 2. KATEGORİLER & ÜRÜNLER
        // ════════════════════════════════════════════════════════
        $this->command->info('📦 Kategoriler ve ürünler oluşturuluyor...');
        $productRecords = [];
        $catIndex = 0;
        foreach ($this->productCategories as $catName => $products) {
            $catId = DB::table('categories')->insertGetId([
                'name' => $catName,
                'sort_order' => $catIndex++,
                'is_active' => true,
                'created_at' => '2016-01-15',
                'updated_at' => '2016-01-15',
            ]);

            foreach ($products as $p) {
                // Güncel fiyat (2026 seviyesi)
                $currentPrice = round(rand($p['price_range'][0] * 100, $p['price_range'][1] * 100) / 100, 2);
                $purchasePrice = round($currentPrice * $p['cost_ratio'], 2);
                $barcode = '869' . str_pad(rand(1000000000, 9999999999), 10, '0', STR_PAD_LEFT);

                $prodId = DB::table('products')->insertGetId([
                    'barcode' => $barcode,
                    'name' => $p['name'],
                    'category_id' => $catId,
                    'unit' => 'Adet',
                    'purchase_price' => $purchasePrice,
                    'sale_price' => $currentPrice,
                    'vat_rate' => $p['vat'],
                    'stock_quantity' => rand(10, 500),
                    'critical_stock' => rand(5, 20),
                    'is_active' => true,
                    'created_at' => '2016-01-15',
                    'updated_at' => now(),
                ]);

                $productRecords[] = [
                    'id' => $prodId,
                    'name' => $p['name'],
                    'barcode' => $barcode,
                    'current_price' => $currentPrice,
                    'cost_ratio' => $p['cost_ratio'],
                    'vat' => $p['vat'],
                    'category' => $catName,
                ];
            }
        }
        $this->command->info('   → ' . count($productRecords) . ' ürün oluşturuldu');

        // ════════════════════════════════════════════════════════
        // 3. MÜŞTERİLER (150 müşteri)
        // ════════════════════════════════════════════════════════
        $this->command->info('👥 Müşteriler oluşturuluyor...');
        $customerIds = [];
        $customerBatch = [];
        for ($i = 0; $i < 150; $i++) {
            $isMale = rand(0, 1);
            $firstName = $isMale ? $this->maleNames[array_rand($this->maleNames)] : $this->femaleNames[array_rand($this->femaleNames)];
            $lastName = $this->surnames[array_rand($this->surnames)];
            $isCompany = rand(1, 100) <= 25;
            $city = $this->cities[array_rand($this->cities)];

            $name = $isCompany
                ? $lastName . ' ' . ['Ticaret','Market','Gıda','İnşaat','Tekstil','Lojistik','Bilişim'][array_rand(['Ticaret','Market','Gıda','İnşaat','Tekstil','Lojistik','Bilişim'])] . ' Ltd. Şti.'
                : $firstName . ' ' . $lastName;

            $createdYear = rand(2021, 2025);
            $createdMonth = rand(1, 12);
            $createdAt = sprintf('%d-%02d-%02d', $createdYear, $createdMonth, rand(1, 28));

            $customerBatch[] = [
                'name' => $name,
                'type' => $isCompany ? 'company' : 'individual',
                'tax_number' => $isCompany ? (string)rand(1000000000, 9999999999) : null,
                'tax_office' => $isCompany ? $city . ' Vergi Dairesi' : null,
                'phone' => '05' . rand(30, 59) . ' ' . rand(100, 999) . ' ' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT),
                'email' => Str::slug($firstName . '.' . $lastName, '.') . '@' . ['gmail.com','hotmail.com','outlook.com','yahoo.com','firma.com.tr'][array_rand(['gmail.com','hotmail.com','outlook.com','yahoo.com','firma.com.tr'])],
                'city' => $city,
                'balance' => 0,
                'is_active' => rand(1, 100) <= 92 ? true : false,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];
        }
        foreach ($customerBatch as $c) {
            $customerIds[] = DB::table('customers')->insertGetId($c);
        }

        // ════════════════════════════════════════════════════════
        // 4. TEDARİKÇİLER (20 firma)
        // ════════════════════════════════════════════════════════
        $this->command->info('🏭 Tedarikçiler oluşturuluyor...');
        $firmIds = [];
        foreach ($this->firmNames as $idx => $firmName) {
            $city = $this->cities[array_rand($this->cities)];
            $firmIds[] = DB::table('firms')->insertGetId([
                'name' => $firmName,
                'tax_number' => (string)rand(1000000000, 9999999999),
                'tax_office' => $city . ' Vergi Dairesi',
                'phone' => '0' . rand(212, 312) . ' ' . rand(100, 999) . ' ' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT),
                'email' => Str::slug($firmName, '.') . '@tedarik.com.tr',
                'city' => $city,
                'balance' => 0,
                'is_active' => true,
                'created_at' => '2016-01-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT),
                'updated_at' => now(),
            ]);
        }

        // ════════════════════════════════════════════════════════
        // 5. PERSONEL (25 kişi — farklı başlangıç tarihleri)
        // ════════════════════════════════════════════════════════
        $this->command->info('👤 Personel oluşturuluyor...');
        $staffRecords = [];
        $roles = ['Kasiyer','Garson','Şef','Müdür','Aşçı','Barista','Temizlik','Depocu','Kurye'];
        for ($i = 0; $i < 25; $i++) {
            $isMale = rand(0, 1);
            $firstName = $isMale ? $this->maleNames[array_rand($this->maleNames)] : $this->femaleNames[array_rand($this->femaleNames)];
            $lastName = $this->surnames[array_rand($this->surnames)];
            $startYear = rand(2021, 2024);
            $branchIdx = min($i % count($branchIds), count($branchIds) - 1);
            // Sonraki şubeler ancak açıldıktan sonra personel alabilir
            $branchOpenDate = Carbon::parse($branchDates[$branchIdx]);
            $staffStart = Carbon::parse(sprintf('%d-%02d-01', max($startYear, $branchOpenDate->year), rand(1, 12)));
            if ($staffStart->lt($branchOpenDate)) $staffStart = $branchOpenDate->copy()->addMonths(rand(0, 3));

            $isActive = $i < 20 || rand(0, 1); // İlk 20 aktif

            $staffId = DB::table('staff')->insertGetId([
                'name' => $firstName . ' ' . $lastName,
                'role' => $roles[array_rand($roles)],
                'branch_id' => $branchIds[$branchIdx],
                'phone' => '05' . rand(30, 59) . rand(1000000, 9999999),
                'email' => Str::slug($firstName, '') . '.' . Str::slug($lastName, '') . '@emarefinance.com',
                'total_sales' => 0,
                'total_transactions' => 0,
                'is_active' => $isActive,
                'created_at' => $staffStart->toDateString(),
                'updated_at' => now(),
            ]);

            $staffRecords[] = [
                'id' => $staffId,
                'name' => $firstName . ' ' . $lastName,
                'branch_idx' => $branchIdx,
                'start_date' => $staffStart,
                'is_active' => $isActive,
            ];
        }

        // ════════════════════════════════════════════════════════
        // 6. ÖDEME TİPLERİ
        // ════════════════════════════════════════════════════════
        $paymentTypes = ['Nakit','Kredi Kartı','Banka Kartı','Havale/EFT','Veresiye','Yemek Kartı','Mobil Ödeme'];
        foreach ($paymentTypes as $idx => $pt) {
            DB::table('payment_types')->insert([
                'name' => $pt,
                'code' => Str::slug($pt),
                'is_active' => true,
                'sort_order' => $idx,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ════════════════════════════════════════════════════════
        // 7. GELİR/GİDER TÜRLERİ
        // ════════════════════════════════════════════════════════
        $this->command->info('📊 Gelir/gider türleri oluşturuluyor...');
        $incomeTypeIds = [];
        foreach ($this->incomeTypes as $type) {
            $incomeTypeIds[] = DB::table('income_expense_types')->insertGetId([
                'name' => $type, 'direction' => 'income', 'is_active' => true,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
        $expenseTypeIds = [];
        foreach ($this->expenseTypes as $type) {
            $expenseTypeIds[] = DB::table('income_expense_types')->insertGetId([
                'name' => $type, 'direction' => 'expense', 'is_active' => true,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        // ════════════════════════════════════════════════════════
        // 8. ANA DÖNGÜ — AY AY VERİ OLUŞTURMA (120 ay)
        // ════════════════════════════════════════════════════════
        $this->command->info('📅 3 yıllık satış, gider, stok verisi üretiliyor (2023-2026)...');
        $this->command->info('   Bu işlem birkaç dakika sürebilir...');

        $totalSales = 0;
        $totalExpenses = 0;
        $totalIncomes = 0;
        $totalPurchases = 0;
        $saleIdCounter = 0;
        $receiptCounter = 0;

        $startDate = Carbon::create(2023, 3, 1);
        $endDate = Carbon::create(2026, 2, 28);

        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $year = $currentDate->year;
            $month = $currentDate->month;

            $inflation = $this->getInflationMultiplier($year);
            $season = $this->getSeasonMultiplier($month);
            $growth = $this->getGrowthMultiplier($year);

            // Hangi şubeler bu tarihte aktif?
            $activeBranches = [];
            foreach ($branches as $idx => $b) {
                if (Carbon::parse($b['created_at'])->lte($currentDate)) {
                    $activeBranches[] = $branchIds[$idx];
                }
            }
            if (empty($activeBranches)) {
                $currentDate->addMonth();
                continue;
            }

            // Bu ay için aktif personel
            $activeStaff = array_filter($staffRecords, function($s) use ($currentDate, $activeBranches, $branchIds) {
                return $s['start_date']->lte($currentDate)
                    && $s['is_active']
                    && in_array($branchIds[$s['branch_idx']], $activeBranches);
            });
            if (empty($activeStaff)) $activeStaff = [$staffRecords[0]];
            $activeStaff = array_values($activeStaff);

            // ── Aylık satış sayısı (büyüme + mevsim + şube sayısı) ──
            $baseSales = 100;
            $branchMultiplier = count($activeBranches) * 0.6 + 0.4;
            $monthlySaleCount = (int) round($baseSales * $season * $growth * $branchMultiplier);
            $monthlySaleCount = max(30, min(450, $monthlySaleCount)); // 30-450 arası

            // Satış verisi batch
            $salesBatch = [];
            $saleItemsBatch = [];
            $stockMovementsBatch = [];
            $accountTransBatch = [];

            for ($s = 0; $s < $monthlySaleCount; $s++) {
                $saleDay = rand(1, (int)$currentDate->copy()->endOfMonth()->day);
                $saleHour = $this->weightedRand([
                    8 => 3, 9 => 5, 10 => 8, 11 => 12, 12 => 18, 13 => 15,
                    14 => 10, 15 => 8, 16 => 7, 17 => 8, 18 => 12, 19 => 15,
                    20 => 14, 21 => 10, 22 => 5, 23 => 2
                ]);
                $saleMinute = rand(0, 59);
                $soldAt = sprintf('%d-%02d-%02d %02d:%02d:%02d', $year, $month, $saleDay, $saleHour, $saleMinute, rand(0, 59));

                $branchId = $activeBranches[array_rand($activeBranches)];
                $staff = $activeStaff[array_rand($activeStaff)];
                $hasCustomer = rand(1, 100) <= 30;
                $customerId = $hasCustomer ? $customerIds[array_rand($customerIds)] : null;

                // 1-6 ürün per satış
                $itemCount = $this->weightedRand([1 => 25, 2 => 35, 3 => 20, 4 => 10, 5 => 7, 6 => 3]);
                $selectedProducts = [];
                $usedProductIds = [];
                for ($i = 0; $i < $itemCount; $i++) {
                    $tries = 0;
                    do {
                        $prod = $productRecords[array_rand($productRecords)];
                        $tries++;
                    } while (in_array($prod['id'], $usedProductIds) && $tries < 10);
                    $usedProductIds[] = $prod['id'];

                    // Fiyatı yıla göre ayarla (güncel fiyat / güncel_enflasyon * o_yıl_enflasyon)
                    $periodPrice = round($prod['current_price'] / $this->getInflationMultiplier(2026) * $inflation, 2);
                    $periodPrice = max(1, $periodPrice); // min 1 TL

                    $qty = $this->weightedRand([1 => 60, 2 => 25, 3 => 10, 4 => 3, 5 => 2]);
                    $discount = (rand(1, 100) <= 15) ? round($periodPrice * $qty * rand(5, 20) / 100, 2) : 0;
                    $vatAmount = round(($periodPrice * $qty - $discount) * $prod['vat'] / (100 + $prod['vat']), 2);
                    $lineTotal = round($periodPrice * $qty - $discount, 2);

                    $selectedProducts[] = [
                        'product' => $prod,
                        'qty' => $qty,
                        'unit_price' => $periodPrice,
                        'discount' => $discount,
                        'vat_rate' => $prod['vat'],
                        'vat_amount' => $vatAmount,
                        'total' => $lineTotal,
                    ];
                }

                $subtotal = array_sum(array_map(fn($p) => $p['unit_price'] * $p['qty'], $selectedProducts));
                $discountTotal = array_sum(array_column($selectedProducts, 'discount'));
                $vatTotal = array_sum(array_column($selectedProducts, 'vat_amount'));
                $grandTotal = array_sum(array_column($selectedProducts, 'total'));

                // Ödeme yöntemi
                $payMethod = $this->weightedRand(['cash' => 35, 'card' => 45, 'mixed' => 10, 'credit' => 10]);
                $cashAmount = 0;
                $cardAmount = 0;
                if ($payMethod == 'cash') { $cashAmount = $grandTotal; }
                elseif ($payMethod == 'card') { $cardAmount = $grandTotal; }
                elseif ($payMethod == 'mixed') {
                    $cashAmount = round($grandTotal * rand(30, 70) / 100, 2);
                    $cardAmount = round($grandTotal - $cashAmount, 2);
                }

                $status = rand(1, 100) <= 3 ? 'cancelled' : (rand(1, 100) <= 2 ? 'refunded' : 'completed');

                $receiptCounter++;
                $receiptNo = 'EF-' . $year . '-' . str_pad($receiptCounter, 7, '0', STR_PAD_LEFT);

                $salesBatch[] = [
                    'receipt_no' => $receiptNo,
                    'branch_id' => $branchId,
                    'customer_id' => $customerId,
                    'user_id' => 1,
                    'payment_method' => $payMethod,
                    'total_items' => $itemCount,
                    'subtotal' => round($subtotal, 2),
                    'vat_total' => round($vatTotal, 2),
                    'discount_total' => round($discountTotal, 2),
                    'discount' => round($discountTotal, 2),
                    'grand_total' => round($grandTotal, 2),
                    'cash_amount' => $cashAmount,
                    'card_amount' => $cardAmount,
                    'status' => $status,
                    'staff_name' => $staff['name'],
                    'sold_at' => $soldAt,
                    'created_at' => $soldAt,
                    'updated_at' => $soldAt,
                    '_items' => $selectedProducts,
                    '_customer_id' => $customerId,
                    '_grand_total' => $grandTotal,
                ];

                $totalSales++;
            }

            // Insert satışları batch olarak
            foreach (array_chunk($salesBatch, 50) as $chunk) {
                foreach ($chunk as $sale) {
                    $items = $sale['_items'];
                    $custId = $sale['_customer_id'];
                    $gt = $sale['_grand_total'];
                    unset($sale['_items'], $sale['_customer_id'], $sale['_grand_total']);

                    $saleId = DB::table('sales')->insertGetId($sale);

                    foreach ($items as $item) {
                        $saleItemsBatch[] = [
                            'sale_id' => $saleId,
                            'product_id' => $item['product']['id'],
                            'product_name' => $item['product']['name'],
                            'barcode' => $item['product']['barcode'],
                            'quantity' => $item['qty'],
                            'unit_price' => $item['unit_price'],
                            'discount' => $item['discount'],
                            'vat_rate' => $item['vat_rate'],
                            'vat_amount' => $item['vat_amount'],
                            'total' => $item['total'],
                            'created_at' => $sale['sold_at'],
                            'updated_at' => $sale['sold_at'],
                        ];

                        // Stok hareketi
                        $stockMovementsBatch[] = [
                            'type' => 'sale',
                            'barcode' => $item['product']['barcode'],
                            'product_id' => $item['product']['id'],
                            'product_name' => $item['product']['name'],
                            'quantity' => -$item['qty'],
                            'unit_price' => $item['unit_price'],
                            'total' => $item['total'],
                            'movement_date' => $sale['sold_at'],
                            'created_at' => $sale['sold_at'],
                            'updated_at' => $sale['sold_at'],
                        ];
                    }

                    // Cari hareket (müşterili satışlar)
                    if ($custId) {
                        $accountTransBatch[] = [
                            'customer_id' => $custId,
                            'type' => 'sale',
                            'amount' => $gt,
                            'balance_after' => 0,
                            'description' => 'Satış: ' . $sale['receipt_no'],
                            'reference' => $sale['receipt_no'],
                            'transaction_date' => $sale['sold_at'],
                            'created_at' => $sale['sold_at'],
                            'updated_at' => $sale['sold_at'],
                        ];
                    }
                }
            }

            // Batch insert
            foreach (array_chunk($saleItemsBatch, 200) as $chunk) {
                DB::table('sale_items')->insert($chunk);
            }
            foreach (array_chunk($stockMovementsBatch, 200) as $chunk) {
                DB::table('stock_movements')->insert($chunk);
            }
            foreach (array_chunk($accountTransBatch, 200) as $chunk) {
                DB::table('account_transactions')->insert($chunk);
            }
            $saleItemsBatch = [];
            $stockMovementsBatch = [];
            $accountTransBatch = [];

            // ── ALIŞ FATURALARI (ayda 5-15) ──
            $purchaseCount = (int) round(rand(5, 10) * count($activeBranches) * 0.5);
            $purchaseBatch = [];
            for ($p = 0; $p < $purchaseCount; $p++) {
                $invDay = rand(1, 28);
                $invDate = sprintf('%d-%02d-%02d', $year, $month, $invDay);
                $firmId = $firmIds[array_rand($firmIds)];
                $branchId = $activeBranches[array_rand($activeBranches)];

                $invItemCount = rand(3, 12);
                $invItems = [];
                $invTotal = 0;
                for ($i = 0; $i < $invItemCount; $i++) {
                    $prod = $productRecords[array_rand($productRecords)];
                    $qty = rand(10, 200);
                    $unitPrice = round($prod['current_price'] * $prod['cost_ratio'] / $this->getInflationMultiplier(2026) * $inflation, 2);
                    $unitPrice = max(0.50, $unitPrice);
                    $lineTotal = round($unitPrice * $qty, 2);
                    $invTotal += $lineTotal;
                    $invItems[] = [
                        'product_id' => $prod['id'],
                        'product_name' => $prod['name'],
                        'barcode' => $prod['barcode'],
                        'quantity' => $qty,
                        'unit_price' => $unitPrice,
                        'total' => $lineTotal,
                        'created_at' => $invDate,
                        'updated_at' => $invDate,
                    ];
                }

                $invoiceNo = 'AF-' . $year . '-' . str_pad(++$totalPurchases, 6, '0', STR_PAD_LEFT);

                $purchaseId = DB::table('purchase_invoices')->insertGetId([
                    'invoice_type' => 'purchase',
                    'invoice_no' => $invoiceNo,
                    'firm_id' => $firmId,
                    'branch_id' => $branchId,
                    'payment_type' => ['cash', 'credit', 'transfer'][array_rand(['cash', 'credit', 'transfer'])],
                    'total_items' => $invItemCount,
                    'total_amount' => round($invTotal, 2),
                    'invoice_date' => $invDate,
                    'created_at' => $invDate,
                    'updated_at' => $invDate,
                ]);

                foreach ($invItems as &$invItem) {
                    $invItem['purchase_invoice_id'] = $purchaseId;
                }
                DB::table('purchase_invoice_items')->insert($invItems);

                // Alış stok hareketleri
                foreach ($invItems as $invItem) {
                    DB::table('stock_movements')->insert([
                        'type' => 'in',
                        'barcode' => $invItem['barcode'],
                        'product_id' => $invItem['product_id'],
                        'product_name' => $invItem['product_name'],
                        'firm_customer' => $this->firmNames[array_rand($this->firmNames)],
                        'quantity' => $invItem['quantity'],
                        'unit_price' => $invItem['unit_price'],
                        'total' => $invItem['total'],
                        'movement_date' => $invDate,
                        'created_at' => $invDate,
                        'updated_at' => $invDate,
                    ]);
                }
            }

            // ── GELİRLER (satış dışı — ayda 2-5) ──
            $monthlyIncomeCount = rand(2, 5);
            for ($i = 0; $i < $monthlyIncomeCount; $i++) {
                $incDay = rand(1, 28);
                $incDate = sprintf('%d-%02d-%02d', $year, $month, $incDay);
                $typeId = $incomeTypeIds[array_rand($incomeTypeIds)];
                $typeName = $this->incomeTypes[array_rand($this->incomeTypes)];
                $amount = round(rand(500, 15000) * $inflation / 3, 2);

                DB::table('incomes')->insert([
                    'income_expense_type_id' => $typeId,
                    'type_name' => $typeName,
                    'note' => $month . '/' . $year . ' ' . $typeName,
                    'amount' => $amount,
                    'payment_type' => ['cash', 'transfer', 'card'][array_rand(['cash', 'transfer', 'card'])],
                    'date' => $incDate,
                    'time' => sprintf('%02d:%02d:00', rand(9, 18), rand(0, 59)),
                    'created_at' => $incDate,
                    'updated_at' => $incDate,
                ]);
                $totalIncomes++;
            }

            // ── GİDERLER (ayda 8-20 — sabit + değişken) ──
            $monthlyExpenseCount = rand(8, 15) + count($activeBranches);
            for ($e = 0; $e < $monthlyExpenseCount; $e++) {
                $expDay = rand(1, 28);
                $expDate = sprintf('%d-%02d-%02d', $year, $month, $expDay);
                $typeIdx = array_rand($expenseTypeIds);
                $typeId = $expenseTypeIds[$typeIdx];
                $typeName = $this->expenseTypes[$typeIdx];

                // Sabit giderler daha yüksek
                $isFixed = in_array($typeName, ['Kira Gideri', 'Personel Maaşı', 'SGK Primi', 'Sigorta']);
                $minAmount = $isFixed ? 3000 : 200;
                $maxAmount = $isFixed ? 50000 : 8000;
                $amount = round(rand($minAmount, $maxAmount) * $inflation / 3, 2);

                DB::table('expenses')->insert([
                    'income_expense_type_id' => $typeId,
                    'type_name' => $typeName,
                    'note' => $month . '/' . $year . ' ' . $typeName . ' ödemesi',
                    'amount' => $amount,
                    'payment_type' => $isFixed ? 'transfer' : ['cash', 'transfer', 'card'][array_rand(['cash', 'transfer', 'card'])],
                    'date' => $expDate,
                    'time' => sprintf('%02d:%02d:00', rand(9, 18), rand(0, 59)),
                    'created_at' => $expDate,
                    'updated_at' => $expDate,
                ]);
                $totalExpenses++;
            }

            // ── STOK SAYIMI (3 ayda bir) ──
            if ($month % 3 == 0) {
                foreach ($activeBranches as $brId) {
                    $countDate = sprintf('%d-%02d-%02d', $year, $month, rand(25, 28));
                    $countId = DB::table('stock_counts')->insertGetId([
                        'branch_id' => $brId,
                        'status' => 'completed',
                        'total_items' => 0,
                        'counted_at' => $countDate,
                        'created_at' => $countDate,
                        'updated_at' => $countDate,
                    ]);

                    // 20-40 ürün sayılır
                    $countItemCount = rand(20, 40);
                    $countItems = [];
                    $sampledProducts = array_rand($productRecords, min($countItemCount, count($productRecords)));
                    if (!is_array($sampledProducts)) $sampledProducts = [$sampledProducts];

                    foreach ($sampledProducts as $pIdx) {
                        $prod = $productRecords[$pIdx];
                        $sysQty = rand(5, 200);
                        $diff = rand(-5, 5);
                        $countedQty = max(0, $sysQty + $diff);
                        $countItems[] = [
                            'stock_count_id' => $countId,
                            'product_id' => $prod['id'],
                            'barcode' => $prod['barcode'],
                            'product_name' => $prod['name'],
                            'system_quantity' => $sysQty,
                            'counted_quantity' => $countedQty,
                            'difference' => $countedQty - $sysQty,
                            'created_at' => $countDate,
                            'updated_at' => $countDate,
                        ];
                    }
                    DB::table('stock_count_items')->insert($countItems);
                    DB::table('stock_counts')->where('id', $countId)->update(['total_items' => count($countItems)]);
                }
            }

            // ── PERSONEL HAREKETLERİ (ayda 5-15) ──
            $motionCount = rand(5, 15);
            $actions = ['delete_item', 'delete_receipt', 'leave_page'];
            $motionBatch = [];
            for ($m = 0; $m < $motionCount; $m++) {
                $staff = $activeStaff[array_rand($activeStaff)];
                $actionDate = sprintf('%d-%02d-%02d %02d:%02d:%02d', $year, $month, rand(1, 28), rand(8, 22), rand(0, 59), rand(0, 59));
                $action = $actions[array_rand($actions)];
                $motionBatch[] = [
                    'staff_id' => $staff['id'],
                    'staff_name' => $staff['name'],
                    'action' => $action,
                    'description' => match($action) {
                        'delete_item' => 'Satıştan ürün silindi',
                        'delete_receipt' => 'Fiş iptal edildi',
                        'leave_page' => 'Kasa ekranından ayrıldı',
                    },
                    'application' => 'POS',
                    'action_date' => $actionDate,
                    'created_at' => $actionDate,
                    'updated_at' => $actionDate,
                ];
            }
            if (!empty($motionBatch)) {
                DB::table('staff_motions')->insert($motionBatch);
            }

            // İlerleme
            $elapsed = round(microtime(true) - $startTime, 1);
            $this->command->info("   ✅ {$month}/{$year} — {$monthlySaleCount} satış, {$purchaseCount} alış, {$monthlyExpenseCount} gider ({$elapsed}s)");

            $currentDate->addMonth();
        }

        // ════════════════════════════════════════════════════════
        // 9. PERSONEL TOPLAMLARINI GÜNCELLE
        // ════════════════════════════════════════════════════════
        $this->command->info('📈 Personel istatistikleri güncelleniyor...');
        $staffSales = DB::table('sales')
            ->select('staff_name', DB::raw('SUM(grand_total) as total_sales'), DB::raw('COUNT(*) as total_transactions'))
            ->groupBy('staff_name')
            ->get();

        foreach ($staffSales as $ss) {
            DB::table('staff')
                ->where('name', $ss->staff_name)
                ->update([
                    'total_sales' => $ss->total_sales,
                    'total_transactions' => $ss->total_transactions,
                ]);
        }

        // ════════════════════════════════════════════════════════
        // 10. MÜŞTERİ BAKİYELERİNİ GÜNCELLE
        // ════════════════════════════════════════════════════════
        $this->command->info('💰 Müşteri bakiyeleri güncelleniyor...');
        $customerBalances = DB::table('account_transactions')
            ->select('customer_id', DB::raw('SUM(amount) as total'))
            ->groupBy('customer_id')
            ->get();

        foreach ($customerBalances as $cb) {
            DB::table('customers')
                ->where('id', $cb->customer_id)
                ->update(['balance' => -$cb->total]); // Borç negatif
        }

        // ════════════════════════════════════════════════════════
        // 11. E-FATURA AYARLARI & ÖRNEK FATURALAR
        // ════════════════════════════════════════════════════════
        $this->command->info('🧾 E-fatura ayarları ve örnek faturalar oluşturuluyor...');

        DB::table('e_invoice_settings')->insert([
            'company_name'     => 'Emare Finance Demo İşletme',
            'tax_number'       => '1234567890',
            'tax_office'       => 'Kadıköy Vergi Dairesi',
            'address'          => 'Caferağa Mah. Moda Cad. No:42 Kadıköy',
            'city'             => 'İstanbul',
            'district'         => 'Kadıköy',
            'phone'            => '02163456789',
            'email'            => 'efatura@emarefinance.com',
            'integrator'       => 'izibiz',
            'sender_alias'     => 'urn:mail:efatura@emarefinance.com',
            'auto_send'        => false,
            'is_active'        => true,
            'default_scenario' => 'commercial',
            'default_currency' => 'TRY',
            'default_vat_rate' => 20,
            'invoice_prefix'   => 'EMR',
            'invoice_counter'  => 10,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        $eInvoiceStatuses  = ['draft', 'sent', 'approved', 'cancelled'];
        $eInvoiceTypes     = ['invoice', 'invoice', 'invoice', 'return', 'withholding']; // ağırlıklı invoice
        $eInvoiceScenarios = ['basic', 'commercial', 'export'];
        $sampleCustomers   = array_slice($customerIds, 0, 20);
        for ($i = 1; $i <= 10; $i++) {
            $invDate   = Carbon::now()->subDays(rand(1, 300));
            $custId    = $sampleCustomers[array_rand($sampleCustomers)];
            $subtotal  = round(rand(500, 10000), 2);
            $vatTotal  = round($subtotal * 0.20, 2);
            $grandTotal = $subtotal + $vatTotal;
            $status    = $eInvoiceStatuses[array_rand($eInvoiceStatuses)];

            $eiId = DB::table('e_invoices')->insertGetId([
                'invoice_no'          => 'EMR2026' . str_pad($i, 9, '0', STR_PAD_LEFT),
                'uuid'                => (string) Str::uuid(),
                'direction'           => 'outgoing',
                'document_type'       => 'fatura',
                'type'                => $eInvoiceTypes[array_rand($eInvoiceTypes)],
                'scenario'            => $eInvoiceScenarios[array_rand($eInvoiceScenarios)],
                'status'              => $status,
                'customer_id'         => $custId,
                'receiver_name'       => 'Alıcı Firma ' . $i,
                'receiver_tax_number' => (string) rand(1000000000, 9999999999),
                'receiver_tax_office' => 'İstanbul Vergi Dairesi',
                'currency'            => 'TRY',
                'exchange_rate'       => 1.0000,
                'subtotal'            => $subtotal,
                'vat_total'           => $vatTotal,
                'discount_total'      => 0,
                'grand_total'         => $grandTotal,
                'vat_rate'            => 20,
                'payment_method'      => ['cash', 'card', 'transfer'][array_rand(['cash', 'card', 'transfer'])],
                'invoice_date'        => $invDate->toDateString(),
                'sent_at'             => in_array($status, ['sent', 'approved']) ? $invDate->copy()->addHours(rand(1, 4))->toDateTimeString() : null,
                'created_at'          => $invDate->toDateTimeString(),
                'updated_at'          => $invDate->toDateTimeString(),
            ]);

            for ($j = 0; $j < rand(2, 4); $j++) {
                $prod      = $productRecords[array_rand($productRecords)];
                $qty       = rand(1, 5);
                $unitPrice = round(rand(100, 2000), 2);
                DB::table('e_invoice_items')->insert([
                    'e_invoice_id' => $eiId,
                    'product_id'   => $prod['id'],
                    'product_name' => $prod['name'],
                    'unit'         => 'Adet',
                    'quantity'     => $qty,
                    'unit_price'   => $unitPrice,
                    'discount'     => 0,
                    'vat_rate'     => 20,
                    'vat_amount'   => round($unitPrice * $qty * 0.20, 2),
                    'total'        => round($unitPrice * $qty * 1.20, 2),
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }
        }
        $this->command->info('   → 10 e-fatura oluşturuldu');

        // ════════════════════════════════════════════════════════
        // 12. KAMPANYALAR & KULLANIM
        // ════════════════════════════════════════════════════════
        $this->command->info('🎯 Kampanyalar oluşturuluyor...');

        $campaignDefs = [
            ['name' => 'Yaz Sezonu İndirimi',     'type' => 'discount',   'discount_type' => 'percentage', 'discount_value' => 15, 'coupon_code' => 'YAZ15',   'starts_at' => '2025-06-01', 'ends_at' => '2025-08-31', 'status' => 'ended'],
            ['name' => 'Öğrenci Kampanyası',       'type' => 'discount',   'discount_type' => 'percentage', 'discount_value' => 10, 'coupon_code' => 'OGRENCI10','starts_at' => '2025-09-01', 'ends_at' => '2026-06-30', 'status' => 'active'],
            ['name' => 'Yılbaşı Şenliği',          'type' => 'seasonal',   'discount_type' => 'percentage', 'discount_value' => 20, 'coupon_code' => 'YB2026',   'starts_at' => '2025-12-20', 'ends_at' => '2026-01-05', 'status' => 'ended'],
            ['name' => '2 Al 1 Öde',               'type' => 'bogo',       'discount_type' => 'buy_x_get_y','discount_value' => 0,  'coupon_code' => null,       'starts_at' => '2026-02-01', 'ends_at' => '2026-03-31', 'status' => 'active'],
            ['name' => 'Hafta Sonu Flaş İndirim',  'type' => 'flash_sale', 'discount_type' => 'percentage', 'discount_value' => 25, 'coupon_code' => 'FLAS25',   'starts_at' => '2026-03-01', 'ends_at' => '2026-03-02', 'status' => 'active'],
            ['name' => 'Sadık Müşteri Bonusu',     'type' => 'loyalty_bonus','discount_type'=> 'fixed_amount','discount_value'=> 50, 'coupon_code' => null,       'starts_at' => '2026-01-01', 'ends_at' => '2026-12-31', 'status' => 'active'],
        ];
        $campaignIds = [];
        foreach ($campaignDefs as $cd) {
            $campaignIds[] = DB::table('campaigns')->insertGetId([
                'name'                => $cd['name'],
                'type'                => $cd['type'],
                'status'              => $cd['status'],
                'discount_type'       => $cd['discount_type'],
                'discount_value'      => $cd['discount_value'],
                'coupon_code'         => $cd['coupon_code'],
                'usage_limit'         => rand(100, 500),
                'usage_count'         => rand(0, 50),
                'min_purchase_amount' => rand(0, 200),
                'starts_at'           => $cd['starts_at'],
                'ends_at'             => $cd['ends_at'],
                'created_at'          => $cd['starts_at'],
                'updated_at'          => now(),
            ]);
        }
        // Örnek kullanımlar
        for ($i = 0; $i < 30; $i++) {
            DB::table('campaign_usages')->insert([
                'campaign_id'      => $campaignIds[array_rand($campaignIds)],
                'customer_id'      => $customerIds[array_rand($customerIds)],
                'discount_applied' => round(rand(10, 200), 2),
                'created_at'       => Carbon::now()->subDays(rand(1, 180))->toDateTimeString(),
                'updated_at'       => now(),
            ]);
        }
        $this->command->info('   → ' . count($campaignIds) . ' kampanya, 30 kullanım oluşturuldu');

        // ════════════════════════════════════════════════════════
        // 13. MÜŞTERİ SEGMENTLERİ
        // ════════════════════════════════════════════════════════
        $this->command->info('📊 Müşteri segmentleri oluşturuluyor...');

        $segmentDefs = [
            ['name' => 'VIP Müşteriler',    'color' => '#f59e0b', 'icon' => 'star',         'type' => 'manual', 'description' => 'En yüksek harcama yapan müşteriler'],
            ['name' => 'Yeni Müşteriler',   'color' => '#10b981', 'icon' => 'user-plus',    'type' => 'auto',   'description' => 'Son 30 günde kaydolan müşteriler'],
            ['name' => 'Pasif Müşteriler',  'color' => '#6b7280', 'icon' => 'user-clock',   'type' => 'auto',   'description' => '90+ gündür alışveriş yapmayan müşteriler'],
            ['name' => 'Kurumsal',          'color' => '#3b82f6', 'icon' => 'building',     'type' => 'manual', 'description' => 'Şirket müşterileri'],
            ['name' => 'Öğrenci',           'color' => '#8b5cf6', 'icon' => 'graduation-cap','type' => 'manual', 'description' => 'Öğrenci indiriminden yararlananlar'],
        ];
        $segmentIds = [];
        foreach ($segmentDefs as $sd) {
            $segmentIds[] = DB::table('customer_segments')->insertGetId([
                'name'           => $sd['name'],
                'description'    => $sd['description'],
                'color'          => $sd['color'],
                'icon'           => $sd['icon'],
                'type'           => $sd['type'],
                'conditions'     => json_encode([]),
                'customer_count' => 0,
                'is_active'      => true,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }
        // Her segmente 5-15 müşteri ekle
        foreach ($segmentIds as $segId) {
            $memberCount = rand(5, 15);
            $shuffled = $customerIds;
            shuffle($shuffled);
            $members = array_slice($shuffled, 0, $memberCount);
            $addedAt = Carbon::now()->subDays(rand(1, 90))->toDateString();
            foreach ($members as $cid) {
                DB::table('customer_segment_members')->insertOrIgnore([
                    'segment_id'  => $segId,
                    'customer_id' => $cid,
                    'added_at'    => $addedAt,
                ]);
            }
            DB::table('customer_segments')->where('id', $segId)->update(['customer_count' => $memberCount]);
        }
        $this->command->info('   → ' . count($segmentIds) . ' segment oluşturuldu');

        // ════════════════════════════════════════════════════════
        // 14. SADAKAT PROGRAMLARI & PUANLAR
        // ════════════════════════════════════════════════════════
        $this->command->info('🌟 Sadakat programları oluşturuluyor...');

        $loyaltyId = DB::table('loyalty_programs')->insertGetId([
            'name'              => 'Emare Puan Programı',
            'description'       => 'Her alışverişte puan kazan, birikmiş puanlarınla indirim kullan.',
            'points_per_currency' => 1.00,  // Her 1 TL için 1 puan
            'currency_per_point'  => 0.01,  // 100 puan = 1 TL
            'min_redeem_points'   => 100,
            'is_active'           => true,
            'created_at'          => '2023-01-01',
            'updated_at'          => now(),
        ]);
        // 50 müşteriye puan hareketi
        $loyaltyCustomers = array_slice($customerIds, 0, 50);
        foreach ($loyaltyCustomers as $lcId) {
            $balance = 0;
            $earnCount = rand(3, 10);
            for ($e = 0; $e < $earnCount; $e++) {
                $pts = rand(10, 500);
                $balance += $pts;
                DB::table('loyalty_points')->insert([
                    'customer_id'        => $lcId,
                    'loyalty_program_id' => $loyaltyId,
                    'points'             => $pts,
                    'type'               => 'earn',
                    'description'        => 'Alışveriş kazanımı',
                    'balance_after'      => $balance,
                    'created_at'         => Carbon::now()->subDays(rand(1, 365))->toDateTimeString(),
                    'updated_at'         => now(),
                ]);
            }
            // %30 ihtimalle harcama da var
            if ($balance >= 100 && rand(1, 100) <= 30) {
                $redeem = min($balance, rand(50, 200));
                $balance -= $redeem;
                DB::table('loyalty_points')->insert([
                    'customer_id'        => $lcId,
                    'loyalty_program_id' => $loyaltyId,
                    'points'             => -$redeem,
                    'type'               => 'redeem',
                    'description'        => 'Puan harcaması',
                    'balance_after'      => $balance,
                    'created_at'         => Carbon::now()->subDays(rand(1, 30))->toDateTimeString(),
                    'updated_at'         => now(),
                ]);
            }
        }
        $this->command->info('   → 1 sadakat programı, 50 müşteriye puan hareketi oluşturuldu');

        // ════════════════════════════════════════════════════════
        // 15. PAZARLAMA MESAJLARI
        // ════════════════════════════════════════════════════════
        $this->command->info('📢 Pazarlama mesajları oluşturuluyor...');

        $messageDefs = [
            ['title' => 'Yaz İndirim Duyurusu',       'channel' => 'sms',      'status' => 'sent',      'recipients' => 120, 'sent' => 118, 'opened' => 0],
            ['title' => 'Yeni Ürün Tanıtımı',          'channel' => 'email',    'status' => 'sent',      'recipients' => 95,  'sent' => 95,  'opened' => 42],
            ['title' => 'Sadık Müşteri Teşekkürü',     'channel' => 'email',    'status' => 'sent',      'recipients' => 50,  'sent' => 50,  'opened' => 28],
            ['title' => 'Hafta Sonu Özel Fırsat',      'channel' => 'sms',      'status' => 'sent',      'recipients' => 200, 'sent' => 197, 'opened' => 0],
            ['title' => 'Bayram Kampanyası Duyurusu',  'channel' => 'whatsapp', 'status' => 'sent',      'recipients' => 80,  'sent' => 79,  'opened' => 61],
            ['title' => 'Mart Ayı Teklifleri',         'channel' => 'email',    'status' => 'scheduled', 'recipients' => 150, 'sent' => 0,   'opened' => 0],
            ['title' => 'Yeni Şube Açılış Haberi',     'channel' => 'sms',      'status' => 'draft',     'recipients' => 0,   'sent' => 0,   'opened' => 0],
        ];
        $messageIds = [];
        foreach ($messageDefs as $md) {
            $sentAt = $md['status'] === 'sent' ? Carbon::now()->subDays(rand(1, 90))->toDateTimeString() : null;
            $messageIds[] = DB::table('marketing_messages')->insertGetId([
                'title'              => $md['title'],
                'content'           => $md['title'] . ' — Detaylı bilgi için mağazamızı ziyaret edin.',
                'channel'            => $md['channel'],
                'status'             => $md['status'],
                'segment_id'         => !empty($segmentIds) ? $segmentIds[array_rand($segmentIds)] : null,
                'total_recipients'   => $md['recipients'],
                'sent_count'         => $md['sent'],
                'delivered_count'    => (int) ($md['sent'] * 0.95),
                'opened_count'       => $md['opened'],
                'scheduled_at'       => $md['status'] === 'scheduled' ? Carbon::now()->addDays(rand(1, 7))->toDateTimeString() : null,
                'sent_at'            => $sentAt,
                'created_at'         => Carbon::now()->subDays(rand(1, 100))->toDateTimeString(),
                'updated_at'         => now(),
            ]);
        }
        // Gönderilmiş mesajlar için log kaydı
        foreach ($messageIds as $idx => $msgId) {
            $msgDef = $messageDefs[$idx];
            if ($msgDef['status'] !== 'sent') continue;
            $logCount = min($msgDef['sent'], 10); // max 10 log per message
            for ($l = 0; $l < $logCount; $l++) {
                DB::table('marketing_message_logs')->insert([
                    'message_id'   => $msgId,
                    'customer_id'  => $customerIds[array_rand($customerIds)],
                    'recipient'    => '05' . rand(30, 59) . rand(1000000, 9999999),
                    'status'       => rand(0, 1) ? 'delivered' : 'sent',
                    'sent_at'      => Carbon::now()->subDays(rand(1, 90))->toDateTimeString(),
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }
        }
        $this->command->info('   → ' . count($messageIds) . ' pazarlama mesajı oluşturuldu');

        // ════════════════════════════════════════════════════════
        // 16. SMS ŞABLONları & SENARYOLARI & LOGLAR
        // ════════════════════════════════════════════════════════
        $this->command->info('📱 SMS şablonları ve senaryoları oluşturuluyor...');

        $smsTemplates = [
            ['name' => 'Hoş Geldiniz',         'code' => 'welcome',       'category' => 'general',  'content' => 'Merhaba {isim}, Emare Finance ailesine hoş geldiniz! İlk alışverişinizde %10 indirim için HOSGELDIN kodunu kullanın.'],
            ['name' => 'Doğum Günü',           'code' => 'birthday',      'category' => 'birthday', 'content' => 'Sayın {isim}, doğum gününüz kutlu olsun! 🎂 Size özel %15 indirim için DOGUMGUN15 kodunu kullanabilirsiniz.'],
            ['name' => 'Sipariş Onayı',        'code' => 'order_confirm', 'category' => 'sales',    'content' => 'Merhaba {isim}, {tutar} TL tutarındaki siparişiniz alındı. Fiş No: {fis_no}'],
            ['name' => 'Ödeme Hatırlatma',     'code' => 'payment_remind','category' => 'payment',  'content' => 'Sayın {isim}, {tutar} TL tutarındaki bakiyenizin bulunmaktadır. Bilgi için: 0212 XXX XX XX'],
            ['name' => 'Kampanya Duyurusu',    'code' => 'campaign',      'category' => 'marketing','content' => 'Merhaba {isim}, bu hafta sonu tüm ürünlerde %{indirim} indirim yapıyoruz! Detaylar: emarefinance.com'],
            ['name' => 'Stok Uyarısı',         'code' => 'low_stock',     'category' => 'general',  'content' => 'Ürün stok uyarısı: {urun_adi} ürününde stok {miktar} adede düştü.'],
            ['name' => 'Hesap Özeti',          'code' => 'account_summary','category'=> 'payment',  'content' => 'Sayın {isim}, {ay} ayı hesap özetiniz: Alışveriş: {alisveris} TL | Ödeme: {odeme} TL | Bakiye: {bakiye} TL'],
        ];
        $smsTemplateIds = [];
        foreach ($smsTemplates as $st) {
            $smsTemplateIds[] = DB::table('sms_templates')->insertGetId([
                'name'       => $st['name'],
                'code'       => $st['code'],
                'content'    => $st['content'],
                'category'   => $st['category'],
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // SMS Senaryoları
        $smsScenarios = [
            ['name' => 'Yeni Üye Karşılama',    'trigger_event' => 'customer_created', 'template_idx' => 0, 'schedule_type' => 'immediate'],
            ['name' => 'Doğum Günü Mesajı',     'trigger_event' => 'birthday',         'template_idx' => 1, 'schedule_type' => 'scheduled'],
            ['name' => 'Ödeme Hatırlatıcı',     'trigger_event' => 'payment_due',      'template_idx' => 3, 'schedule_type' => 'delayed', 'delay' => 1440],
            ['name' => 'Sipariş Bildirimi',     'trigger_event' => 'sale_created',     'template_idx' => 2, 'schedule_type' => 'immediate'],
        ];
        foreach ($smsScenarios as $sc) {
            DB::table('sms_scenarios')->insert([
                'name'           => $sc['name'],
                'trigger_event'  => $sc['trigger_event'],
                'template_id'    => $smsTemplateIds[$sc['template_idx']],
                'target_type'    => 'all',
                'conditions'     => json_encode([]),
                'schedule_type'  => $sc['schedule_type'],
                'delay_minutes'  => $sc['delay'] ?? 0,
                'is_active'      => true,
                'priority'       => 1,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }

        // SMS Logları (son 60 güne dağılmış 50 adet)
        for ($i = 0; $i < 50; $i++) {
            $sentAt  = Carbon::now()->subDays(rand(1, 60))->subHours(rand(0, 12));
            DB::table('sms_logs')->insert([
                'template_id' => $smsTemplateIds[array_rand($smsTemplateIds)],
                'customer_id' => $customerIds[array_rand($customerIds)],
                'phone'       => '05' . rand(30, 59) . rand(1000000, 9999999),
                'content'     => 'Örnek SMS içeriği #' . $i,
                'status'      => ['sent', 'delivered', 'failed'][array_rand(['sent', 'delivered', 'failed'])],
                'sent_at'     => $sentAt->toDateTimeString(),
                'created_at'  => $sentAt->toDateTimeString(),
                'updated_at'  => now(),
            ]);
        }

        // SMS Kara Listesi (5 numara)
        for ($i = 0; $i < 5; $i++) {
            DB::table('sms_blacklist')->insert([
                'phone'      => '05' . rand(30, 59) . rand(1000000, 9999999),
                'reason'     => ['Müşteri isteği', 'Hatalı numara', 'Şikayet'][array_rand(['Müşteri isteği', 'Hatalı numara', 'Şikayet'])],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->command->info('   → ' . count($smsTemplateIds) . ' şablon, 4 senaryo, 50 log oluşturuldu');

        // ════════════════════════════════════════════════════════
        // 17. DİJİTAL EKRAN (SİGNAGE)
        // ════════════════════════════════════════════════════════
        $this->command->info('📺 Dijital ekran verisi oluşturuluyor...');

        // İçerikler
        $signageContents = [
            ['name' => 'Hoş Geldiniz Görseli',    'type' => 'image', 'duration' => 10, 'status' => 'active'],
            ['name' => 'Menü Görseli',             'type' => 'image', 'duration' => 15, 'status' => 'active'],
            ['name' => 'Kampanya Afişi',           'type' => 'image', 'duration' => 8,  'status' => 'active'],
            ['name' => 'Tanıtım Videosu',          'type' => 'video', 'duration' => 30, 'status' => 'active'],
            ['name' => 'Hafta Sonu Özel Fiyatlar', 'type' => 'image', 'duration' => 12, 'status' => 'archived'],
        ];
        $contentIds = [];
        foreach ($signageContents as $sc) {
            $contentIds[] = DB::table('signage_contents')->insertGetId([
                'name'       => $sc['name'],
                'type'       => $sc['type'],
                'duration'   => $sc['duration'],
                'status'     => $sc['status'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Playlist
        $playlistId = DB::table('signage_playlists')->insertGetId([
            'name'        => 'Ana Ekran Listesi',
            'loop'        => true,
            'status'      => 'active',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
        foreach ($contentIds as $sort => $cid) {
            DB::table('signage_playlist_items')->insert([
                'playlist_id' => $playlistId,
                'content_id'  => $cid,
                'sort_order'  => $sort,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        // Cihazlar
        $deviceLocations = ['Giriş Katı', 'Kasa Önü', 'Bekleme Alanı'];
        $deviceIds = [];
        foreach ($deviceLocations as $loc) {
            $deviceIds[] = DB::table('signage_devices')->insertGetId([
                'name'        => $loc . ' Ekranı',
                'location'    => $loc,
                'resolution'  => '1920x1080',
                'orientation' => 'landscape',
                'template'    => 'default',
                'device_type' => 'display',
                'status'      => 'online',
                'ip_address'  => '192.168.' . rand(1, 5) . '.' . rand(10, 50),
                'api_token'   => Str::random(32),
                'brightness'  => 80,
                'volume'      => 50,
                'auto_power'  => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
        // Cihaz-Playlist ilişkisi
        foreach ($deviceIds as $did) {
            DB::table('signage_device_playlist')->insert([
                'device_id'   => $did,
                'playlist_id' => $playlistId,
                'priority'    => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        // Zamanlama
        DB::table('signage_schedules')->insert([
            'name'        => 'Hafta içi gündüz',
            'playlist_id' => $playlistId,
            'days'        => json_encode(['Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt', 'Paz']),
            'time_start'  => '09:00',
            'time_end'    => '22:00',
            'priority'    => 1,
            'is_active'   => true,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
        $this->command->info('   → 5 içerik, 1 playlist, 3 cihaz, 1 zamanlama oluşturuldu');

        // ════════════════════════════════════════════════════════
        // 18. TEKRARLAYAN FATURALAR
        // ════════════════════════════════════════════════════════
        $this->command->info('🔄 Tekrarlayan faturalar oluşturuluyor...');

        $serviceCategories = ['Yazılım Aboneliği', 'Bakım Sözleşmesi', 'Danışmanlık', 'Hosting', 'Sigorta'];
        // Önce service_categories ekle (eğer yoksa)
        $svcCatIds = [];
        foreach ($serviceCategories as $svc) {
            $existing = DB::table('service_categories')->where('name', $svc)->first();
            if ($existing) {
                $svcCatIds[] = $existing->id;
            } else {
                $svcCatIds[] = DB::table('service_categories')->insertGetId([
                    'name'       => $svc,
                    'is_active'  => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $branchId = $branchIds[0];
        $recurringDefs = [
            ['title' => 'POS Yazılım Aylık Lisans',  'freq' => 'monthly', 'amount' => 990,  'svc_idx' => 0, 'status' => 'active', 'start' => '2024-01-01'],
            ['title' => 'Bakım Hizmeti Sözleşmesi',  'freq' => 'monthly', 'amount' => 1500, 'svc_idx' => 1, 'status' => 'active', 'start' => '2023-06-01'],
            ['title' => 'Muhasebe Danışmanlığı',     'freq' => 'monthly', 'amount' => 2500, 'svc_idx' => 2, 'status' => 'active', 'start' => '2022-01-01'],
            ['title' => 'Sunucu Hosting Paketi',     'freq' => 'monthly', 'amount' => 450,  'svc_idx' => 3, 'status' => 'active', 'start' => '2024-03-01'],
            ['title' => 'İşyeri Sigorta Poliçesi',   'freq' => 'annual',  'amount' => 8500, 'svc_idx' => 4, 'status' => 'paused', 'start' => '2025-01-01'],
        ];
        foreach ($recurringDefs as $rd) {
            $tax    = round($rd['amount'] * 0.20, 2);
            $total  = $rd['amount'] + $tax;
            $custId = $customerIds[array_rand($customerIds)];
            $nextDate = Carbon::parse($rd['start'])->addMonth()->toDateString();

            $rInvId = DB::table('recurring_invoices')->insertGetId([
                'title'               => $rd['title'],
                'customer_id'         => $custId,
                'branch_id'           => $branchId,
                'service_category_id' => $svcCatIds[$rd['svc_idx']],
                'frequency'           => $rd['freq'],
                'frequency_day'       => 1,
                'currency'            => 'TRY',
                'subtotal'            => $rd['amount'],
                'tax_total'           => $tax,
                'discount_total'      => 0,
                'grand_total'         => $total,
                'payment_method'      => 'transfer',
                'status'              => $rd['status'],
                'start_date'          => $rd['start'],
                'next_invoice_date'   => $nextDate,
                'invoices_generated'  => rand(1, 24),
                'auto_send'           => false,
                'created_at'          => $rd['start'],
                'updated_at'          => now(),
            ]);

            DB::table('recurring_invoice_items')->insert([
                'recurring_invoice_id' => $rInvId,
                'product_name'         => $rd['title'],
                'unit'                 => 'Adet',
                'quantity'             => 1.000,
                'unit_price'           => $rd['amount'],
                'discount'             => 0,
                'taxes'                => json_encode([['rate' => 20, 'name' => 'KDV']]),
                'tax_amount'           => $tax,
                'total'                => $total,
                'created_at'           => now(),
                'updated_at'           => now(),
            ]);
        }
        $this->command->info('   → ' . count($recurringDefs) . ' tekrarlayan fatura oluşturuldu');

        // ════════════════════════════════════════════════════════
        // 19. GÖREVLER
        // ════════════════════════════════════════════════════════
        $this->command->info('✅ Görevler oluşturuluyor...');

        $taskDefs = [
            ['title' => 'Aylık stok sayımı yap',                'priority' => 'high',   'status' => 'pending',     'days_ahead' => 5],
            ['title' => 'Tedarikçi faturalarını kontrol et',    'priority' => 'medium', 'status' => 'pending',     'days_ahead' => 2],
            ['title' => 'Kasa raporunu muhasebeciye gönder',    'priority' => 'high',   'status' => 'in_progress', 'days_ahead' => 1],
            ['title' => 'Barkod yazıcı bakımı',                 'priority' => 'low',    'status' => 'pending',     'days_ahead' => 14],
            ['title' => 'Yeni kampanya içeriklerini hazırla',   'priority' => 'medium', 'status' => 'in_progress', 'days_ahead' => 7],
            ['title' => 'Kar/zarar raporunu incele',            'priority' => 'high',   'status' => 'completed',   'days_ahead' => -3],
            ['title' => 'Personel maaş ödemelerini yap',        'priority' => 'high',   'status' => 'completed',   'days_ahead' => -5],
            ['title' => 'E-fatura şifresini yenile',            'priority' => 'medium', 'status' => 'pending',     'days_ahead' => 10],
            ['title' => 'Müşteri segmentlerini güncelle',       'priority' => 'low',    'status' => 'pending',     'days_ahead' => 30],
            ['title' => 'Yıllık sigorta poliçesini incele',     'priority' => 'medium', 'status' => 'pending',     'days_ahead' => 45],
        ];
        foreach ($taskDefs as $idx => $td) {
            $dueDate = Carbon::now()->addDays($td['days_ahead'])->toDateString();
            $assignedTo = !empty($staffRecords) ? $staffRecords[$idx % count($staffRecords)]['id'] : null;
            DB::table('tasks')->insert([
                'title'        => $td['title'],
                'priority'     => $td['priority'],
                'status'       => $td['status'],
                'assigned_to'  => $assignedTo,
                'due_date'     => $dueDate,
                'completed_at' => $td['status'] === 'completed' ? Carbon::now()->subDays(abs($td['days_ahead']))->toDateTimeString() : null,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }
        $this->command->info('   → ' . count($taskDefs) . ' görev oluşturuldu');

        // ════════════════════════════════════════════════════════
        // 20. DONANIM CİHAZLARI
        // ════════════════════════════════════════════════════════
        $this->command->info('🖨️  Donanım cihazları oluşturuluyor...');

        $hwDevices = [
            ['name' => 'Fiş Yazıcı - Kasa 1',   'type' => 'printer',    'connection' => 'network',  'protocol' => 'escpos', 'ip' => '192.168.1.100', 'port' => 9100, 'manufacturer' => 'EPSON',  'model' => 'TM-T20III',  'default' => true],
            ['name' => 'Fiş Yazıcı - Kasa 2',   'type' => 'printer',    'connection' => 'network',  'protocol' => 'escpos', 'ip' => '192.168.1.101', 'port' => 9100, 'manufacturer' => 'EPSON',  'model' => 'TM-T20III',  'default' => false],
            ['name' => 'Barkod Okuyucu',         'type' => 'barcode',    'connection' => 'usb',      'protocol' => 'hid',    'ip' => null,            'port' => null, 'manufacturer' => 'Honeywell','model' => '1900GSR',   'default' => true],
            ['name' => 'Barkod Yazıcı',          'type' => 'label',      'connection' => 'usb',      'protocol' => 'zpl',    'ip' => null,            'port' => null, 'manufacturer' => 'Zebra',  'model' => 'ZD421',      'default' => true],
            ['name' => 'Terazisi',               'type' => 'scale',      'connection' => 'serial',   'protocol' => 'rs232',  'ip' => null,            'port' => null, 'manufacturer' => 'Brecknell','model' => 'PS-USB',  'default' => true],
            ['name' => 'Para Çekmecesi',         'type' => 'cashdrawer', 'connection' => 'printer',  'protocol' => 'escpos', 'ip' => null,            'port' => null, 'manufacturer' => 'APG',    'model' => 'Vasario',    'default' => true],
        ];
        foreach ($hwDevices as $hd) {
            DB::table('hardware_devices')->insert([
                'name'         => $hd['name'],
                'type'         => $hd['type'],
                'connection'   => $hd['connection'],
                'protocol'     => $hd['protocol'],
                'manufacturer' => $hd['manufacturer'],
                'model'        => $hd['model'],
                'ip_address'   => $hd['ip'],
                'port'         => $hd['port'],
                'branch_id'    => $branchIds[0],
                'is_default'   => $hd['default'],
                'is_active'    => true,
                'status'       => 'online',
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }
        $this->command->info('   → ' . count($hwDevices) . ' donanım cihazı oluşturuldu');

        // ════════════════════════════════════════════════════════
        // 21. TEKLİFLER
        // ════════════════════════════════════════════════════════
        $this->command->info('📋 Teklifler oluşturuluyor...');

        $quoteStatuses = ['draft', 'sent', 'viewed', 'accepted', 'rejected', 'expired'];
        for ($q = 1; $q <= 12; $q++) {
            $custId    = $customerIds[array_rand($customerIds)];
            $customer  = DB::table('customers')->where('id', $custId)->first();
            $custName  = $customer ? $customer->name : 'Müşteri ' . $custId;
            $issueDate = Carbon::now()->subDays(rand(1, 180));
            $validUntil = $issueDate->copy()->addDays(30);
            $status   = $quoteStatuses[array_rand($quoteStatuses)];
            $quoteNum = 'TEK-' . date('Y') . '-' . str_pad($q, 4, '0', STR_PAD_LEFT);

            $itemCount = rand(2, 5);
            $subtotal  = 0;
            $taxTotal  = 0;
            $items     = [];
            for ($qi = 0; $qi < $itemCount; $qi++) {
                $prod      = $productRecords[array_rand($productRecords)];
                $qty       = rand(1, 10);
                $unitPrice = round($prod['current_price'] * rand(90, 110) / 100, 2);
                $taxRate   = $prod['vat'];
                $lineTotal = round($qty * $unitPrice, 2);
                $taxAmt    = round($lineTotal * $taxRate / 100, 2);
                $subtotal += $lineTotal;
                $taxTotal += $taxAmt;
                $items[]   = [
                    'product_id' => $prod['id'],
                    'name'       => $prod['name'],
                    'quantity'   => $qty,
                    'unit'       => 'Adet',
                    'unit_price' => $unitPrice,
                    'tax_rate'   => $taxRate,
                    'tax_amount' => $taxAmt,
                    'discount_rate' => 0,
                    'discount_amount' => 0,
                    'total'      => $lineTotal + $taxAmt,
                    'sort_order' => $qi,
                ];
            }
            $grandTotal = round($subtotal + $taxTotal, 2);

            $quoteId = DB::table('quotes')->insertGetId([
                'quote_number'  => $quoteNum,
                'customer_id'   => $custId,
                'customer_name' => $custName,
                'title'         => 'Teklif ' . $quoteNum,
                'status'        => $status,
                'subtotal'      => round($subtotal, 2),
                'tax_total'     => round($taxTotal, 2),
                'discount_total'=> 0,
                'grand_total'   => $grandTotal,
                'currency'      => 'TRY',
                'issue_date'    => $issueDate->toDateString(),
                'valid_until'   => $validUntil->toDateString(),
                'sent_at'       => in_array($status, ['sent','viewed','accepted','rejected']) ? $issueDate->copy()->addHours(rand(1,5))->toDateTimeString() : null,
                'accepted_at'   => $status === 'accepted' ? $issueDate->copy()->addDays(rand(1,10))->toDateTimeString() : null,
                'rejected_at'   => $status === 'rejected' ? $issueDate->copy()->addDays(rand(1,10))->toDateTimeString() : null,
                'created_at'    => $issueDate->toDateTimeString(),
                'updated_at'    => now(),
            ]);

            foreach ($items as &$item) {
                $item['quote_id']   = $quoteId;
                $item['created_at'] = now();
                $item['updated_at'] = now();
            }
            DB::table('quote_items')->insert($items);
        }
        $this->command->info('   → 12 teklif oluşturuldu');

        // ════════════════════════════════════════════════════════
        // ÖZET
        // ════════════════════════════════════════════════════════
        $elapsed = round(microtime(true) - $startTime, 1);
        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════════');
        $this->command->info('🎉 ARŞİV VERİSİ OLUŞTURULDU!');
        $this->command->info('═══════════════════════════════════════════');
        $this->command->info("  Şubeler:          " . count($branchIds));
        $this->command->info("  Ürünler:          " . count($productRecords));
        $this->command->info("  Müşteriler:       " . count($customerIds));
        $this->command->info("  Tedarikçiler:     " . count($firmIds));
        $this->command->info("  Personel:         " . count($staffRecords));
        $this->command->info("  Satışlar:         " . number_format($totalSales));
        $this->command->info("  Alış Faturaları:  " . number_format($totalPurchases));
        $this->command->info("  Gelirler:         " . number_format($totalIncomes));
        $this->command->info("  Giderler:         " . number_format($totalExpenses));
        $this->command->info("  Süre:             {$elapsed} saniye");
        $this->command->info('═══════════════════════════════════════════');
    }

    /**
     * Ağırlıklı rastgele seçim
     */
    private function weightedRand(array $weights): int|string
    {
        $total = array_sum($weights);
        $rand = rand(1, $total);
        $cumulative = 0;
        foreach ($weights as $value => $weight) {
            $cumulative += $weight;
            if ($rand <= $cumulative) return $value;
        }
        return array_key_first($weights);
    }
}
