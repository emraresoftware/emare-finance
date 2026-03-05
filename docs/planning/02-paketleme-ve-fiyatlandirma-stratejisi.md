# Emare Finance — Paketleme ve Fiyatlandırma Stratejisi

> Amaç: Farklı müşteri kitlelerine (mikro / orta / kurumsal) uygun **paket** ve **modül** bazlı satış planı oluşturmak.  
> Tarih: Mart 2026

---

## 1) Kitleler ve “En Çok Değer” Noktaları

### Mikro (tek şube, düşük karmaşıklık)
- Hızlı kurulum, kolay kullanım
- Donanım uyumluluğu (fiş yazıcı, barkod, terazi)
- Basit raporlar

### Orta (çok kullanıcı, süreçler oturmuş)
- Şube/depoya göre stok
- E-fatura, gelir/gider
- Gelişmiş raporlama, personel performansı

### Kurumsal (entegrasyon ve kontrol)
- API/ERP entegrasyon
- Audit log, gelişmiş yetkilendirme
- SLA, destek, özel geliştirme

---

## 2) Paket Yapısı (Öneri)

### 2.1 Starter (POS Temel)
**Hedef:** Mikro işletmeler

Dahil:
- Core POS: Ürün, Satış, Stok, Cari
- Donanım: Fiş yazdırma + Barkod dinleme (temel)
- Basit raporlar
- 1 şube (limit)

Opsiyonel eklenti:
- Etiket yazdırma
- Terazi entegrasyonu

---

### 2.2 Business (Operasyonel)
**Hedef:** Orta ölçek

Dahil:
- Starter içeriği
- Gelir/Gider
- Personel
- E-Fatura (entegratör ayarları dahil)
- Gelişmiş raporlar (seçilmiş set)
- 3 şube (limit)

Opsiyonel:
- Mobil premium
- Ek şube

---

### 2.3 Enterprise (Kurumsal)
**Hedef:** Zincir / kurumsal

Dahil:
- Business içeriği
- API Access + rate limit + allowlist
- Audit & Log (gelişmiş)
- Çoklu şube (yüksek limit)
- SSO / LDAP (opsiyonel)
- SLA destek paketi

Opsiyonel:
- ERP konektörü
- Özel rapor geliştirme
- Özel donanım entegrasyonları

---

## 3) Modül Bazlı “Add-on” Listesi

| Modül | Add-on mı? | Hedef Paket | Not |
|---|---:|---|---|
| `label_print` | ✅ | Starter/Business | etiket yazdırma |
| `scale_integration` | ✅ | Starter | market vb. |
| `einvoice` | ✅/pakete dahil | Business+ | entegratör |
| `income_expense` | ✅ | Business | — |
| `staff` | ✅ | Business | — |
| `advanced_reports` | ✅ | Business/Ent | KPI + pivot |
| `api_access` | ✅ | Enterprise | dış entegrasyon |
| `mobile_premium` | ✅ | Business/Ent | offline/ek |
| `multi_branch` | ✅ | Business/Ent | limit artışı |

---

## 4) Fiyatlandırma Modelleri

### 4.1 Basit (Tavsiye edilen başlangıç)
- Paket bazlı aylık ücret
- Şube ve kullanıcı sayısı kota/limit olarak

Örnek limit parametreleri:
- max_branches
- max_users
- max_products
- max_monthly_transactions

### 4.2 Hibrit
- Paket + modül eklenti ücretleri
- Ek şube / ek kullanıcı ücretlendirme

### 4.3 Kullanıma dayalı (ileri seviye)
- API çağrısı / yazdırma / e-fatura sayısı bazlı
- Başlangıç için zor; enterprise anlaşmalarında uygundur

---

## 5) Örnek Fiyat Tablosu (Placeholder)

> Not: Bu rakamlar pazara göre güncellenir; yazılımcı için amaç **modeli** netleştirmek.

| Paket | Aylık | Yıllık | Şube | Kullanıcı |
|---|---:|---:|---:|---:|
| Starter | ₺X | ₺Y | 1 | 3 |
| Business | ₺X | ₺Y | 3 | 10 |
| Enterprise | ₺X | ₺Y | 20 | 50 |

Add-on örnekleri:
- Ek şube: ₺…
- Mobil premium: ₺…
- API access: ₺…

---

## 6) Upsell / UI Önerileri

- Modül kapalı sayfada “paketinize dahil değil” ekranı + “deneme başlat” CTA
- Admin panelde “Modüller” sayfası (aktif/pasif + açıklama + fiyat)
- Kullanım limitleri yaklaşınca uyarı (ör: 1.000/1.200 satış)

---

## 7) Kabul Kriterleri

- [ ] Paket → modül ilişkisi DB’de var (plans + plan_modules)
- [ ] Limitler JSON olarak yönetilebiliyor
- [ ] UI’da modül kapalıyken upsell ekranı gösteriliyor
