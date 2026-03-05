# Emare Finance — Modül Bazlı Veritabanı Tasarımı (MVP → Enterprise)

> Amaç: Emare Finance’in **çekirdek + opsiyonel modül** yaklaşımıyla her müşteri kitlesine uyarlanabilmesi için veritabanı katmanında net bir model sunmak.  
> Tarih: Mart 2026

---

## 1) Tasarım Prensipleri

1. **Çekirdek her zaman açık (core):** Satış, ürün, stok, cari, raporların temel kısmı gibi modüller core kabul edilir.
2. **Modül aktivasyonu tenant/şube düzeyinde yapılabilmeli:** Bazı müşterilerde tek şube, bazılarında çok şube olabilir.  
3. **Yetki ve menü dinamik olmalı:** Modül kapalıysa route, menü, API erişimi ve veri girişleri kapalı olmalı.
4. **Audit ve lisanslama izlenebilir olmalı:** Hangi modül ne zaman aktif edildi, kim yaptı, hangi paketle geldi gibi bilgiler loglanabilmeli.
5. **Geriye dönük uyumluluk:** Mevcut tablo ve modellere minimum kırıcı değişiklikle eklemlenmeli.

---

## 2) Kavramlar

- **Tenant (Müşteri Hesabı):** SaaS için üst seviye müşteri organizasyonu.
- **Branch (Şube):** Mevcut yapıda bulunan işletme/şube katmanı.
- **Module:** Açılıp kapanabilen özellik paketi (E-Fatura, Gelir/Gider, Personel, API, Gelişmiş Rapor vb.)
- **Plan/Paket:** Modülleri bir araya getiren ticari paket.

> Not: Mevcut projede `branches` tablosu ve ilişkiler var. Bu tasarım, hem “tek kurulum / on-prem” hem de “SaaS / multi-tenant” için uygulanabilir.

---

## 3) Önerilen Tablolar (Yeni)

### 3.1 `tenants` (SaaS için opsiyonel)
Tek kurulum yapıda zorunlu değil; SaaS’a geçişte gereklidir.

| Kolon | Tip | Açıklama |
|---|---|---|
| id | bigint | PK |
| name | string | Müşteri organizasyonu adı |
| status | enum | active/suspended/cancelled |
| plan_id | bigint | aktif paket |
| trial_ends_at | datetime | deneme bitiş |
| billing_email | string | faturalandırma |
| meta | json | ekstra |

İlişki:
- tenant **1:N** branch

---

### 3.2 `modules`
Sistemdeki tüm modül katalogu.

| Kolon | Tip | Açıklama |
|---|---|---|
| id | bigint | PK |
| code | string (unique) | örn: `einvoice`, `income_expense`, `advanced_reports` |
| name | string | görünen isim |
| description | text | açıklama |
| is_core | boolean | core mu? |
| scope | enum | tenant / branch / both |
| dependencies | json | bağımlı modüller (örn: advanced_reports → reports) |
| created_at / updated_at | timestamps | — |

Örnek modül kodları:
- `core_pos` (core)
- `hardware` (core)
- `einvoice`
- `income_expense`
- `staff`
- `advanced_reports`
- `api_access`
- `mobile_premium`

---

### 3.3 `plans` (Paketler)
| Kolon | Tip | Açıklama |
|---|---|---|
| id | bigint | PK |
| code | string unique | starter/business/enterprise |
| name | string | Paket adı |
| price_monthly | decimal | aylık ücret |
| price_yearly | decimal | yıllık ücret |
| is_active | boolean | yayında mı |
| limits | json | kota/limitler (şube sayısı, kullanıcı sayısı vb.) |

---

### 3.4 `plan_modules`
Paket → Modül bağlama tablosu.

| Kolon | Tip | Açıklama |
|---|---|---|
| id | bigint | PK |
| plan_id | bigint | FK plans |
| module_id | bigint | FK modules |
| included | boolean | pakete dahil mi (true) |
| config | json | modül özel ayarları |

---

### 3.5 `tenant_modules`
Tenant düzeyinde modül aktivasyonu (SaaS).

| Kolon | Tip | Açıklama |
|---|---|---|
| id | bigint | PK |
| tenant_id | bigint | FK tenants |
| module_id | bigint | FK modules |
| is_active | boolean | aktif mi |
| activated_at | datetime | aktif edildi |
| expires_at | datetime | opsiyonel |
| config | json | tenant bazlı ayar |

---

### 3.6 `branch_modules`
Şube bazlı modül aktivasyonu.

| Kolon | Tip | Açıklama |
|---|---|---|
| id | bigint | PK |
| branch_id | bigint | FK branches |
| module_id | bigint | FK modules |
| is_active | boolean | aktif mi |
| activated_at | datetime | — |
| config | json | şube bazlı ayar |

> Kural: `modules.scope=tenant` ise `tenant_modules` esas alınır; `branch` ise `branch_modules` esas alınır; `both` ise önce tenant sonra branch override edebilir.

---

### 3.7 `module_audit_logs`
Modül aç/kapa geçmişi.

| Kolon | Tip | Açıklama |
|---|---|---|
| id | bigint | PK |
| actor_user_id | bigint | işlemi yapan |
| tenant_id | bigint | — |
| branch_id | bigint | — |
| module_id | bigint | — |
| action | enum | enable/disable/config_update |
| before | json | önceki durum |
| after | json | sonraki durum |
| ip | string | opsiyonel |
| created_at | datetime | — |

---

## 4) Mevcut Tablolara Önerilen Eklentiler

### 4.1 `branches`
- `tenant_id` (SaaS için)
- `settings` JSON (şube bazlı bazı modül ayarları)

### 4.2 `users`
- `tenant_id`, `branch_id` (çoklu şube/tenant için)
- kullanıcı rol sistemi için `role_id` veya pivot (RBAC dokümanına bak)

---

## 5) Uygulama Katmanı Kuralları

### 5.1 Middleware: `module:CODE`
- Web route ve API route’larda kullanılacak.

**Mantık (pseudo):**
1. İstenen modül core ise izin ver
2. Tenant/Branch scope’a göre `*_modules` tablosundan aktif mi bak
3. Aktif değilse `403` (web’de modül sayfasına yönlendirme + upsell)

### 5.2 Menü ve UI
- Sidebar menü öğeleri modül aktifliğine göre render edilmeli.
- Modül kapalıysa “Bu özellik paketinize dahil değil” kartı gösterilebilir.

### 5.3 Veri Tutarlılığı
- Modül kapalıyken o modüle ait tablolar silinmez.
- Kullanıcı açınca kaldığı yerden devam eder (özellikle E-Fatura, Gelir/Gider gibi).

---

## 6) Örnek Modül Konfigürasyonları

### `einvoice` config (tenant_modules.config)
```json
{
  "integrator": "xxx",
  "default_scenario": "basic",
  "auto_send": false
}
```

### `api_access` config
```json
{
  "rate_limit_per_min": 120,
  "allowed_ips": ["1.2.3.4/32"]
}
```

---

## 7) Migration Taslakları (Özet)

- create_modules_table
- create_plans_table
- create_plan_modules_table
- create_tenants_table (opsiyonel)
- create_tenant_modules_table
- create_branch_modules_table
- create_module_audit_logs_table
- branches add tenant_id + settings

---

## 8) MVP İçin Minimum Set

MVP’de bile gerekli olanlar:
- `modules`
- `branch_modules`
- `module_audit_logs`
- Middleware + Sidebar dinamikliği

SaaS’a geçişte ek:
- `tenants`, `tenant_modules`, `plans`, `plan_modules`

---

## 9) Kabul Kriterleri

- [ ] Bir modül kapalıyken ilgili route/API erişimi engelleniyor
- [ ] Sidebar ve UI modül aktifliğine göre değişiyor
- [ ] Modül aç/kapa logları kaydediliyor
- [ ] Şube bazlı modül aktifliği çalışıyor
