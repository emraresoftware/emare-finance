# 📘 Emare Finance — Genel Bakış

> **Sürüm:** 1.0.0  
> **Tarih:** Mart 2026  
> **Platform:** Web + Mobil

---

## 1. Proje Tanımı

Emare Finance, işletmelerin satış, stok, cari hesap, fatura, gelir/gider ve personel yönetimini tek bir platformdan yapmasını sağlayan kapsamlı bir finansal yönetim sistemidir. Web tabanlı yönetim paneli ile birlikte, hareket halindeyken kullanılabilecek bir mobil uygulama sunar.

---

## 2. Teknoloji Yığını

| Katman | Teknoloji | Sürüm |
|--------|-----------|-------|
| **Backend Framework** | Laravel | 12.x |
| **Programlama Dili** | PHP | 8.5+ |
| **Veritabanı** | SQLite (varsayılan) | — |
| **Frontend (Web)** | Blade + Tailwind CSS 4 + Alpine.js | CDN |
| **Grafikler (Web)** | Chart.js | 4.x |
| **Mobil Framework** | React Native (Expo) | SDK 52 |
| **Mobil Navigasyon** | React Navigation | 7.x |
| **Mobil Grafikler** | react-native-chart-kit | 6.12 |
| **API İletişimi** | RESTful JSON | — |
| **Donanım Sürücüleri** | WebUSB + Web Serial API | — |
| **Barkod Oluşturma** | JsBarcode | 3.x (CDN) |
| **Yazıcı Protokolü** | ESC/POS (WPC1254) | — |

---

## 3. Temel Özellikler

### 📊 Dashboard
- Günlük/haftalık/aylık/toplam ciro
- Son satışlar, düşük stok uyarıları
- Satış grafiği, kategori bazlı dağılım

### 📦 Ürün Yönetimi
- Ürün CRUD (ekleme, düzenleme, silme)
- Kategori/grup yönetimi
- Varyant desteği (alt ürünler)
- Barkod & etiket tasarımcısı
- Terazi barkod desteği
- İade ve iade talepleri takibi
- CSV export

### 💰 Satış Yönetimi
- Satış listesi ve detayları
- Ödeme yöntemleri (Nakit, Kart, Veresiye, Karışık)
- Fiş/fatura bilgileri
- CSV export

### 👥 Cari Hesaplar
- Müşteri/tedarikçi yönetimi
- Hesap hareketleri (alacak/borç)
- Müşteri bazlı satış geçmişi
- CSV export

### 🏢 Firma Yönetimi
- Tedarikçi firma bilgileri
- Firma bazlı fatura takibi

### 🧾 Fatura Yönetimi
- Alış faturaları
- E-Fatura oluşturma ve takip (gelen/giden)
- E-Fatura entegratör ayarları

### 📈 Raporlar (10 farklı rapor)
- Rapor merkezi
- Günlük, tarihsel, ürünsel, grupsal raporlar
- Satış ve kâr analizi
- Korelasyon analizi
- Stok hareket raporu
- Personel hareket raporu

### 🏭 Stok Yönetimi
- Stok hareketleri (giriş/çıkış)
- Stok sayımları
- Düşük stok uyarıları
- Şube bazlı stok takibi

### 💵 Gelir & Gider
- Gelir/gider kayıtları
- Gelir/gider tür yönetimi

### 👤 Personel Yönetimi
- Personel listesi ve detayları
- Personel hareketleri (giriş/çıkış, satış performansı)
- Görev atama ve takibi

### 💳 Ödeme Tipleri
- Ödeme yöntemi tanımları

### 📱 Mobil Uygulama
- Dashboard, satışlar, ürünler, cariler, raporlar, stok
- Arama, filtreleme, sayfalama
- Pull-to-refresh, haptic feedback
- Yapılandırılabilir sunucu bağlantısı

### 🔌 Donanım Sürücüleri (Tak-Çalıştır)
- **108 bilinen cihaz modeli** — 7 kategoride ayrı sürücü kataloğu veritabanı
- Fiş yazıcı desteği (ESC/POS — 20 model: Epson, Star, Bixolon, Citizen, Xprinter, Rongta, SNBC, Sam4s)
- Etiket yazıcı desteği (TSPL/ZPL/EPL — 16 model: Zebra, TSC, Brother, Godex, Xprinter, HPRT)
- A4 yazıcı desteği (System Print — 24 model: HP, Canon, Epson, Brother, Samsung, Kyocera, Xerox)
- Barkod tarayıcı desteği (HID/Keyboard Wedge — 12 model: Symbol, Honeywell, Datalogic, Netum, Newland, Opticon)
- Terazi desteği (CAS/Dibal/DIGI/Mettler/Bizerba/Ohaus/Baykon — 16 model)
- Kasa çekmecesi kontrolü (ESC/POS kick/RJ11 — 10 model: Star, APG, Posiflex, MAKEN, Safescan)
- Müşteri ekranı (VFD/LCD/Pole Display — 10 model: Posiflex, Birch, Epson, Bixolon)
- WebUSB ve Web Serial API ile tak-çalıştır
- A4 fatura/rapor yazdırma (IPP/System Print bridge)
- Gerçek SVG barkod oluşturma (JsBarcode)
- Türkçe karakter desteği (WPC1254)
- Sürücü kataloğu API (arama, filtreleme, model seçimi)

---

## 4. Proje Dizin Yapısı

```
Emare Finance/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── Api/                    ← 6 API controller
│   │       ├── CustomerController.php
│   │       ├── DashboardController.php
│   │       ├── EInvoiceController.php
│   │       ├── FirmController.php
│   │       ├── IncomeExpenseController.php
│   │       ├── PaymentTypeController.php
│   │       ├── ProductController.php
│   │       ├── PurchaseInvoiceController.php
│   │       ├── ReportController.php
│   │       ├── SaleController.php
│   │       ├── StaffController.php
│   │       ├── StockController.php
│   │       ├── TaskController.php
│   │       └── HardwareController.php
│   ├── Models/                         ← 26 model (HardwareDriver dahil)
│   └── Providers/
├── config/                             ← Yapılandırma dosyaları (hardware.php dahil)
├── database/
│   ├── data/hardware-drivers.json      ← 108 cihaz sürücü kataloğu
│   ├── migrations/                     ← 8 migration dosyası
│   └── seeders/                        ← HardwareDriverSeeder dahil
├── resources/
│   └── views/                          ← Blade şablonları
│       ├── layouts/app.blade.php       ← Ana layout
│       ├── dashboard.blade.php
│       ├── products/ (13 view)
│       ├── sales/ (2 view)
│       ├── customers/ (3 view)
│       ├── firms/ (2 view)
│       ├── invoices/ (2 view)
│       ├── einvoices/ (6 view)
│       ├── stock/ (3 view)
│       ├── income-expense/ (3 view)
│       ├── staff/ (3 view)
│       ├── tasks/ (1 view)
│       ├── payment-types/ (1 view)
│       ├── reports/ (10 view)
│       ├── hardware/ (3 view)
│       └── errors/ (5 view)
├── public/
│   └── js/hardware-drivers.js          ← Donanım sürücü kütüphanesi
├── routes/
│   ├── web.php                         ← 88 web route
│   └── api.php                         ← 20 API route
├── mobile/                             ← React Native Expo uygulaması
│   ├── App.js                          ← Navigasyon giriş noktası
│   ├── src/
│   │   ├── api/client.js               ← API istemcisi
│   │   ├── components/ (8 bileşen)
│   │   ├── screens/ (11 ekran)
│   │   ├── theme/index.js
│   │   └── utils/formatters.js
│   └── assets/
└── docs/                               ← Bu dokümantasyon
```

---

## 5. Gereksinimler

### Sunucu (Backend)
- PHP 8.5+
- Composer 2.x
- SQLite3 (veya MySQL 8+ / PostgreSQL 15+)

### Web Tarayıcı
- Chrome, Firefox, Safari, Edge (güncel sürümler)

### Mobil Geliştirme
- Node.js 18+ (v24 desteklenir)
- Expo Go uygulaması (iOS / Android)
- Telefon ve sunucu aynı ağda olmalı

---

## 6. Hızlı Başlangıç

```bash
# 1. Bağımlılıkları yükle
composer install

# 2. Ortam dosyasını oluştur
cp .env.example .env
php artisan key:generate

# 3. Veritabanını hazırla
touch database/database.sqlite
php artisan migrate

# 4. Sunucuyu başlat (web)
php artisan serve --host=0.0.0.0 --port=8000

# 5. Mobil uygulamayı başlat
cd mobile
npm install
npm start
```

---

## 7. Sayfa ve Route Özeti

| Modül | Web Route | API Endpoint | View Sayısı |
|-------|-----------|--------------|-------------|
| Dashboard | 1 | 1 | 1 |
| Ürünler | 17 | 4 | 13 |
| Satışlar | 3 | 3 | 2 |
| Cariler | 6 | 3 | 3 |
| Firmalar | 2 | — | 2 |
| Alış Faturaları | 2 | — | 2 |
| E-Faturalar | 8 | — | 6 |
| Stok | 3 | 3 | 3 |
| Gelir/Gider | 3 | — | 3 |
| Personeller | 3 | — | 3 |
| Raporlar | 10 | 4 | 10 |
| Görevler | 1 | — | 1 |
| Ödeme Tipleri | 1 | — | 1 |
| Donanım | 8 | 7 | 3 |
| **Toplam** | **68** | **25** | **53** |
