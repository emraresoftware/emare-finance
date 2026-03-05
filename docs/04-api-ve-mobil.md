# 🔌 Emare Finance — API Dokümantasyonu & Mobil Uygulama

> **Base URL:** `http://localhost:8000/api`  
> **Format:** JSON  
> **Toplam Endpoint:** 25  
> **Mobil Framework:** React Native (Expo SDK 52)  
> **Bundle ID:** `com.emare.finance`

---

# Bölüm A — API Dokümantasyonu

## 1. API Genel Bilgi

API, React Native mobil uygulama tarafından kullanılmak üzere tasarlanmıştır. Tüm cevaplar JSON formatındadır. Sayfalama destekleyen endpointler `?page=` parametresi alır.

### Ortak Response Yapısı

**Başarılı Yanıt:**
```json
{
  "data": [...],           // Veri dizisi
  "current_page": 1,       // Aktif sayfa
  "last_page": 5,          // Toplam sayfa
  "per_page": 20,          // Sayfa başına kayıt
  "total": 98              // Toplam kayıt
}
```

**Hata Yanıtı:**
```json
{
  "message": "Kayıt bulunamadı",
  "status": 404
}
```

---

## 2. Dashboard API

### `GET /api/dashboard`

Ana panel istatistiklerini döner.

**Response:**
```json
{
  "today_sales_count": 42,
  "today_sales_total": 15680.50,
  "total_products": 324,
  "total_customers": 156,
  "weekly_sales": [
    { "date": "2025-07-07", "total": 2340.00, "count": 15 },
    { "date": "2025-07-08", "total": 3120.50, "count": 22 }
  ],
  "top_products": [
    { "id": 1, "name": "Ürün A", "total_sold": 150 }
  ],
  "low_stock_products": [
    { "id": 5, "name": "Ürün E", "stock": 2, "min_stock": 10 }
  ],
  "recent_sales": [
    { "id": 101, "total": 245.00, "created_at": "2025-07-10T14:30:00" }
  ]
}
```

---

## 3. Satış API

### `GET /api/sales`

Satış listesini sayfalı olarak döner.

**Parametreler:**

| Parametre | Tip | Zorunlu | Açıklama |
|-----------|-----|---------|----------|
| `page` | int | ✗ | Sayfa numarası (varsayılan: 1) |
| `per_page` | int | ✗ | Sayfa başına kayıt (varsayılan: 20) |
| `start_date` | date | ✗ | Başlangıç tarihi (Y-m-d) |
| `end_date` | date | ✗ | Bitiş tarihi (Y-m-d) |
| `search` | string | ✗ | Fiş no veya müşteri adı araması |

**Response:**
```json
{
  "data": [
    {
      "id": 101,
      "receipt_no": "F-00101",
      "total_amount": 245.00,
      "discount_amount": 10.00,
      "net_amount": 235.00,
      "payment_type": "Nakit",
      "customer_name": "Ali Yılmaz",
      "staff_name": "Mehmet K.",
      "items_count": 3,
      "created_at": "2025-07-10T14:30:00"
    }
  ],
  "current_page": 1,
  "last_page": 12,
  "total": 230
}
```

---

### `GET /api/sales/{id}`

Belirli bir satışın detayını döner.

**Response:**
```json
{
  "id": 101,
  "receipt_no": "F-00101",
  "total_amount": 245.00,
  "discount_amount": 10.00,
  "net_amount": 235.00,
  "tax_amount": 42.35,
  "payment_type": "Nakit",
  "customer": {
    "id": 5,
    "name": "Ali Yılmaz",
    "phone": "05321234567"
  },
  "staff": {
    "id": 2,
    "name": "Mehmet K."
  },
  "items": [
    {
      "id": 1,
      "product_name": "Ürün A",
      "barcode": "8690001234567",
      "quantity": 2,
      "unit_price": 75.00,
      "total_price": 150.00,
      "discount": 5.00
    }
  ],
  "created_at": "2025-07-10T14:30:00"
}
```

---

## 4. Ürün API

### `GET /api/products`

Ürün listesini döner.

**Parametreler:**

| Parametre | Tip | Zorunlu | Açıklama |
|-----------|-----|---------|----------|
| `page` | int | ✗ | Sayfa numarası |
| `per_page` | int | ✗ | Sayfa başına kayıt (varsayılan: 20) |
| `search` | string | ✗ | Ürün adı veya barkod araması |
| `category_id` | int | ✗ | Kategori filtresi |

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Ürün A",
      "barcode": "8690001234567",
      "category_name": "Gıda",
      "sale_price": 75.00,
      "purchase_price": 50.00,
      "stock": 124,
      "unit": "Adet",
      "is_active": true
    }
  ],
  "current_page": 1,
  "last_page": 17,
  "total": 324
}
```

---

### `GET /api/products/{id}`

Ürün detayını döner.

**Response:**
```json
{
  "id": 1,
  "name": "Ürün A",
  "barcode": "8690001234567",
  "category": {
    "id": 3,
    "name": "Gıda"
  },
  "sale_price": 75.00,
  "purchase_price": 50.00,
  "stock": 124,
  "min_stock": 10,
  "unit": "Adet",
  "vat_rate": 18,
  "is_active": true,
  "recent_sales": [
    { "date": "2025-07-10", "quantity": 5 }
  ],
  "stock_movements": [
    { "type": "Giriş", "quantity": 100, "date": "2025-07-01" }
  ]
}
```

---

### `GET /api/categories`

Tüm kategorileri hiyerarşik olarak döner.

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Gıda",
      "parent_id": null,
      "product_count": 45,
      "children": [
        { "id": 4, "name": "İçecekler", "product_count": 12 }
      ]
    }
  ]
}
```

---

## 5. Müşteri API

### `GET /api/customers`

Müşteri listesini döner.

**Parametreler:**

| Parametre | Tip | Zorunlu | Açıklama |
|-----------|-----|---------|----------|
| `page` | int | ✗ | Sayfa numarası |
| `search` | string | ✗ | Ad, telefon veya vergi no araması |

**Response:**
```json
{
  "data": [
    {
      "id": 5,
      "name": "Ali Yılmaz",
      "phone": "05321234567",
      "email": "ali@email.com",
      "tax_number": "1234567890",
      "balance": -1250.00,
      "total_purchases": 15680.00,
      "last_purchase_date": "2025-07-10"
    }
  ],
  "current_page": 1,
  "last_page": 8,
  "total": 156
}
```

---

### `GET /api/customers/{id}`

Müşteri detayını döner.

**Response:**
```json
{
  "id": 5,
  "name": "Ali Yılmaz",
  "phone": "05321234567",
  "email": "ali@email.com",
  "address": "İstanbul, Türkiye",
  "tax_number": "1234567890",
  "tax_office": "Kadıköy V.D.",
  "balance": -1250.00,
  "total_purchases": 15680.00,
  "recent_transactions": [
    {
      "id": 1,
      "type": "sale",
      "amount": 500.00,
      "description": "Satış #101",
      "date": "2025-07-10"
    }
  ],
  "recent_sales": [
    {
      "id": 101,
      "total": 500.00,
      "date": "2025-07-10"
    }
  ]
}
```

---

## 6. Stok API

### `GET /api/stock/movements`

Stok hareketlerini döner.

**Parametreler:**

| Parametre | Tip | Zorunlu | Açıklama |
|-----------|-----|---------|----------|
| `page` | int | ✗ | Sayfa numarası |
| `type` | string | ✗ | `in` (giriş) veya `out` (çıkış) |
| `product_id` | int | ✗ | Ürün bazlı filtreleme |

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "product_name": "Ürün A",
      "barcode": "8690001234567",
      "type": "Giriş",
      "quantity": 100,
      "description": "Alış faturası #45",
      "created_at": "2025-07-01T09:00:00"
    }
  ]
}
```

---

### `GET /api/stock/alerts`

Minimum stok seviyesinin altındaki ürünleri döner.

**Response:**
```json
{
  "data": [
    {
      "id": 5,
      "name": "Ürün E",
      "barcode": "8690005678901",
      "current_stock": 2,
      "min_stock": 10,
      "category": "Gıda"
    }
  ],
  "total_alerts": 8
}
```

---

## 7. Rapor API

### `GET /api/reports/daily`

Günlük satış raporunu döner.

**Parametreler:**

| Parametre | Tip | Zorunlu | Açıklama |
|-----------|-----|---------|----------|
| `date` | date | ✗ | Rapor tarihi (varsayılan: bugün) |

**Response:**
```json
{
  "date": "2025-07-10",
  "total_sales": 42,
  "total_revenue": 15680.50,
  "total_cost": 10200.00,
  "gross_profit": 5480.50,
  "profit_margin": 34.95,
  "payment_breakdown": {
    "Nakit": 8500.00,
    "Kredi Kartı": 5200.00,
    "Havale/EFT": 1980.50
  },
  "hourly_sales": [
    { "hour": "09:00", "count": 3, "total": 450.00 },
    { "hour": "10:00", "count": 5, "total": 1200.00 }
  ],
  "top_products": [
    { "name": "Ürün A", "quantity": 25, "revenue": 1875.00 }
  ]
}
```

---

### `GET /api/reports/summary`

Genel özet raporunu döner.

**Parametreler:**

| Parametre | Tip | Zorunlu | Açıklama |
|-----------|-----|---------|----------|
| `start_date` | date | ✗ | Başlangıç tarihi |
| `end_date` | date | ✗ | Bitiş tarihi |

**Response:**
```json
{
  "period": {
    "start": "2025-07-01",
    "end": "2025-07-10"
  },
  "total_sales_count": 420,
  "total_revenue": 156800.00,
  "total_cost": 102000.00,
  "gross_profit": 54800.00,
  "average_sale": 373.33,
  "top_categories": [
    { "name": "Gıda", "revenue": 65000.00, "percentage": 41.5 }
  ]
}
```

---

## 8. Donanım API

### `GET /api/hardware/devices`

Aktif donanım cihazlarını türe göre gruplu döner.

**Parametreler:**

| Parametre | Tip | Zorunlu | Açıklama |
|-----------|-----|---------|----------|
| `type` | string | ✗ | Cihaz türü filtresi (receipt_printer, label_printer, scale vb.) |

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Fiş Yazıcı 1",
      "type": "receipt_printer",
      "connection": "usb",
      "protocol": "escpos",
      "model": "TM-T20II",
      "manufacturer": "Epson",
      "vendor_id": "04b8",
      "product_id": "0e15",
      "is_default": true,
      "status": "connected",
      "settings": { "paper_width": 80, "char_per_line": 48 }
    }
  ]
}
```

---

### `POST /api/hardware/print-network`

Ağ yazıcıya TCP socket üzerinden yazdırma verisi gönderir.

**Body:**
```json
{
  "ip": "192.168.1.100",
  "port": 9100,
  "data": [27, 64, 27, 97, 1, ...]
}
```

**Response:**
```json
{
  "success": true,
  "message": "Yazdırma başarılı"
}
```

---

## 9. Sürücü Kataloğu API

> Sürücü kataloğu, 108 bilinen POS cihaz modelini içeren referans veritabanıdır.
> Kaynak: `database/data/hardware-drivers.json` → `hardware_drivers` tablosu

### `GET /api/hardware/drivers`

Sürücü kataloğunu arayarak/filtreleyerek döner.

**Parametreler:**

| Parametre | Tip | Zorunlu | Açıklama |
|-----------|-----|---------|----------|
| `type` | string | ✗ | Cihaz türü filtresi |
| `manufacturer` | string | ✗ | Üretici filtresi |
| `q` | string | ✗ | Serbest metin arama |
| `usb_only` | boolean | ✗ | Sadece USB ID'si olan cihazlar |

**Response:**
```json
{
  "drivers": [
    {
      "id": 1,
      "device_type": "receipt_printer",
      "manufacturer": "Epson",
      "model": "TM-T20II",
      "full_name": "Epson TM-T20II",
      "protocol": "escpos",
      "vendor_id": "04b8",
      "product_id": "0202"
    }
  ],
  "count": 108
}
```

### `GET /api/hardware/drivers/stats`

Sürücü kataloğu istatistiklerini döner.

### `GET /api/hardware/drivers/manufacturers`

Cihaz türüne göre üretici listesini döner.

### `GET /api/hardware/drivers/models`

Üreticiye ve türe göre model listesini döner.

### `GET /api/hardware/drivers/{id}`

Tek sürücünün tüm detaylarını döner.

---

## 10. Endpoint Özet Tablosu

| # | Metod | Endpoint | Açıklama |
|---|-------|----------|----------|
| 1 | GET | `/api/dashboard` | Dashboard istatistikleri |
| 2 | GET | `/api/sales` | Satış listesi |
| 3 | GET | `/api/sales/{id}` | Satış detay |
| 4 | GET | `/api/products` | Ürün listesi |
| 5 | GET | `/api/products/{id}` | Ürün detay |
| 6 | GET | `/api/categories` | Kategori listesi |
| 7 | GET | `/api/customers` | Müşteri listesi |
| 8 | GET | `/api/customers/{id}` | Müşteri detay |
| 9 | GET | `/api/stock/movements` | Stok hareketleri |
| 10 | GET | `/api/stock/alerts` | Stok uyarıları |
| 11 | GET | `/api/reports/daily` | Günlük rapor |
| 12 | GET | `/api/reports/summary` | Özet rapor |
| 13 | GET | `/api/hardware/devices` | Aktif donanım cihaz listesi |
| 14 | POST | `/api/hardware/print-network` | Ağ yazıcıya yazdır |
| 15 | GET | `/api/hardware/drivers` | Sürücü kataloğu arama/filtreleme |
| 16 | GET | `/api/hardware/drivers/stats` | Sürücü istatistikleri |
| 17 | GET | `/api/hardware/drivers/manufacturers` | Üretici listesi |
| 18 | GET | `/api/hardware/drivers/models` | Üreticiye göre model listesi |
| 19 | GET | `/api/hardware/drivers/{id}` | Tek sürücü detayı |
| 20–25 | — | *(Ek endpoints)* | Gelir/gider, personel vb. |

---

## 11. Hata Kodları

| HTTP Kodu | Anlamı | Açıklama |
|-----------|--------|----------|
| 200 | OK | Başarılı istek |
| 201 | Created | Kayıt oluşturuldu |
| 400 | Bad Request | Geçersiz parametre |
| 404 | Not Found | Kayıt bulunamadı |
| 422 | Unprocessable | Validasyon hatası |
| 500 | Server Error | Sunucu hatası |

---

## 12. API Kullanım Örnekleri

### cURL ile Dashboard Verisi Çekme
```bash
curl -X GET http://localhost:8000/api/dashboard \
  -H "Accept: application/json"
```

### cURL ile Ürün Arama
```bash
curl -X GET "http://localhost:8000/api/products?search=cola&page=1" \
  -H "Accept: application/json"
```

### cURL ile Satış Detayı
```bash
curl -X GET http://localhost:8000/api/sales/101 \
  -H "Accept: application/json"
```

---

## 13. API Gelecek Planları

- [ ] **Token bazlı kimlik doğrulama** (Laravel Sanctum)
- [ ] **POST/PUT/DELETE** endpointleri (veri oluşturma ve güncelleme)
- [ ] **WebSocket** desteği (gerçek zamanlı bildirimler)
- [ ] **Rate limiting** (istek sınırlama)
- [ ] **API versiyonlama** (`/api/v2/...`)

---
---

# Bölüm B — Mobil Uygulama

> **Framework:** React Native (Expo SDK 52)  
> **Platform:** iOS & Android  
> **Dizin:** `/mobile`

---

## 14. Kurulum ve Çalıştırma

### Gereksinimler
- Node.js v18+ (v24 için özel ayar gerekli)
- Expo CLI (`npx expo`)
- iOS: Expo Go veya Xcode
- Android: Expo Go veya Android Studio

### Kurulum
```bash
cd mobile
npm install
```

### Çalıştırma
```bash
npm start        # Expo dev server başlat
npm run ios      # iOS simülatörde çalıştır
npm run android  # Android emulatörde çalıştır
```

> ⚠️ **Node.js v24+ Uyarısı:**  
> Node.js v24'ün `--experimental-strip-types` özelliği Expo ile çakışır.  
> Tüm npm scriptlerinde `NODE_OPTIONS='--no-experimental-strip-types'` eklenmelidir.  
> Bu ayar `package.json` içinde zaten yapılmıştır.

---

## 15. Paket Bağımlılıkları

| Paket | Sürüm | Açıklama |
|-------|-------|----------|
| `expo` | ~52.0.46 | Expo SDK |
| `react-native` | 0.76.9 | React Native çekirdeği |
| `@react-navigation/native` | ^7.x | Navigasyon kütüphanesi |
| `@react-navigation/bottom-tabs` | ^7.x | Alt tab navigasyonu |
| `@react-navigation/native-stack` | ^7.x | Stack navigasyonu |
| `@react-native-async-storage/async-storage` | 1.23.1 | Yerel depolama |
| `@expo/vector-icons` | ^14.0.4 | İkon kütüphanesi |
| `react-native-safe-area-context` | 4.12.0 | Güvenli alan yönetimi |
| `react-native-screens` | ~4.4.0 | Ekran optimizasyonu |
| `expo-status-bar` | ~2.0.1 | Durum çubuğu |
| `expo-asset` | ~11.0.5 | Asset yönetimi |
| `expo-splash-screen` | ~0.29.22 | Splash ekranı |

---

## 16. Proje Yapısı

```
mobile/
├── App.js                    ← Ana uygulama & navigasyon
├── app.json                  ← Expo konfigürasyonu
├── package.json              ← Bağımlılıklar
│
├── src/
│   ├── api/
│   │   └── client.js         ← API istemcisi (axios benzeri)
│   │
│   ├── components/
│   │   ├── Card.js           ← Kart bileşeni
│   │   ├── EmptyState.js     ← Boş durum gösterimi
│   │   ├── ErrorState.js     ← Hata durumu gösterimi
│   │   ├── Header.js         ← Sayfa başlığı
│   │   ├── ListItem.js       ← Liste elemanı
│   │   ├── LoadingState.js   ← Yükleniyor gösterimi
│   │   ├── SearchBar.js      ← Arama çubuğu
│   │   └── StatCard.js       ← İstatistik kartı
│   │
│   ├── screens/
│   │   ├── DashboardScreen.js    ← Ana panel
│   │   ├── SalesScreen.js        ← Satış listesi
│   │   ├── SaleDetailScreen.js   ← Satış detay
│   │   ├── ProductsScreen.js     ← Ürün listesi
│   │   ├── ProductDetailScreen.js← Ürün detay
│   │   ├── CustomersScreen.js    ← Müşteri listesi
│   │   ├── CustomerDetailScreen.js← Müşteri detay
│   │   ├── StockAlertsScreen.js  ← Stok uyarıları
│   │   ├── ReportsScreen.js      ← Raporlar
│   │   ├── SettingsScreen.js     ← Ayarlar
│   │   └── MoreScreen.js         ← Daha fazla menü
│   │
│   └── utils/
│       ├── theme.js          ← Renk paleti & stiller
│       └── formatters.js     ← Para, tarih formatlayıcılar
│
└── assets/
    ├── icon.png              ← Uygulama ikonu (1024x1024)
    ├── splash-icon.png       ← Splash ekran ikonu
    ├── adaptive-icon.png     ← Android adaptive ikon
    └── favicon.png           ← Web favicon
```

---

## 17. Navigasyon Yapısı

### Alt Tab Navigasyonu (5 Tab)

```
┌──────────────────────────────────────────────────┐
│                                                  │
│              [Ekran İçeriği]                     │
│                                                  │
├──────────┬──────────┬──────────┬────────┬────────┤
│ 📊       │ 💰       │ 📦       │ 👥     │ ≡      │
│ Ana Panel│ Satışlar │ Ürünler  │Cariler │ Daha   │
└──────────┴──────────┴──────────┴────────┴────────┘
```

### Stack Navigasyonlar

Her tab kendi stack navigator'ına sahiptir:

```
DashboardTab                SalesTab              ProductsTab
├── DashboardScreen         ├── SalesScreen       ├── ProductsScreen
                            └── SaleDetailScreen  └── ProductDetailScreen

CustomersTab                MoreTab
├── CustomersScreen         ├── MoreScreen
└── CustomerDetailScreen    ├── StockAlertsScreen
                            ├── ReportsScreen
                            └── SettingsScreen
```

---

## 18. Ekranlar (Detaylı)

### 18.1 📊 DashboardScreen

**Ana panel ekranı** — Uygulamaya girişte ilk görülen ekrandır.

**Gösterir:**
- 4 adet StatCard (bugünkü satış, ciro, ürün sayısı, müşteri sayısı)
- Düşük stoklu ürünler listesi (kırmızı uyarı)
- Son satışlar özeti

**API Çağrısı:** `GET /api/dashboard`

### 18.2 💰 SalesScreen

**Satış listesi** — Tüm satışları tarih sırasına göre listeler.

**Özellikler:**
- Arama (fiş no veya müşteri adı)
- Sonsuz scroll (sayfalama)
- Pull-to-refresh
- Satışa dokunarak detay ekranına geçiş

**API Çağrısı:** `GET /api/sales?page={page}&search={query}`

### 18.3 💰 SaleDetailScreen

**Satış detayı** — Belirli bir satışın tüm bilgilerini gösterir.

**Gösterir:**
- Fiş numarası ve tarih
- Müşteri bilgisi
- Personel bilgisi
- Ödeme yöntemi
- Ürün kalemleri (miktar × fiyat)
- Toplam, indirim, net tutar

**API Çağrısı:** `GET /api/sales/{id}`

### 18.4 📦 ProductsScreen

**Ürün listesi** — Tüm ürünleri kategoriye göre filtreli listeler.

**Özellikler:**
- Arama (ürün adı veya barkod)
- Kategori filtresi (chip'ler)
- Stok durumu renk göstergesi (yeşil/sarı/kırmızı)
- Sonsuz scroll

**API Çağrısı:** `GET /api/products?page={page}&search={query}&category_id={id}`

### 18.5 📦 ProductDetailScreen

**Ürün detayı** — Ürünün tüm bilgileri ve geçmişi.

**Gösterir:**
- Ürün adı, barkod, kategori
- Satış fiyatı / alış fiyatı / kâr marjı
- Güncel stok miktarı
- Son satış hareketleri
- Stok giriş/çıkış geçmişi

**API Çağrısı:** `GET /api/products/{id}`

### 18.6 👥 CustomersScreen

**Müşteri listesi** — Tüm cari hesapları listeler.

**Özellikler:**
- Arama (ad, telefon, vergi no)
- Bakiye göstergesi (borçlu: kırmızı, alacaklı: yeşil)
- Son alışveriş tarihi

**API Çağrısı:** `GET /api/customers?page={page}&search={query}`

### 18.7 👥 CustomerDetailScreen

**Müşteri detayı** — Müşterinin tam profili.

**Gösterir:**
- İletişim bilgileri (telefon, email, adres)
- Vergi bilgileri
- Güncel bakiye
- Son hesap hareketleri
- Satış geçmişi

**API Çağrısı:** `GET /api/customers/{id}`

### 18.8 🚨 StockAlertsScreen

**Stok uyarıları** — Minimum stok altındaki ürünleri gösterir.

**API Çağrısı:** `GET /api/stock/alerts`

### 18.9 📈 ReportsScreen

**Raporlar** — Satış raporlarını gösterir.

**API Çağrısı:** `GET /api/reports/daily`

### 18.10 ⚙️ SettingsScreen

**Ayarlar** — API URL değiştirme, tema seçimi, önbellek temizleme.

### 18.11 ≡ MoreScreen

**Daha fazla** — Stok Uyarıları, Raporlar, Ayarlar menü öğeleri.

---

## 19. Bileşenler (Components)

| Bileşen | Dosya | Açıklama |
|---------|-------|----------|
| `Card` | `Card.js` | Gölgeli, yuvarlatılmış köşeli kart konteyneri |
| `StatCard` | `StatCard.js` | İkon + değer + etiket gösteren istatistik kartı |
| `ListItem` | `ListItem.js` | Standart liste öğesi (başlık, alt başlık, sağ değer) |
| `SearchBar` | `SearchBar.js` | Arama girdisi (debounce destekli) |
| `Header` | `Header.js` | Özel sayfa başlığı |
| `LoadingState` | `LoadingState.js` | Yüklenme göstergesi (ActivityIndicator) |
| `EmptyState` | `EmptyState.js` | Veri yokken gösterilen ikon + mesaj |
| `ErrorState` | `ErrorState.js` | Hata durumunda yeniden deneme butonu |

---

## 20. API İstemcisi (`src/api/client.js`)

```javascript
// Yapılandırma
const API_BASE_URL = 'http://192.168.2.100:8000/api';
const TIMEOUT = 10000; // 10 saniye

// Kullanım
import api from '../api/client';

// GET isteği
const sales = await api.get('/sales', { page: 1, search: 'ali' });

// Hata yönetimi
try {
  const data = await api.get('/products');
} catch (error) {
  console.error('API hatası:', error.message);
}
```

**Özellikler:**
- Otomatik JSON parse
- Timeout yönetimi (10sn)
- Query string oluşturma
- Hata yakalama ve düzgün hata mesajları
- Base URL AsyncStorage'dan okunabilir (Ayarlar ekranı)

---

## 21. Tema Sistemi (`src/utils/theme.js`)

```javascript
const theme = {
  colors: {
    primary: '#2563EB',      // Mavi
    secondary: '#7C3AED',    // Mor
    success: '#10B981',      // Yeşil
    warning: '#F59E0B',      // Sarı
    danger: '#EF4444',       // Kırmızı
    background: '#F3F4F6',   // Açık gri arka plan
    surface: '#FFFFFF',      // Beyaz yüzey
    text: '#1F2937',         // Koyu metin
    textSecondary: '#6B7280' // İkincil metin
  },
  spacing: { xs: 4, sm: 8, md: 16, lg: 24, xl: 32 },
  borderRadius: { sm: 8, md: 12, lg: 16, full: 999 },
  fontSize: { xs: 10, sm: 12, md: 14, lg: 16, xl: 20, xxl: 24, hero: 32 }
};
```

---

## 22. Formatlayıcılar (`src/utils/formatters.js`)

| Fonksiyon | Giriş | Çıkış | Açıklama |
|-----------|-------|-------|----------|
| `formatCurrency(1500)` | `1500` | `₺1.500,00` | Para birimi formatlama |
| `formatDate('2025-07-10T14:30')` | ISO tarih | `10.07.2025` | Tarih formatlama |
| `formatDateTime('2025-07-10T14:30')` | ISO tarih | `10.07.2025 14:30` | Tarih-saat formatlama |
| `formatNumber(1234567)` | `1234567` | `1.234.567` | Sayı formatlama |
| `formatPhone('05321234567')` | Telefon | `0532 123 45 67` | Telefon formatlama |

---

## 23. Mobil Sorun Giderme

| Sorun | Çözüm |
|-------|-------|
| `ERR_UNSUPPORTED_NODE_MODULES_TYPE_STRIPPING` | `NODE_OPTIONS='--no-experimental-strip-types'` ekleyin (package.json'da mevcut) |
| Metro Bundler bağlantı hatası | Aynı WiFi ağına bağlanın, `src/api/client.js` IP'sini kontrol edin |
| API bağlantısı başarısız | `php artisan serve --host=0.0.0.0 --port=8000` ile sunucuyu başlatın |
| Expo Go uyumsuzluk | Expo Go'yu App Store / Play Store'dan güncelleyin |

---

## 24. Build ve Dağıtım

```bash
# Development Build
npx expo run:ios        # iOS native build
npx expo run:android    # Android native build

# Production Build (EAS)
npm install -g eas-cli
eas login
eas build --platform ios
eas build --platform android

# APK Oluşturma (Android)
eas build --platform android --profile preview
```

---

## 25. Mobil Gelecek Planları

- [ ] **Push bildirimleri** — Düşük stok uyarısı, yeni satış bildirimi
- [ ] **Koyu tema** — Dark mode desteği
- [ ] **Offline destek** — Çevrimdışı veri önbellekleme
- [ ] **Barkod tarayıcı** — Kamera ile ürün arama
- [ ] **Grafik ve grafikler** — react-native-chart-kit ile görsel raporlar
- [ ] **Biometrik giriş** — Face ID / Parmak izi ile güvenli giriş
- [ ] **Çoklu dil desteği** — i18n entegrasyonu
