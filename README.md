# Emare Finance

Finans ve satış yönetim sistemi. Detaylı raporlama, stok takibi, müşteri yönetimi ve finansal analiz yapmanızı sağlar.

## 🛠️ Teknoloji Yığını

| Katman | Teknoloji |
|--------|-----------|
| Backend | Laravel 12, PHP 8.2+ |
| Frontend | Blade, Tailwind CSS 4, Alpine.js |
| Grafik | Chart.js |
| Veritabanı | SQLite (varsayılan) / MySQL / PostgreSQL |

## 📋 Gereksinimler

- PHP >= 8.2
- Composer
- Node.js >= 18
- npm

## 🚀 Kurulum

```bash
# 1. Repoyu klonla
git clone <repo-url> emare-finance
cd emare-finance

# 2. Bağımlılıkları yükle ve projeyi kur
composer setup

# 3. .env dosyasını düzenle
cp .env.example .env
```

## ▶️ Çalıştırma

```bash
# Geliştirme sunucusunu başlat
composer dev

# Veya sadece sunucu
php artisan serve
```

## 📊 Modüller

| Modül | URL | Açıklama |
|-------|-----|----------|
| Dashboard | `/` | Genel istatistikler, grafikler |
| Raporlar | `/raporlar/*` | Günlük, tarihsel, ürünsel, kâr analizi |
| Müşteriler | `/cariler` | Müşteri/cari listesi ve detayları |
| Ürünler | `/urunler` | Ürün listesi, gruplar, varyantlar |
| Satışlar | `/satislar` | Satış listesi ve detayları |
| Alış Faturaları | `/alis-faturalari` | Tedarikçi faturaları |
| Firmalar | `/firmalar` | Tedarikçi yönetimi |
| E-Faturalar | `/e-faturalar` | Gelen/giden e-faturalar |
| Stok | `/stok` | Stok hareketleri, sayımlar |
| Gelir/Gider | `/gelir-gider` | Gelir, gider ve türleri |
| Personeller | `/personeller` | Personel listesi ve hareketleri |
| Görevler | `/gorevler` | Görev yönetimi |

## 🧪 Test

```bash
composer test
```

## 📁 Proje Yapısı

```
├── app/
│   ├── Console/Commands/      # Artisan komutları
│   ├── Http/Controllers/      # Controller'lar
│   ├── Models/                # Eloquent modeller
│   ├── Providers/             # Service Provider'lar
│   └── Services/              # İş mantığı servisleri
├── database/migrations/       # Veritabanı şeması
├── resources/views/           # Blade view dosyaları
├── routes/
│   ├── web.php                # Web route tanımları
│   └── console.php            # Artisan komutları & schedule
└── config/                    # Konfigürasyon dosyaları
```

## 📝 Blade Directive'leri

```blade
@money(1234.56)          {{-- Çıktı: 1.234,56 ₺ --}}
@tarih($date)            {{-- Çıktı: 01 Mart 2026 --}}
@tarihSaat($date)        {{-- Çıktı: 01 Mart 2026 14:30 --}}
```

## 📄 Lisans

MIT
