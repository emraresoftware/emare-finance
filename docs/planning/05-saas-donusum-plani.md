# Emare Finance — SaaS’a Dönüşüm Planı (Multi-Tenant Yol Haritası)

> Amaç: Emare Finance’in tek kurulum yapısından **multi-tenant SaaS** yapısına güvenli ve sürdürülebilir şekilde evrilmesi.  
> Tarih: Mart 2026

---

## 1) Hedef Mimari

### Multi-tenant seçenekleri
1. **Shared DB, tenant_id ile ayrım (önerilen başlangıç)**
2. Tenant başına ayrı DB (enterprise için ileri aşama)

Başlangıç için:
- Tek DB
- `tenant_id` kolonları
- Query scope’ları (global scopes)

---

## 2) Temel Bileşenler

- Tenant kayıt/yaşam döngüsü (aktif/askıda/iptal)
- Plan/Paket sistemi
- Modül aktivasyonu (tenant_modules)
- Kullanıcı ve şube bağlama
- Faturalandırma (ileride)
- İzolasyon ve güvenlik (data leakage engeli)

---

## 3) Aşamalar (Faz Planı)

### Faz 0 — Hazırlık (Kod standardizasyonu)
- Service katmanı ayrımı
- FormRequest standardı
- Loglama standardı (request_id, user_id, branch_id)

### Faz 1 — Tenant Modeli
- `tenants` tablosu
- `branches.tenant_id`
- Kullanıcı girişinde tenant belirleme
- Tenant/branch scoping

### Faz 2 — Modül & Paket
- modules + plans + plan_modules
- tenant_modules + branch_modules
- middleware: module + permission

### Faz 3 — İzolasyon ve Güvenlik
- Global scope: tüm sorgular tenant_id ile filtrelenmeli
- Policy & Gate ile ek kontrol
- Rate limit (özellikle API ve print-network)
- Audit log zorunlu

### Faz 4 — Provisioning & Self-service
- Tenant oluşturma sihirbazı
- Varsayılan sektör şablonu uygulama
- Deneme süresi (trial)
- E-posta ile onboarding

### Faz 5 — Billing (Opsiyonel)
- Stripe/iyzico entegrasyonu
- Paket yükselt/düşür
- Fatura ve ödeme geçmişi

---

## 4) Teknik Detaylar

### 4.1 Tenant Context
- Login sonrası kullanıcıdan tenant tespit edilir
- Her request’te `TenantContext::current()` gibi merkezi erişim

### 4.2 Eloquent Global Scope
- Tenant scoping gereken modellere `belongsToTenant` trait’i:
  - sales, products, customers, staff, hardware_devices, etc.

> Not: Bazı tablolar “global katalog” olabilir (ör: `hardware_drivers` gibi). Bunlarda tenant scope olmaz.

### 4.3 Subdomain yaklaşımı (opsiyonel)
- `tenantSlug.app.com`
- Middleware subdomain’den tenant bulur

---

## 5) Data Migration Stratejisi

Mevcut on-prem müşterileri SaaS’a taşırken:
1. Tenant oluştur
2. Şubeleri ilişkilendir
3. Kullanıcıları ilişkilendir
4. Verileri tenant_id ile tag’le
5. Doğrulama raporu (kayıt sayıları, kritik tablolar)

---

## 6) Operasyon ve İzleme

- Tenant bazlı metrikler:
  - günlük satış sayısı
  - API çağrısı
  - yazdırma başarı oranı
- Alarmlar:
  - hata oranı artışı
  - login başarısızlık artışı
  - print-network timeouts

---

## 7) Güvenlik Notları (Öncelikli)

- `/api/hardware/print-network` mutlaka:
  - auth + permission
  - allowlist IP/port
  - payload limit
  - rate limit
  - audit log

---

## 8) Kabul Kriterleri

- [ ] Tenant izolasyonu %100 (data leakage testi)
- [ ] Modül/paket sistemi tenant bazlı çalışıyor
- [ ] RBAC tenant/branch scope ile çalışıyor
- [ ] Tenant bazlı log ve metrikler alınabiliyor
