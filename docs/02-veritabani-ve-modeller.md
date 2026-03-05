# 🗃️ Emare Finance — Veritabanı & Modeller

> **Veritabanı:** SQLite (varsayılan) — MySQL/PostgreSQL desteği mevcut  
> **ORM:** Eloquent  
> **Toplam Model:** 26 | **Toplam Tablo:** 27+

---

## 1. Tablo İlişki Diyagramı

```
┌──────────┐    1:N    ┌──────────┐    1:N    ┌───────────┐
│   User   │──────────►│   Sale   │──────────►│ SaleItem  │
└──────────┘           └──────────┘           └───────────┘
                          │  │                      │
                     N:1  │  │ N:1             N:1  │
                          ▼  ▼                      ▼
                    ┌────────┐ ┌──────────┐   ┌─────────┐
                    │Customer│ │  Branch   │   │ Product │
                    └────────┘ └──────────┘   └─────────┘
                       │            │              │  │
                  1:N  │       N:M  │         1:N  │  │ N:1
                       ▼            │              │  ▼
              ┌────────────────┐    │         ┌────┘ ┌──────────┐
              │AccountTransaction│  │         │      │ Category │
              └────────────────┘   │         │      └──────────┘
                                   │         │         │ self-ref
                            ┌──────┘         │         ▼
                            │                │      (parent/children)
                            ▼                │
                    ┌──────────────┐         │
                    │ branch_product│         │
                    │  (pivot)      │◄────────┘
                    └──────────────┘

┌──────────┐   1:N   ┌─────────────────┐   1:N   ┌─────────────────────┐
│   Firm   │────────►│ PurchaseInvoice │────────►│ PurchaseInvoiceItem │
└──────────┘         └─────────────────┘         └─────────────────────┘

┌──────────┐   1:N   ┌──────────────┐
│  Staff   │────────►│ StaffMotion  │
│          │────────►│    Task      │
└──────────┘         └──────────────┘

┌──────────────────┐   1:N   ┌──────────┐
│IncomeExpenseType │────────►│  Income  │
│                  │────────►│  Expense │
└──────────────────┘         └──────────┘

┌────────────┐   1:N   ┌────────────────┐
│ StockCount │────────►│ StockCountItem │
└────────────┘         └────────────────┘

┌──────────┐   1:N   ┌───────────────┐
│ EInvoice │────────►│ EInvoiceItem  │
└──────────┘         └───────────────┘

┌──────────┐   1:N   ┌─────────────────┐
│  Branch  │────────►│ HardwareDevice  │
└──────────┘         └─────────────────┘

┌─────────────────┐
│ HardwareDriver  │  ← Sürücü Kataloğu (108 cihaz)
│ (bağımsız tablo) │
└─────────────────┘
```

---

## 2. Tablo Detayları

### 2.1 `users`

| Sütun | Tip | Açıklama |
|-------|-----|----------|
| id | bigint (PK) | Otomatik artan |
| name | string | Kullanıcı adı |
| email | string (unique) | E-posta |
| password | string | Şifreli parola |
| remember_token | string | Oturum hatırlama |
| timestamps | — | created_at, updated_at |

**İlişkiler:** `sales` → HasMany Sale

---

### 2.2 `branches`

| Sütun | Tip | Açıklama |
|-------|-----|----------|
| id | bigint (PK) | — |
| external_id | string (idx) | Harici sistem ID |
| name | string | Şube adı |
| code | string | Şube kodu |
| address | string | Adres |
| phone | string | Telefon |
| city | string | Şehir |
| district | string | İlçe |
| is_active | boolean (def: true) | Aktiflik durumu |
| timestamps | — | — |
| deleted_at | timestamp | Soft delete |

**İlişkiler:** `products` (N:M pivot), `sales`, `staff`, `purchaseInvoices`, `stockCounts`

---

### 2.3 `categories`

| Sütun | Tip | Açıklama |
|-------|-----|----------|
| id | bigint (PK) | — |
| external_id | string (idx) | Harici sistem ID |
| name | string | Kategori adı |
| parent_id | FK → categories | Üst kategori |
| sort_order | integer (def: 0) | Sıralama |
| is_active | boolean (def: true) | Aktiflik |
| timestamps | — | — |

**İlişkiler:** `parent` (self-ref), `children` (self-ref), `products`

---

### 2.4 `products`

| Sütun | Tip | Açıklama |
|-------|-----|----------|
| id | bigint (PK) | — |
| external_id | string (idx) | Harici sistem ID |
| barcode | string (idx) | Barkod |
| name | string | Ürün adı |
| description | text | Açıklama |
| category_id | FK → categories | Kategori |
| variant_type | string (nullable) | Varyant tipi |
| parent_id | FK → products | Ebeveyn ürün (varyant) |
| unit | string (def: 'Adet') | Birim |
| purchase_price | decimal(12,2) | Alış fiyatı |
| sale_price | decimal(12,2) | Satış fiyatı |
| vat_rate | integer (def: 20) | KDV oranı (%) |
| stock_quantity | decimal(12,2) | Stok miktarı |
| critical_stock | decimal(12,2) | Kritik stok seviyesi |
| image_url | string | Ürün görseli |
| is_active | boolean (def: true) | Aktiflik |
| timestamps | — | — |
| deleted_at | timestamp | Soft delete |

**İlişkiler:** `category`, `parent` (self-ref), `variants` (self-ref), `branches` (N:M), `saleItems`, `stockMovements`, `purchaseInvoiceItems`, `stockCountItems`

**Özel Metodlar:**
- `profitMargin` (accessor) → Kâr marjı yüzdesi
- `isLowStock()` → Stok kritik seviyenin altında mı?

---

### 2.5 `branch_product` (Pivot)

| Sütun | Tip | Açıklama |
|-------|-----|----------|
| id | bigint (PK) | — |
| branch_id | FK → branches | Şube |
| product_id | FK → products | Ürün |
| stock_quantity | decimal(12,2) | Şube bazlı stok |
| sale_price | decimal(12,2) | Şube bazlı fiyat |
| timestamps | — | — |

**Unique:** (branch_id, product_id)

---

### 2.6 `customers`

| Sütun | Tip | Açıklama |
|-------|-----|----------|
| id | bigint (PK) | — |
| external_id | string (idx) | Harici ID |
| name | string | Müşteri adı |
| type | string (def: 'individual') | Bireysel/kurumsal |
| tax_number | string | Vergi numarası |
| tax_office | string | Vergi dairesi |
| phone, email | string | İletişim |
| address, city, district | string | Adres |
| balance | decimal(14,2) | Cari bakiye |
| notes | text | Notlar |
| is_active | boolean | Aktiflik |
| timestamps, deleted_at | — | — |

**İlişkiler:** `sales`, `transactions`  
**Accessor:** `formattedBalance` → "Alacak: ₺1.000,00" / "Borç: ₺500,00"

---

### 2.7 `sales`

| Sütun | Tip | Açıklama |
|-------|-----|----------|
| id | bigint (PK) | — |
| external_id | string (idx) | Harici ID |
| receipt_no | string (idx) | Fiş numarası |
| branch_id | FK → branches | Şube |
| customer_id | FK → customers | Müşteri |
| user_id | FK → users | Kasiyer |
| payment_method | string (def: 'cash') | Ödeme yöntemi |
| subtotal | decimal(14,2) | Ara toplam |
| vat_total | decimal(14,2) | KDV toplamı |
| discount_total | decimal(14,2) | İndirim toplamı |
| discount | decimal(14,2) | İndirim |
| grand_total | decimal(14,2) | Genel toplam |
| cash_amount | decimal(14,2) | Nakit tutar |
| card_amount | decimal(14,2) | Kart tutarı |
| total_items | integer | Kalem sayısı |
| status | string (def: 'completed') | Durum |
| notes, note | text/string | Notlar |
| staff_name | string | Personel adı |
| application | string | Kaynak uygulama |
| sold_at | datetime | Satış tarihi |
| timestamps, deleted_at | — | — |

**İlişkiler:** `branch`, `customer`, `user`, `items`  
**Accessors:** `paymentMethodLabel`, `statusLabel`

---

### 2.8 `sale_items`

| Sütun | Tip | Açıklama |
|-------|-----|----------|
| id | bigint (PK) | — |
| sale_id | FK → sales (cascade) | Satış |
| product_id | FK → products | Ürün |
| product_name | string | Ürün adı (snapshot) |
| barcode | string | Barkod (snapshot) |
| quantity | decimal(12,2) | Miktar |
| unit_price | decimal(12,2) | Birim fiyat |
| discount | decimal(12,2) | İndirim |
| vat_rate | integer (def: 20) | KDV % |
| vat_amount | decimal(12,2) | KDV tutarı |
| total | decimal(14,2) | Toplam |
| timestamps | — | — |

---

### 2.9 `account_transactions`

| Sütun | Tip | Açıklama |
|-------|-----|----------|
| id | bigint (PK) | — |
| external_id | string (idx) | Harici ID |
| customer_id | FK → customers (cascade) | Müşteri |
| type | string | sale / payment / refund / adjustment |
| amount | decimal(14,2) | Tutar |
| balance_after | decimal(14,2) | İşlem sonrası bakiye |
| description | string | Açıklama |
| reference | string | Referans kodu |
| transaction_date | datetime | İşlem tarihi |
| timestamps | — | — |

---

### 2.10 `firms`

| Sütun | Tip | Açıklama |
|-------|-----|----------|
| id | bigint (PK) | — |
| external_id | string (idx) | — |
| name, tax_number, tax_office | string | Firma bilgileri |
| phone, email, address, city | string | İletişim |
| balance | decimal(14,2) | Bakiye |
| notes | text | — |
| is_active | boolean | — |
| timestamps, deleted_at | — | — |

**İlişkiler:** `purchaseInvoices`

---

### 2.11 `purchase_invoices`

| Sütun | Tip | Açıklama |
|-------|-----|----------|
| id | bigint (PK) | — |
| external_id | string (idx) | — |
| invoice_type | string (def: 'purchase') | Fatura tipi |
| invoice_no | string (idx) | Fatura numarası |
| firm_id | FK → firms | Firma |
| branch_id | FK → branches | Şube |
| waybill_no, document_no | string | İrsaliye / Belge no |
| payment_type | string (def: 'cash') | Ödeme yöntemi |
| total_items | integer | Kalem sayısı |
| total_amount | decimal(14,2) | Toplam tutar |
| invoice_date, shipment_date | date | Tarihler |
| notes | text | — |
| timestamps, deleted_at | — | — |

---

### 2.12 `purchase_invoice_items`

| Sütun | Tip | Açıklama |
|-------|-----|----------|
| id | bigint (PK) | — |
| purchase_invoice_id | FK (cascade) | Fatura |
| product_id | FK | Ürün |
| product_name, barcode | string | Snapshot |
| quantity | decimal(12,2) | Miktar |
| unit_price | decimal(12,2) | Birim fiyat |
| total | decimal(14,2) | Toplam |
| timestamps | — | — |

---

### 2.13 `income_expense_types`

| Sütun | Tip | Açıklama |
|-------|-----|----------|
| id | bigint (PK) | — |
| name | string | Tür adı |
| direction | string | income / expense |
| is_active | boolean (def: true) | — |
| timestamps | — | — |

---

### 2.14 `incomes` / `expenses` (aynı yapı)

| Sütun | Tip | Açıklama |
|-------|-----|----------|
| id | bigint (PK) | — |
| external_id | string (idx) | — |
| income_expense_type_id | FK | Tür |
| type_name | string | Tür adı (snapshot) |
| note | text | Not |
| amount | decimal(14,2) | Tutar |
| payment_type | string (def: 'cash') | Ödeme |
| date | date | Tarih |
| time | time | Saat |
| timestamps | — | — |

---

### 2.15 `stock_movements`

| Sütun | Tip | Açıklama |
|-------|-----|----------|
| id | bigint (PK) | — |
| type | string | Hareket tipi (in/out) |
| barcode | string | Ürün barkodu |
| product_id | FK → products | Ürün |
| product_name | string | Snapshot |
| transaction_code | string | İşlem kodu |
| note | text | Not |
| firm_customer | string | Firma/müşteri adı |
| payment_type | string | Ödeme |
| quantity | decimal(12,2) | Miktar |
| remaining | decimal(12,2) | Kalan |
| unit_price | decimal(12,2) | Birim fiyat |
| total | decimal(14,2) | Toplam |
| movement_date | datetime | Hareket tarihi |
| timestamps | — | — |

---

### 2.16 `stock_counts` / `stock_count_items`

**stock_counts:**
| Sütun | Tip | Açıklama |
|-------|-----|----------|
| id | bigint (PK) | — |
| branch_id | FK → branches | Şube |
| status | string (def: 'draft') | Durum |
| total_items | integer | Kalem sayısı |
| notes | text | — |
| counted_at | datetime | Sayım tarihi |

**stock_count_items:**
| Sütun | Tip | Açıklama |
|-------|-----|----------|
| id | bigint (PK) | — |
| stock_count_id | FK (cascade) | Sayım |
| product_id | FK | Ürün |
| barcode, product_name | string | — |
| system_quantity | decimal(12,2) | Sistem miktarı |
| counted_quantity | decimal(12,2) | Sayılan |
| difference | decimal(12,2) | Fark |

---

### 2.17 `staff` / `staff_motions`

**staff:**
| Sütun | Tip | Açıklama |
|-------|-----|----------|
| id | bigint (PK) | — |
| external_id | string (idx) | — |
| name | string | Ad |
| role | string | Rol |
| branch_id | FK → branches | Şube |
| phone, email | string | İletişim |
| total_sales | decimal(14,2) | Toplam satış |
| total_transactions | integer | İşlem sayısı |
| is_active | boolean | — |

**staff_motions:**
| Sütun | Tip | Açıklama |
|-------|-----|----------|
| id | bigint (PK) | — |
| staff_id | FK → staff | Personel |
| staff_name | string | Ad snapshot |
| action | string | Aksiyon |
| description | text | Açıklama |
| application | string | Uygulama |
| detail | text | Detay |
| action_date | datetime | Tarih |

---

### 2.18 `tasks`

| Sütun | Tip | Açıklama |
|-------|-----|----------|
| id | bigint (PK) | — |
| title | string | Başlık |
| description | text | Açıklama |
| status | string (def: 'pending') | Durum |
| priority | string (def: 'normal') | Öncelik |
| assigned_to | FK → staff | Atanan personel |
| due_date | date | Son tarih |
| completed_at | datetime | Tamamlanma |
| timestamps | — | — |

---

### 2.19 `payment_types`

| Sütun | Tip | Açıklama |
|-------|-----|----------|
| id | bigint (PK) | — |
| name | string | Ad |
| code | string | Kod |
| is_active | boolean | Aktiflik |
| sort_order | integer | Sıra |

---

### 2.20 `e_invoices`

| Sütun | Tip | Açıklama |
|-------|-----|----------|
| id | bigint (PK) | — |
| external_id | string (unique) | Harici ID |
| invoice_no | string | Fatura no |
| uuid | string (unique) | UUID |
| direction | enum | outgoing / incoming |
| type | enum | invoice / return / withholding / exception / special |
| scenario | enum | basic / commercial / export |
| status | string (def: 'draft') | draft / sent / accepted / rejected / cancelled |
| customer_id | FK | Müşteri |
| receiver_* | string | Alıcı bilgileri |
| branch_id, sale_id | FK | Referanslar |
| currency | string (def: 'TRY') | Para birimi |
| exchange_rate | decimal(12,4) | Döviz kuru |
| subtotal, vat_total, discount_total | decimal(12,2) | Tutarlar |
| grand_total, withholding_total | decimal(12,2) | Toplam |
| vat_rate | integer (def: 20) | KDV % |
| notes | text | Notlar |
| payment_method | string | Ödeme yöntemi |
| invoice_date | date | Fatura tarihi |
| sent_at, received_at | datetime | Gönderim/alım |
| meta | json | Ek veriler |
| timestamps, deleted_at | — | — |

---

### 2.21 `e_invoice_items`

| Sütun | Tip | Açıklama |
|-------|-----|----------|
| id | bigint (PK) | — |
| e_invoice_id | FK (cascade) | E-fatura |
| product_id | FK | Ürün |
| product_name, product_code | string | — |
| unit | string (def: 'Adet') | Birim |
| quantity | decimal(12,3) | Miktar |
| unit_price | decimal(12,2) | Birim fiyat |
| discount | decimal(12,2) | İndirim |
| vat_rate | integer (def: 20) | KDV % |
| vat_amount | decimal(12,2) | KDV tutarı |
| total | decimal(12,2) | Toplam |

---

### 2.22 `e_invoice_settings`

Tekil ayar tablosu (singleton pattern — `EInvoiceSetting::current()`)

| Sütun | Tip | Açıklama |
|-------|-----|----------|
| id | bigint (PK) | — |
| company_name | string | Şirket adı |
| tax_number, tax_office | string | Vergi bilgileri |
| address, city, district | string | Adres |
| phone, email, web | string | İletişim |
| integrator | string | Entegratör adı |
| api_key, api_secret | string (hidden) | API kimlik bilgileri |
| sender_alias, receiver_alias | string | Posta kutusu alias |
| auto_send | boolean (def: false) | Otomatik gönderim |
| is_active | boolean (def: false) | Aktiflik |
| default_scenario | string (def: 'basic') | Varsayılan senaryo |
| default_currency | string (def: 'TRY') | Varsayılan para birimi |
| default_vat_rate | integer (def: 20) | Varsayılan KDV |
| invoice_prefix | string | Fatura ön eki |
| invoice_counter | integer (def: 1) | Sayaç |
| meta | json | Ek ayarlar |

---

### 2.23 `hardware_devices`

| Sütun | Tip | Açıklama |
|-------|-----|----------|
| id | bigint (PK) | — |
| name | string | Cihaz adı |
| type | string | receipt_printer / label_printer / a4_printer / barcode_scanner / scale / cash_drawer / customer_display |
| connection | string | usb / network / serial / bluetooth |
| protocol | string | escpos / tspl / zpl / epl / cas / dibal vb. |
| model | string | Model adı |
| manufacturer | string | Üretici |
| vendor_id | string | USB Vendor ID |
| product_id | string | USB Product ID |
| ip_address | string | Ağ IP adresi |
| port | integer | TCP port (def: 9100) |
| serial_port | string | Seri port yolu |
| baud_rate | integer | Baud hızı |
| mac_address | string | Bluetooth MAC |
| settings | json | Ek ayarlar |
| is_default | boolean (def: false) | Türünde varsayılan mı |
| is_active | boolean (def: true) | Aktiflik |
| last_seen_at | datetime | Son bağlantı zamanı |
| status | string (def: 'disconnected') | connected / disconnected / error |
| branch_id | FK → branches | Şube |
| timestamps | — | — |

**İlişkiler:** `branch` (belongsTo Branch)

**Scopelar:** `active()`, `ofType(type)`, `default(type)`

**Metodlar:**
- `getTypeLabel()` → İnsan okunabilir tür adı
- `getDefault(type)` → Türünde varsayılan cihazı bul
- `markConnected()` / `markDisconnected()` → Durum güncelle
- `toDriverConfig()` → Frontend JS sürücüsü için JSON çıktısı

---

### 2.24 `hardware_drivers` (Sürücü Kataloğu)

> **Amaç:** Bilinen POS donanım cihazlarının referans veritabanı. `hardware_devices` tablosundan bağımsızdır; cihaz eklerken model seçimi için kullanılır.
> **Kaynak:** `database/data/hardware-drivers.json` (108 cihaz)

| Sütun | Tip | Açıklama |
|-------|-----|----------|
| id | bigint (PK) | — |
| device_type | string (idx) | receipt_printer / label_printer / a4_printer / barcode_scanner / scale / cash_drawer / customer_display |
| manufacturer | string (idx) | Üretici (Epson, Zebra, HP vb.) |
| model | string | Model adı |
| vendor_id | string | USB Vendor ID (hex) |
| product_id | string | USB Product ID (hex) |
| protocol | string | escpos / tspl / zpl / system / hid / cas / vfd vb. |
| connections | json | Desteklenen bağlantı türleri ["usb","network"] |
| features | json | Cihaz özellikleri ["auto_cutter","wifi"] |
| specs | json | Teknik özellikler {paper_width, dpi, ...} |
| notes | text | Ek notlar |
| timestamps | — | created_at, updated_at |

**İndeksler:** `device_type`, `manufacturer`, `[vendor_id, product_id]`

**Scopelar:** `ofType(type)`, `byManufacturer(name)`, `search(query)`, `withUsb()`

**Accessor’lar:**
- `full_name` → "Üretici Model" birleşimi
- `type_label` → Türkçe cihaz türü adı
- `type_icon` → Font Awesome ikon sınıfı
- `type_color` → Tailwind renk sınıfı
- `connection_labels` → Bağlantı türleri Türkçe

**Metodlar:**
- `getSpec(key)` → Teknik özellik değeri
- `hasFeature(feature)` → Özellik kontrolü
- `supportsConnection(type)` → Bağlantı desteği kontrolü
- `toDriverConfig()` → JS driver için JSON çıktısı

**Statik Metodlar:**
- `getStats()` → Tür bazlı cihaz sayıları
- `getManufacturers()` → Tüm üretici listesi

**Cihaz Dağılımı (108 toplam):**

| Kategori | Sayı | Örnek Üreticiler |
|----------|------|------------------|
| A4 Yazıcı | 24 | HP, Canon, Epson, Brother, Samsung, Kyocera, Xerox |
| Fiş Yazıcı | 20 | Epson, Star, Bixolon, Citizen, Xprinter, Rongta |
| Etiket Yazıcı | 16 | Zebra, TSC, Brother, Godex, Xprinter, HPRT |
| Terazi | 16 | CAS, Dibal, DIGI, Mettler Toledo, Bizerba, Ohaus |
| Barkod Okuyucu | 12 | Symbol, Honeywell, Datalogic, Netum, Newland |
| Kasa Çekmecesi | 10 | Star, APG, Posiflex, MAKEN, Safescan, VPOS |
| Müşteri Ekranı | 10 | Posiflex, Birch, Epson, Bixolon, Firich |
