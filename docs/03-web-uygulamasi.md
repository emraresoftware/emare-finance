# 🌐 Emare Finance — Web Uygulaması

> **Framework:** Laravel 12 + Blade + Tailwind CSS 4 + Alpine.js  
> **Toplam Route:** 83 | **Toplam View:** ~50 | **Controller:** 13

---

## 1. Layout ve Genel Yapı

### Ana Layout: `layouts/app.blade.php`

Tüm sayfalar bu layout üzerinden yüklenir. İçerir:
- **Sidebar** — Sol menü navigasyonu
- **Header** — Üst bar
- **Content** — `@yield('content')` alanı
- **Footer** — Alt bilgi

**Kullanılan CSS/JS:**
- Tailwind CSS 4 (CDN)
- Alpine.js (CDN)
- Chart.js (CDN, raporlar için)
- JsBarcode 3 (CDN — gerçek SVG barkod oluşturma)
- Font Awesome / Heroicons (ikonlar)
- `hardware-drivers.js` (donanım sürücü kütüphanesi — global yüklenir)

---

## 2. Modül Bazlı Sayfa Detayları

### 2.1 📊 Dashboard

| Route | URL | Controller | Açıklama |
|-------|-----|------------|----------|
| `dashboard` | `GET /` | DashboardController@index | Ana sayfa |

**Gösterir:**
- Toplam satış, ciro, müşteri ve ürün sayısı kartları
- Günlük satış grafiği (Chart.js — 7 gün)
- Kategori bazlı satış dağılımı (pasta grafik)
- Son 5 satış listesi
- Düşük stoklu ürünler uyarısı

---

### 2.2 📦 Ürünler (17 Route, 13 View)

| Route Adı | URL | Metod | Açıklama |
|-----------|-----|-------|----------|
| `products.index` | `GET /urunler` | index | Ürün listesi (arama, kategori filtre, sayfalama) |
| `products.create` | `GET /urunler/ekle` | create | Ürün ekleme formu |
| `products.store` | `POST /urunler/ekle` | store | Yeni ürün kaydet |
| `products.show` | `GET /urunler/{id}` | show | Ürün detay (satış geçmişi, stok durumu) |
| `products.edit` | `GET /urunler/{id}/duzenle` | edit | Ürün düzenleme formu |
| `products.update` | `PUT /urunler/{id}` | update | Ürün güncelle |
| `products.groups` | `GET /urunler/gruplar` | groups | Kategori/grup yönetimi |
| `products.sub_products` | `GET /urunler/alt-urunler` | subProducts | Alt ürün tanımları |
| `products.variants` | `GET /urunler/varyantlar` | variants | Varyant listesi |
| `products.create_variant` | `GET /urunler/varyant-ekle` | createVariant | Varyant ekleme formu |
| `products.store_variant` | `POST /urunler/varyant-ekle` | storeVariant | Varyant kaydet |
| `products.refunds` | `GET /urunler/iadeler` | refunds | İade listesi |
| `products.refund_requests` | `GET /urunler/iade-talepleri` | refundRequests | İade talepleri |
| `products.labels` | `GET /urunler/etiket` | labels | Etiket yazdırma |
| `products.label_designer` | `GET /urunler/etiket-tasarla` | labelDesigner | Etiket tasarımcısı |
| `products.scale_barcode` | `GET /urunler/terazi-barkod` | scaleBarcode | Terazi barkod |
| `products.export` | `GET /urunler/export` | export | CSV export |

**View Dosyaları:**
```
resources/views/products/
├── index.blade.php          ← Ürün listesi
├── show.blade.php           ← Ürün detay
├── create.blade.php         ← Ürün ekleme formu
├── edit.blade.php           ← Ürün düzenleme
├── groups.blade.php         ← Kategori grupları
├── sub-products.blade.php   ← Alt ürünler
├── variants.blade.php       ← Varyantlar
├── create-variant.blade.php ← Varyant ekleme
├── refunds.blade.php        ← İadeler
├── refund-requests.blade.php← İade talepleri
├── labels.blade.php         ← Etiket yazdırma
├── label-designer.blade.php ← Etiket tasarımcısı
└── scale-barcode.blade.php  ← Terazi barkod
```

---

### 2.3 💰 Satışlar (3 Route, 2 View)

| Route Adı | URL | Açıklama |
|-----------|-----|----------|
| `sales.index` | `GET /satislar` | Satış listesi (tarih filtre, ödeme yöntemi, arama) |
| `sales.show` | `GET /satislar/{id}` | Satış detay (ürün kalemleri, ödeme bilgileri) |
| `sales.export` | `GET /satislar/export` | CSV olarak dışa aktar |

---

### 2.4 👥 Cariler (6 Route, 3 View)

| Route Adı | URL | Açıklama |
|-----------|-----|----------|
| `customers.index` | `GET /cariler` | Cari listesi (arama, bakiye durumu) |
| `customers.show` | `GET /cariler/{id}` | Cari detay (hesap hareketleri, satış geçmişi) |
| `customers.edit` | `GET /cariler/{id}/duzenle` | Düzenleme formu |
| `customers.update` | `PUT /cariler/{id}` | Güncelle |
| `customers.export` | `GET /cariler/export` | Cari listesi CSV |
| `customers.export_sales` | `GET /cariler/{id}/export-sales` | Müşteri satışları CSV |

---

### 2.5 🏢 Firmalar (2 Route, 2 View)

| Route Adı | URL | Açıklama |
|-----------|-----|----------|
| `firms.index` | `GET /firmalar` | Tedarikçi firma listesi |
| `firms.show` | `GET /firmalar/{id}` | Firma detay (alış faturaları) |

---

### 2.6 🧾 Alış Faturaları (2 Route, 2 View)

| Route Adı | URL | Açıklama |
|-----------|-----|----------|
| `invoices.index` | `GET /alis-faturalari` | Fatura listesi |
| `invoices.show` | `GET /alis-faturalari/{id}` | Fatura detay (kalemler) |

---

### 2.7 📄 E-Faturalar (8 Route, 6 View)

| Route Adı | URL | Açıklama |
|-----------|-----|----------|
| `einvoices.index` | `GET /e-faturalar` | E-fatura ana sayfa |
| `einvoices.outgoing` | `GET /e-faturalar/giden` | Giden e-faturalar |
| `einvoices.incoming` | `GET /e-faturalar/gelen` | Gelen e-faturalar |
| `einvoices.create` | `GET /e-faturalar/olustur` | Yeni e-fatura oluştur |
| `einvoices.store` | `POST /e-faturalar/olustur` | E-fatura kaydet |
| `einvoices.show` | `GET /e-faturalar/{id}` | E-fatura detay |
| `einvoices.settings` | `GET /e-faturalar/ayarlar` | Entegratör ayarları |
| `einvoices.settings.update` | `POST /e-faturalar/ayarlar` | Ayarları güncelle |

---

### 2.8 🏭 Stok Yönetimi (3 Route, 3 View)

| Route Adı | URL | Açıklama |
|-----------|-----|----------|
| `stock.movements` | `GET /stok/hareketler` | Stok giriş/çıkış hareketleri |
| `stock.counts` | `GET /stok/sayim` | Stok sayım listesi |
| `stock.count_show` | `GET /stok/sayim/{id}` | Sayım detay (farklar) |

---

### 2.9 💵 Gelir & Gider (3 Route, 3 View)

| Route Adı | URL | Açıklama |
|-----------|-----|----------|
| `income_expense.incomes` | `GET /gelir-gider/gelirler` | Gelir kayıtları |
| `income_expense.expenses` | `GET /gelir-gider/giderler` | Gider kayıtları |
| `income_expense.types` | `GET /gelir-gider/turler` | Gelir/gider türleri |

---

### 2.10 👤 Personel (3 Route, 3 View)

| Route Adı | URL | Açıklama |
|-----------|-----|----------|
| `staff.index` | `GET /personeller` | Personel listesi |
| `staff.show` | `GET /personeller/{id}` | Personel detay (satış performansı) |
| `staff.motions` | `GET /personeller/hareketler` | Personel hareketleri |

---

### 2.11 📈 Raporlar (10 Route, 10 View)

| Route Adı | URL | Açıklama |
|-----------|-----|----------|
| `reports.index` | `GET /raporlar` | Rapor merkezi (tüm raporlara erişim) |
| `reports.daily` | `GET /raporlar/gunluk` | Günlük satış raporu |
| `reports.historical` | `GET /raporlar/tarihsel` | Tarihsel karşılaştırma |
| `reports.products` | `GET /raporlar/urunsel` | Ürün bazlı rapor |
| `reports.groups` | `GET /raporlar/grupsal` | Kategori bazlı rapor |
| `reports.sales` | `GET /raporlar/satislar` | Satış analiz raporu |
| `reports.profit` | `GET /raporlar/kar` | Kâr analizi |
| `reports.correlation` | `GET /raporlar/korelasyon` | Ürün korelasyon analizi |
| `reports.stock_movement` | `GET /raporlar/stok-hareket` | Stok hareket raporu |
| `reports.staff_movement` | `GET /raporlar/personel-hareket` | Personel hareket raporu |

---

### 2.12 Diğer (2 Route, 2 View)

| Route Adı | URL | Açıklama |
|-----------|-----|----------|
| `tasks.index` | `GET /gorevler` | Görev yönetimi |
| `payment_types.index` | `GET /odeme-tipleri` | Ödeme tipi tanımları |

---

### 2.13 🔌 Donanım (8 Route + 7 API, 3 View)

| Route Adı | URL | Metod | Açıklama |
|-----------|-----|-------|----------|
| `hardware.index` | `/donanim` | GET | Cihaz yönetim paneli |
| `hardware.create` | `/donanim/ekle` | GET | Cihaz ekleme formu (driver DB ile) |
| `hardware.store` | `/donanim/ekle` | POST | Cihaz kaydet |
| `hardware.edit` | `/donanim/{id}/duzenle` | GET | Cihaz düzenleme |
| `hardware.update` | `/donanim/{id}` | PUT | Cihaz güncelle |
| `hardware.destroy` | `/donanim/{id}` | DELETE | Cihaz sil |
| `hardware.status` | `/donanim/{id}/status` | POST | Durum güncelle (AJAX) |
| `hardware.set_default` | `/donanim/{id}/default` | POST | Varsayılan yap (AJAX) |

**API Endpointleri (7 adet):**
| URL | Metod | Açıklama |
|-----|-------|----------|
| `/api/hardware/devices` | GET | Aktif cihaz listesi (JSON) |
| `/api/hardware/print-network` | POST | Ağ yazıcıya yazdır (TCP proxy) |
| `/api/hardware/drivers` | GET | Sürücü kataloğu arama/filtreleme |
| `/api/hardware/drivers/stats` | GET | Sürücü istatistikleri |
| `/api/hardware/drivers/manufacturers` | GET | Üretici listesi (türe göre) |
| `/api/hardware/drivers/models` | GET | Üreticiye göre modeller |
| `/api/hardware/drivers/{id}` | GET | Tek sürücü detayı |

**View Dosyaları:**
```
resources/views/hardware/
├── index.blade.php   ← Cihaz listesi + istatistik + tarayıcı uyumluluk
├── create.blade.php  ← Yeni cihaz ekleme (bilinen cihaz veritabanı ile)
└── edit.blade.php    ← Cihaz düzenleme
```

**Desteklenen Cihazlar (108 model — Sürücü Kataloğu DB):**
- Fiş Yazıcı: Epson, Star, Bixolon, Citizen, Xprinter, Rongta, SNBC, Sam4s (20 model)
- Etiket Yazıcı: Zebra, TSC, Brother, Godex, Xprinter, HPRT (16 model)
- A4 Yazıcı: HP, Canon, Epson, Brother, Samsung, Kyocera, Xerox (24 model)
- Barkod Tarayıcı: Symbol, Honeywell, Datalogic, Netum, Newland, Opticon (12 model)
- Terazi: CAS, Dibal, DIGI, Mettler Toledo, Bizerba, Ohaus, Baykon (16 model)
- Kasa Çekmecesi: Star, APG, Posiflex, MAKEN, Safescan, VPOS (10 model)
- Müşteri Ekranı: Posiflex, Birch, Epson, Bixolon, Firich, Custom (10 model)

**Sayfa Entegrasyonları:**
| Sayfa | Donanım Özelliği |
|-------|------------------|
| Satış Detay | Fiş yazdır (ESC/POS) + Çekmece aç + A4 fatura yazdır |
| Etiketler | Etiket yazıcıya gönder (TSPL/ZPL) |
| Etiket Tasarımcısı | Etiket yazıcıya gönder |
| Terazi Barkod | Terazi bağla (Seri port) |
| Raporlar | A4 rapor yazdır (System Print) |
| Tüm Sayfalar | Barkod tarayıcı dinleme (Keyboard wedge) |

---

## 3. Hata Sayfaları

```
resources/views/errors/
├── 403.blade.php    ← Erişim reddedildi
├── 404.blade.php    ← Sayfa bulunamadı
├── 419.blade.php    ← Oturum süresi doldu
├── 429.blade.php    ← Çok fazla istek
└── 500.blade.php    ← Sunucu hatası
```

---

## 4. Sidebar Menü Yapısı

```
📊 Ana Sayfa
📦 Ürünler
   ├── Ürün Listesi
   ├── Ürün Ekle
   ├── Gruplar
   ├── Alt Ürünler
   ├── Varyantlar
   ├── İadeler
   ├── İade Talepleri
   ├── Etiketler
   ├── Etiket Tasarla
   └── Terazi Barkod
💰 Satışlar
👥 Cariler
🏢 Firmalar
🧾 Alış Faturaları
📄 E-Faturalar
   ├── Genel Bakış
   ├── Giden
   ├── Gelen
   ├── Oluştur
   └── Ayarlar
🏭 Stok
   ├── Hareketler
   └── Sayım
💵 Gelir & Gider
   ├── Gelirler
   ├── Giderler
   └── Türler
👤 Personeller
   ├── Liste
   └── Hareketler
📋 Görevler
💳 Ödeme Tipleri
� Donanım
�📈 Raporlar
   ├── Rapor Merkezi
   ├── Günlük
   ├── Tarihsel
   ├── Ürünsel
   ├── Grupsal
   ├── Satışlar
   ├── Kâr
   ├── Korelasyon
   ├── Stok Hareket
   └── Personel Hareket
```

---

## 5. Export (Dışa Aktarma) Özellikleri

| Modül | URL | Format | Açıklama |
|-------|-----|--------|----------|
| Ürünler | `/urunler/export` | CSV | Tüm ürün listesi |
| Satışlar | `/satislar/export` | CSV | Satış verileri |
| Cariler | `/cariler/export` | CSV | Müşteri listesi |
| Cari Satışları | `/cariler/{id}/export-sales` | CSV | Belirli müşterinin satışları |
