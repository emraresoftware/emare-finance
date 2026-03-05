# 🔌 Emare Finance — Donanım Sürücüleri (Tak-Çalıştır)

> **Donanım Entegrasyonu:** WebUSB + Web Serial API + ESC/POS  
> **Bilinen Cihaz Modeli:** 108 (7 kategoride)  
> **Tarih:** Mart 2026

## 1. Genel Bakış

Emare Finance, POS çevre birimlerini tarayıcı üzerinden doğrudan kontrol edebilen kapsamlı bir donanım sürücü sistemine sahiptir. Sistem **WebUSB API**, **Web Serial API** ve **ESC/POS** protokolü kullanarak yazıcılar, teraziler, barkod tarayıcılar ve kasa çekmeceleri ile tak-çalıştır entegrasyonu sağlar.

**108 bilinen cihaz modeli**, 7 kategoride ayrı bir sürücü kataloğu veritabanında (`hardware_drivers` tablosu) saklanır. Bu katalog, cihaz eklerken dinamik üretici→model seçimi için kullanılır ve API üzerinden erişilebilir.

> ⚠️ **Tarayıcı Uyumluluğu:** WebUSB ve Web Serial API yalnızca **Chrome** ve **Edge** tarayıcılarda desteklenir. Safari ve Firefox desteği yoktur.

---

## 2. Desteklenen Cihaz Türleri

| Tür | İkon | Protokoller | Bağlantı | Model Sayısı |
|-----|------|-------------|----------|-------------|
| Fiş Yazıcı | 🖨️ | ESC/POS, Star, Citizen | USB, Ağ (TCP/9100), Seri | 20 |
| Etiket Yazıcı | 🏷️ | TSPL, ZPL, EPL | USB, Ağ, Seri | 16 |
| A4 Yazıcı | 📄 | System (IPP) | USB, Ağ (WiFi/Ethernet) | 24 |
| Barkod Tarayıcı | 📱 | Keyboard Wedge, HID, Seri | USB HID, Seri, Bluetooth | 12 |
| Terazi | ⚖️ | CAS, Dibal, DIGI, Mettler | Seri (RS-232), USB | 16 |
| Kasa Çekmecesi | 💰 | ESC/POS Kick, RJ11 | USB (yazıcı üzerinden) | 10 |
| Müşteri Ekranı | 🖥️ | VFD, LCD, Pole Display | USB, Seri | 10 |

---

## 3. Bilinen Cihaz Veritabanı (Sürücü Kataloğu)

> **Toplam:** 108 cihaz modeli | **Tablo:** `hardware_drivers` | **Kaynak:** `database/data/hardware-drivers.json`

### Fiş Yazıcılar (20 model)
- **Epson:** TM-T20II, TM-T88V, TM-T88VI, TM-m30, TM-T20III
- **Star:** TSP100, TSP143IIIU, mC-Print3
- **Bixolon:** SRP-350III, SRP-350plusIII, SRP-330III
- **Citizen:** CT-S310II, CT-E351
- **Xprinter:** XP-80C, XP-Q200, XP-58IIH
- **Rongta:** RP80, RP326
- **SNBC:** BTP-R880NP
- **Sam4s:** Ellix 40

### Etiket Yazıcılar (16 model)
- **Zebra:** ZD220, GK420d, ZD420, ZD620, GC420t
- **TSC:** TDP-225, TE200, TE310, TTP-244 Pro
- **Brother:** QL-800, QL-820NWB
- **Godex:** G500, RT700i
- **Xprinter:** XP-360B, XP-420B
- **HPRT:** HT300

### A4 Yazıcılar (24 model)
- **HP:** LaserJet Pro M15w, M28w, M404dn, M428fdw, Color LaserJet M255dw, OfficeJet Pro 9015e
- **Canon:** imageCLASS LBP226dw, MF445dw, LBP6030, PIXMA G3020
- **Epson:** EcoTank L3250, L5290, WorkForce WF-2850
- **Brother:** HL-L2350DW, HL-L2395DW, MFC-L2710DW, HL-L3270CDW
- **Samsung:** Xpress M2020W, M2070FW
- **Kyocera:** ECOSYS P2040dn, M2135dn
- **Xerox:** Phaser 3020, B210, C230

### Barkod Tarayıcılar (12 model)
- **Symbol/Zebra:** DS2208, LS2208, DS4308
- **Honeywell:** Voyager 1400g, 1250g, Xenon 1900g
- **Datalogic:** QuickScan QW2120, QD2500
- **Netum:** NT-1228BL, NT-L6X
- **Newland:** HR22 Dorado
- **Opticon:** OPI-3601

### Elektronik Teraziler (16 model)
- **CAS:** SW-1S, ER Junior, PR-II, CL-5200
- **Dibal:** G-310, G-325, M-525
- **DIGI:** SM-100, SM-5100, DS-781B
- **Mettler Toledo:** bRite Standard, bRite Advanced, Ariva-S
- **Bizerba:** SC-II 800
- **Ohaus:** Ranger 7000
- **Baykon:** BX-11

### Kasa Çekmeceleri (10 model)
- **Star:** CD3-1616
- **APG:** Vasario VB320, VB554A
- **Posiflex:** CR-4100, CR-3100
- **MAKEN:** EK-330, EK-410
- **Partner Tech:** CD-101
- **Safescan:** 4100
- **VPOS:** EC-410

### Müşteri Ekranları (10 model)
- **Posiflex:** PD-2605, PD-2608
- **Birch:** BDM-2000
- **Logic Controls:** PD6200
- **Epson:** DM-D110, DM-D30
- **Partner Tech:** CD-7220
- **Bixolon:** BCD-1100
- **Firich:** FV-2030
- **Custom:** VKP80

---

## 4. Dosya Yapısı

```
config/hardware.php                    # Donanım konfigürasyonu (7 cihaz türü + 80+ known_device)
app/Models/HardwareDevice.php          # Aktif cihazlar için Eloquent model
app/Models/HardwareDriver.php          # Sürücü kataloğu Eloquent model (108 cihaz)
app/Http/Controllers/HardwareController.php  # CRUD + 7 API endpoint
public/js/hardware-drivers.js          # JavaScript sürücü kütüphanesi (~1300 satır)
resources/views/hardware/
    index.blade.php                    # Cihaz yönetim paneli
    create.blade.php                   # Cihaz ekleme formu (driver DB ile dinamik seçim)
    edit.blade.php                     # Cihaz düzenleme formu
database/data/hardware-drivers.json    # 108 cihaz JSON kataloğu (kaynak veri)
database/migrations/
    2026_03_01_000002_create_hardware_devices_table.php
    2026_03_01_000003_create_hardware_drivers_table.php
database/seeders/
    HardwareDriverSeeder.php           # JSON → DB yükleyici
```

---

## 5. Rotalar

### Web Rotaları (8 adet)

| Metod | URL | İsim | Açıklama |
|-------|-----|------|----------|
| GET | `/donanim` | hardware.index | Cihaz listesi |
| GET | `/donanim/ekle` | hardware.create | Cihaz ekleme formu |
| POST | `/donanim/ekle` | hardware.store | Cihaz kaydet |
| GET | `/donanim/{device}/duzenle` | hardware.edit | Cihaz düzenleme |
| PUT | `/donanim/{device}` | hardware.update | Cihaz güncelle |
| DELETE | `/donanim/{device}` | hardware.destroy | Cihaz sil |
| POST | `/donanim/{device}/status` | hardware.status | Durum güncelle (AJAX) |
| POST | `/donanim/{device}/default` | hardware.set_default | Varsayılan yap (AJAX) |

### API Rotaları (7 adet)

| Metod | URL | Açıklama |
|-------|-----|----------|
| GET | `/api/hardware/devices` | Aktif cihaz listesi (JSON) |
| POST | `/api/hardware/print-network` | Ağ yazıcıya yazdır (TCP proxy) |
| GET | `/api/hardware/drivers` | Sürücü kataloğu arama/filtreleme |
| GET | `/api/hardware/drivers/stats` | Sürücü istatistikleri |
| GET | `/api/hardware/drivers/manufacturers` | Üretici listesi (türe göre) |
| GET | `/api/hardware/drivers/models` | Üreticiye göre model listesi |
| GET | `/api/hardware/drivers/{id}` | Tek sürücü detayı |

---

## 6. JavaScript Sürücü Kütüphanesi

### Global Nesneler

```javascript
window.hw              // HardwareManager instance
window.ESCPOS          // ESC/POS komut sabitleri
window.encodeTurkish   // Türkçe karakter encode fonksiyonu
```

### Temel Kullanım

#### Fiş Yazdırma
```javascript
const connId = await window.hw.connectUSB('receipt_printer');
await window.hw.printReceipt({
    header: { company: 'Mağaza Adı', address: 'Adres' },
    receiptNo: 'F-001',
    date: '01.01.2026 12:00',
    cashier: 'Personel',
    items: [
        { name: 'Ürün 1', quantity: 2, price: 10.00, total: 20.00 }
    ],
    subtotal: 20.00,
    vat: 3.60,
    total: 23.60,
    paymentMethod: 'Nakit'
}, connId);
```

#### Etiket Yazdırma
```javascript
const connId = await window.hw.connectUSB('label_printer');
await window.hw.printLabels([
    { name: 'Ürün Adı', barcode: '8690123456789', price: '₺15.90' }
], { width: 50, height: 30, protocol: 'tspl' }, connId);
```

#### Terazi Okuma
```javascript
const connId = await window.hw.connectSerial({ baudRate: 9600 });
window.hw.startScalePolling(function(reading) {
    console.log(reading.weight, reading.unit, reading.stable);
}, 500, 'cas', connId);
```

#### Kasa Çekmecesi Açma
```javascript
const connId = await window.hw.connectUSB('receipt_printer');
await window.hw.openCashDrawer(2, connId); // Pin 2
```

#### A4 Fatura Yazdırma
```javascript
await window.hw.printA4({
    type: 'invoice',
    title: 'Satış Faturası',
    data: {
        companyName: 'Mağaza Adı',
        companyAddress: 'Adres bilgisi',
        taxId: '1234567890',
        invoiceNo: 'FTR-2026-001',
        date: '01.03.2026',
        customerName: 'Müşteri Adı',
        items: [
            { name: 'Ürün 1', qty: 2, price: 50.00, total: 100.00 },
            { name: 'Ürün 2', qty: 1, price: 75.00, total: 75.00 }
        ],
        subtotal: 175.00,
        tax: 35.00,
        total: 210.00,
        paymentMethod: 'Kredi Kartı'
    }
}, { orientation: 'portrait', paperSize: 'A4' });
```

#### A4 Rapor Yazdırma
```javascript
await window.hw.printA4({
    type: 'report',
    title: 'Günlük Satış Raporu',
    data: {
        title: 'Günlük Satış Raporu',
        dateRange: '01.03.2026',
        summary: { 'Toplam Satış': '42', 'Ciro': '₺15.680' },
        columns: [
            { key: 'product', label: 'Ürün' },
            { key: 'qty', label: 'Miktar', align: 'right' },
            { key: 'total', label: 'Tutar', align: 'right' }
        ],
        rows: [
            { product: 'Ürün A', qty: 15, total: '₺750' },
            { product: 'Ürün B', qty: 8, total: '₺400' }
        ]
    }
});
```

#### Sürücü Kataloğu API Kullanımı
```javascript
// Türe göre üreticileri getir
const manufacturers = await window.hw.getDriverManufacturers('receipt_printer');

// Üreticiye göre modelleri getir
const models = await window.hw.getDriverModels('Epson', 'receipt_printer');

// Serbest metin arama
const results = await window.hw.searchDrivers('zebra', 'label_printer');

// İstatistikler
const stats = await window.hw.getDriverStats();
```

#### Barkod Tarayıcı Dinleme
```javascript
// Otomatik olarak layout'ta aktif
// Keyboard wedge modunda hızlı tuş vuruşlarını algılar
window.hw.startBarcodeListener(function(barcode) {
    console.log('Okunan barkod:', barcode);
});
```

---

## 7. ESC/POS Protokolü

### Türkçe Karakter Desteği

Sistem, WPC1254 (Windows-1254) kod sayfası kullanarak Türkçe karakterleri destekler:

| Karakter | WPC1254 Kodu |
|----------|-------------|
| ç | 0xE7 |
| Ç | 0xC7 |
| ğ | 0xF0 |
| Ğ | 0xD0 |
| ı | 0xFD |
| İ | 0xDD |
| ö | 0xF6 |
| Ö | 0xD6 |
| ş | 0xFE |
| Ş | 0xDE |
| ü | 0xFC |
| Ü | 0xDC |

### Komut Referansı

| Komut | Hex Kodu | Açıklama |
|-------|----------|----------|
| INIT | `1B 40` | Yazıcıyı başlat |
| ALIGN_LEFT | `1B 61 00` | Sola hizala |
| ALIGN_CENTER | `1B 61 01` | Ortala |
| ALIGN_RIGHT | `1B 61 02` | Sağa hizala |
| BOLD_ON | `1B 45 01` | Kalın yazı aç |
| BOLD_OFF | `1B 45 00` | Kalın yazı kapat |
| FONT_A | `1B 4D 00` | Normal font |
| FONT_B | `1B 4D 01` | Küçük font |
| CUT | `1D 56 42 03` | Kağıt kes |
| KICK_PIN2 | `1B 70 00 19 FA` | Çekmece aç (pin 2) |
| KICK_PIN5 | `1B 70 01 19 FA` | Çekmece aç (pin 5) |

---

## 8. Veritabanı Şeması

### hardware_devices Tablosu (Aktif Cihazlar)

> Kullanıcının sisteme eklediği ve aktif olarak kullandığı cihazlar.

| Kolon | Tip | Açıklama |
|-------|-----|----------|
| id | INTEGER | Primary key |
| name | VARCHAR | Cihaz adı |
| type | VARCHAR | Cihaz türü |
| connection | VARCHAR | Bağlantı türü (usb/network/serial/bluetooth) |
| protocol | VARCHAR | İletişim protokolü |
| model | VARCHAR | Model adı |
| manufacturer | VARCHAR | Üretici |
| vendor_id | VARCHAR | USB Vendor ID |
| product_id | VARCHAR | USB Product ID |
| ip_address | VARCHAR | Ağ IP adresi |
| port | INTEGER | TCP port |
| serial_port | VARCHAR | Seri port yolu |
| baud_rate | INTEGER | Baud hızı |
| mac_address | VARCHAR | Bluetooth MAC |
| settings | JSON | Ek ayarlar |
| is_default | BOOLEAN | Varsayılan cihaz mı |
| is_active | BOOLEAN | Aktif mi |
| last_seen_at | DATETIME | Son bağlantı zamanı |
| status | VARCHAR | connected/disconnected/error |
| branch_id | FK | Şube ilişkisi |

### hardware_drivers Tablosu (Sürücü Kataloğu)

> 108 bilinen POS cihazının referans veritabanı.

| Kolon | Tip | Açıklama |
|-------|-----|----------|
| id | INTEGER | Primary key |
| device_type | VARCHAR (idx) | Cihaz türü (7 kategori) |
| manufacturer | VARCHAR (idx) | Üretici adı |
| model | VARCHAR | Model adı |
| vendor_id | VARCHAR | USB Vendor ID (hex) |
| product_id | VARCHAR | USB Product ID (hex) |
| protocol | VARCHAR | İletişim protokolü |
| connections | JSON | Desteklenen bağlantı türleri |
| features | JSON | Cihaz özellikleri |
| specs | JSON | Teknik özellikler |
| notes | TEXT | Ek notlar |
| timestamps | DATETIME | Oluşturulma/güncellenme |

---

## 9. Entegrasyonlar

### Sayfalardaki Entegrasyonlar

| Sayfa | Özellik |
|-------|---------|
| **Satış Detay** (`sales/show`) | Fiş yazdır (ESC/POS), Çekmece aç, A4 fatura yazdır |
| **Etiketler** (`products/labels`) | Etiket yazıcıya gönder (TSPL/ZPL) |
| **Etiket Tasarımcısı** (`products/label-designer`) | Etiket yazıcıya gönder |
| **Terazi Barkod** (`products/scale-barcode`) | Terazi bağla (Seri port) |
| **Tüm Sayfalar** (Layout) | Barkod tarayıcı dinleme (Keyboard wedge) |

### Global Barkod Tarayıcı

Layout dosyasında (`layouts/app.blade.php`) otomatik olarak yüklenen barkod tarayıcı dinleyicisi:
- Keyboard wedge modunda çalışan barkod tarayıcıları otomatik algılar
- Hızlı tuş vuruşlarını (<50ms aralık) barkod olarak tanımlar
- Sayfadaki arama inputlarını otomatik doldurur
- Enter tuşuyla formu otomatik submit eder

### JsBarcode CDN

Tüm sayfalarda gerçek SVG barkodlar oluşturmak için JsBarcode 3 kütüphanesi CDN üzerinden yüklüdür:
- EAN-13 (13 haneli barkodlar)
- EAN-8 (8 haneli barkodlar)
- CODE128 (diğer tüm barkodlar)

---

## 10. Donanım Kurulum ve Yapılandırma

### 1. Migration Çalıştırma
```bash
php artisan migrate --path=database/migrations/2026_03_01_000002_create_hardware_devices_table.php
php artisan migrate --path=database/migrations/2026_03_01_000003_create_hardware_drivers_table.php
```

### 2. Sürücü Kataloğunu Yükle
```bash
php artisan db:seed --class=HardwareDriverSeeder
```

### 3. Cihaz Ekleme
1. Sidebar'dan **Donanım** menüsüne tıklayın
2. **+ Yeni Cihaz Ekle** butonuna basın
3. Cihaz türünü seçin
4. Bilinen cihazlar listesinden model seçin (otomatik doldurulur)
5. Bağlantı türünü ve detaylarını girin
6. Kaydedin

### 4. Cihaz Test
1. Donanım panelinde cihazın yanındaki **Bağlan** butonuna tıklayın
2. Tarayıcı USB/Seri izin diyaloğunu gösterecektir
3. Cihazı seçin ve bağlantıyı onaylayın
4. **Test** butonuyla test sayfası yazdırın

### 5. Ağ Yazıcı Yapılandırması
Ağ yazıcılar PHP socket proxy üzerinden çalışır:
- IP adresi ve port (genellikle 9100) girin
- Sunucu, yazdırma verilerini TCP üzerinden yazıcıya iletir

---

## 11. Donanım Sorun Giderme

| Sorun | Çözüm |
|-------|-------|
| WebUSB desteklenmiyor | Chrome veya Edge tarayıcı kullanın |
| Cihaz bulunamıyor | USB kablosunu kontrol edin, cihazı yeniden bağlayın |
| Türkçe karakterler bozuk | WPC1254 kod sayfası ayarını kontrol edin |
| Ağ yazıcı yanıt vermiyor | IP adresi ve port 9100'ü kontrol edin |
| Terazi okuma hatası | Baud rate ve protokol ayarını kontrol edin |
| Barkod tarayıcı çalışmıyor | Keyboard wedge (HID) moduna ayarlayın |

