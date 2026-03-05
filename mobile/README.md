# 📱 Emare Finance Mobil Uygulama

React Native (Expo) ile geliştirilmiş Emare Finance mobil uygulaması.

## 🚀 Özellikler

### 📊 Ana Sayfa (Dashboard)
- Günlük, haftalık, aylık ve toplam ciro
- Ürün, müşteri ve düşük stok sayıları
- Düşük stok uyarıları
- Son satışlar listesi

### 💰 Satışlar
- Satış listesi (arama, sayfalama)
- Satış detay görünümü (ürünler, toplam)
- Ödeme yöntemi filtreleme

### 📦 Ürünler
- Ürün listesi (arama, kategori filtre)
- Ürün detayı (fiyat, stok, kâr marjı)
- Satış istatistikleri (son 30 gün)

### 👥 Cariler (Müşteriler)
- Müşteri listesi (arama)
- Müşteri detayı (iletişim, alışveriş geçmişi)
- Hızlı iletişim butonları (Ara, Mesaj, E-posta)

### 📈 Raporlar
- Gelir trend grafiği
- En çok satan ürünler
- Ödeme yöntemleri pasta grafiği
- Dönem seçimi (7, 15, 30 gün)

### 🏭 Stok Yönetimi
- Stok genel bakış
- Düşük stok uyarıları
- Stok hareketleri

### ⚙️ Ayarlar
- API sunucu adresi yapılandırma
- Bağlantı testi

---

## 📋 Gereksinimler

- **Node.js** 18+
- **Expo CLI** (`npx expo`)
- **iOS**: macOS + Xcode (veya Expo Go uygulaması)
- **Android**: Android Studio (veya Expo Go uygulaması)
- **Laravel Backend**: 8000 portunda çalışıyor olmalı

---

## 🛠️ Kurulum

### 1. Bağımlılıkları yükleyin
```bash
cd mobile
npm install
```

### 2. Laravel Backend'i başlatın
```bash
# Ana dizinde
php artisan serve --host=0.0.0.0 --port=8000
```

> ⚠️ `--host=0.0.0.0` parametresi mobil cihazların erişebilmesi için gereklidir.

### 3. Bilgisayarınızın IP adresini bulun
```bash
# macOS
ifconfig | grep "inet " | grep -v 127.0.0.1

# Windows
ipconfig
```

### 4. Mobil uygulamayı başlatın
```bash
cd mobile
npx expo start
```

### 5. Expo Go ile Bağlanın
- Telefonunuza **Expo Go** uygulamasını yükleyin (App Store / Google Play)
- Terminaldeki QR kodunu tarayın
- Uygulama açıldığında **Menü → Ayarlar** ekranından API URL'sini ayarlayın:
  ```
  http://BILGISAYAR_IP:8000
  ```
  Örnek: `http://192.168.1.100:8000`

---

## 📁 Proje Yapısı

```
mobile/
├── App.js                      # Ana giriş noktası + navigasyon
├── app.json                    # Expo yapılandırması
├── package.json                # Bağımlılıklar
├── babel.config.js             # Babel yapılandırması
└── src/
    ├── api/
    │   └── client.js           # API istemcisi
    ├── components/
    │   ├── StatCard.js          # İstatistik kartı
    │   ├── SearchBar.js         # Arama çubuğu
    │   ├── SaleCard.js          # Satış kartı
    │   ├── ProductCard.js       # Ürün kartı
    │   ├── CustomerCard.js      # Müşteri kartı
    │   ├── SectionHeader.js     # Bölüm başlığı
    │   ├── EmptyState.js        # Boş durum gösterimi
    │   └── LoadingState.js      # Yükleme gösterimi
    ├── screens/
    │   ├── DashboardScreen.js   # Ana sayfa
    │   ├── SalesScreen.js       # Satışlar
    │   ├── SaleDetailScreen.js  # Satış detayı
    │   ├── ProductsScreen.js    # Ürünler
    │   ├── ProductDetailScreen.js # Ürün detayı
    │   ├── CustomersScreen.js   # Müşteriler
    │   ├── CustomerDetailScreen.js # Müşteri detayı
    │   ├── ReportsScreen.js     # Raporlar
    │   ├── StockScreen.js       # Stok yönetimi
    │   ├── SettingsScreen.js    # Ayarlar
    │   └── MoreScreen.js        # Menü
    ├── theme/
    │   └── index.js             # Renkler, boyutlar, gölgeler
    └── utils/
        └── formatters.js        # Para, tarih formatları
```

---

## 🔌 API Endpoint'leri

Uygulama aşağıdaki Laravel API endpoint'lerini kullanır:

| Endpoint | Açıklama |
|----------|----------|
| `GET /api/dashboard` | Dashboard verileri |
| `GET /api/products` | Ürün listesi (sayfalama + arama) |
| `GET /api/products/{id}` | Ürün detayı |
| `GET /api/products/categories` | Kategoriler |
| `GET /api/products/low-stock` | Düşük stoklu ürünler |
| `GET /api/sales` | Satış listesi (sayfalama + filtre) |
| `GET /api/sales/{id}` | Satış detayı |
| `GET /api/sales/summary` | Satış özeti |
| `GET /api/customers` | Müşteri listesi |
| `GET /api/customers/{id}` | Müşteri detayı |
| `GET /api/customers/{id}/sales` | Müşteri satışları |
| `GET /api/reports/daily` | Günlük rapor |
| `GET /api/reports/top-products` | En çok satanlar |
| `GET /api/reports/revenue-chart` | Gelir grafiği |
| `GET /api/reports/payment-methods` | Ödeme yöntemleri |
| `GET /api/stock/overview` | Stok genel bakış |
| `GET /api/stock/movements` | Stok hareketleri |
| `GET /api/stock/alerts` | Stok uyarıları |

---

## 🎨 Tasarım

- **Renk paleti**: Indigo (#4F46E5), Yeşil (#10B981), Turuncu (#F59E0B), Kırmızı (#EF4444)
- **Gradient hero kartları** ile modern dashboard
- **Haptic feedback** ile dokunsal geri bildirim
- **Pull to refresh** ile veri yenileme
- **Sayfalama** (infinite scroll) ile performanslı listeler
- **Turkish locale** - Tam Türkçe arayüz

---

## 🔧 Sorun Giderme

### "Sunucuya bağlanılamıyor" hatası
1. Laravel sunucusu `--host=0.0.0.0` ile çalışıyor mu?
2. Telefon ve bilgisayar aynı Wi-Fi ağında mı?
3. Güvenlik duvarı 8000 portuna izin veriyor mu?
4. Doğru IP adresi girildi mi?

### Expo Go QR kod taranamıyor
- Aynı ağda olduğunuzdan emin olun
- `npx expo start --tunnel` ile tünel modu deneyin

### Veriler görünmüyor
- Tarayıcıdan `http://BILGISAYAR_IP:8000/api/dashboard` test edin
- CORS ayarlarını kontrol edin
