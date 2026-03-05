# Emare Finance — Rol & Yetki Sistemi (RBAC) Tasarımı

> Amaç: Modül bazlı yapıyı tamamlayacak şekilde **rol/izin** kontrolünü standartlaştırmak (web + api).  
> Tarih: Mart 2026

---

## 1) Neden RBAC?

- Çok şubeli yapılarda kullanıcıların erişimleri ayrışmalı
- Personel, muhasebe, yönetici gibi roller net olmalı
- API erişimi (özellikle) kesin kural seti ister
- Modül kapalıyken yetki olsa bile erişim olmamalı (module middleware önce çalışır)

---

## 2) Kavramlar

- **Role (Rol):** Yetkilerin toplandığı profil (Admin, Kasiyer, Muhasebe, Depo, Yönetici)
- **Permission (İzin):** Tekil aksiyon (products.view, sales.export, einvoice.send)
- **UserRole:** Bir kullanıcının hangi role sahip olduğu (tenant/branch scope ile)
- **RolePermission:** Rolün hangi izinlere sahip olduğu

---

## 3) Önerilen Tablolar

### 3.1 `roles`
| Kolon | Tip | Açıklama |
|---|---|---|
| id | bigint | PK |
| code | string unique | admin/cashier/accounting |
| name | string | görünen isim |
| scope | enum | tenant / branch |
| is_system | boolean | sistem rolü mü |
| created_at/updated_at | timestamps | — |

### 3.2 `permissions`
| Kolon | Tip | Açıklama |
|---|---|---|
| id | bigint | PK |
| code | string unique | `products.view` |
| name | string | görünen isim |
| module_code | string | hangi modüle ait |
| group | string | ui grubu (products/sales/...) |
| created_at/updated_at | timestamps | — |

### 3.3 `role_permissions`
| role_id | permission_id |

### 3.4 `user_roles`
| Kolon | Tip | Açıklama |
|---|---|---|
| id | bigint | PK |
| user_id | bigint | — |
| role_id | bigint | — |
| tenant_id | bigint | — |
| branch_id | bigint | — (scope=branch için zorunlu) |
| created_at | datetime | — |

> Alternatif: Basit kurulumlarda `users.role_id` tekil rol olarak da yeterli olabilir. Kurumsalda pivot daha sağlıklı.

---

## 4) Permission Kod Standardı

Format: `{domain}.{action}`

Örnek domain’ler:
- products, sales, customers, reports, stock, staff, einvoice, hardware, settings

Örnek izinler:
- `products.view`, `products.create`, `products.update`, `products.delete`, `products.export`
- `sales.view`, `sales.create`, `sales.refund`, `sales.export`
- `einvoice.view`, `einvoice.create`, `einvoice.send`, `einvoice.cancel`
- `hardware.manage`, `hardware.print`, `hardware.connect`
- `reports.view`, `reports.advanced`

---

## 5) Middleware Zinciri

Önerilen sıra:
1. `auth`
2. `module:XYZ` (modül aktif mi?)
3. `permission:abc.def` (kullanıcıda izin var mı?)

### API için
- 401 (unauthenticated)
- 403 (forbidden — modül kapalı veya izin yok)

---

## 6) Varsayılan Roller (Seed)

### Admin
- tüm izinler

### Manager (Şube Müdürü)
- ürün/satış/cari/rapor yönetimi
- personel görüntüleme
- e-fatura yönetimi (varsa)

### Cashier (Kasiyer)
- satış yapma
- ürün görüntüleme
- raporların sınırlı görünümü

### Accounting (Muhasebe)
- gelir/gider
- e-fatura
- cari hareketler
- raporlar

### Warehouse (Depo)
- stok hareket
- ürün/stok güncelleme

---

## 7) UI Önerileri

- Ayarlar → “Roller & Yetkiler” ekranı
- Rol kopyalama (Clone)
- İzin grupları (accordion): Ürünler / Satışlar / Raporlar / E-Fatura / Donanım

---

## 8) Kabul Kriterleri

- [ ] Web route’lar permission ile korunuyor
- [ ] API endpoint’ler permission ile korunuyor
- [ ] Modül kapalıysa izin olsa bile erişim yok
- [ ] Seed ile varsayılan roller oluşuyor
