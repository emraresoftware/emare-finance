# Emare Finance — Proje Hafızası (Memory Document)

> 🔗 **Ortak Hafıza:** [`EMARE_ORTAK_HAFIZA.md`](/Users/emre/Desktop/Emare/EMARE_ORTAK_HAFIZA.md) — Tüm Emare ekosistemi, sunucu bilgileri, standartlar ve proje envanteri için bak.


> Bu dosya, yazılımın tüm detaylarını, mimari kararlarını, veritabanı yapısını,  
> modül listesini, deploy sürecini ve kaldığımız noktayı kayıt altına alır.  
> **Son güncelleme:** 3 Mart 2026

---

## 1. YAZILIM NEDİR?

**Emare Finance**, multi-tenant SaaS mimarisinde çalışan, Türkçe arayüzlü, tam donanımlı bir **POS (Point of Sale) ve İşletme Yönetim Yazılımı**dır.

### Temel Özellikler
- Çok şubeli, çok kullanıcılı, çok rollü mimari
- Gerçek zamanlı POS satış ekranı (12 ödeme yöntemi)
- Ürün yönetimi (barkod, varyant, alt ürün, etiket tasarımı, terazi barkodu)
- Müşteri/cari yönetimi (bakiye takibi, segmentler, sadakat puanı)
- Satış raporları (günlük, tarihsel, ürünsel, grupsal, korelasyon, kâr analizi)
- E-Fatura & e-Arşiv Fatura entegrasyonu
- Birleşik Faturalar modülü (giden/gelen/irsaliye/e-arşiv)
- Alış faturaları ve tedarikçi takibi
- Gelir-gider yönetimi
- Personel yönetimi ve hareket takibi
- Stok yönetimi (hareket, sayım, transfer)
- SMS yönetimi (14 otomasyon türü, şablon, senaryo, kara liste)
- Pazarlama (teklifler, kampanyalar, segmentler, mesajlar, sadakat programı)
- Donanım yönetimi (yazıcı, barkod okuyucu, sürücü kataloğu)
- Dijital ekran (digital signage) — cihaz, playlist, zamanlama
- Mobil işlemler (kamera ile ürün ekleme, barkod tarama, hızlı sipariş)
- AI sohbet asistanı (Google Gemini entegrasyonu)
- Geri bildirim sistemi (widget + admin paneli)
- Süper admin paneli (çoklu firma yönetimi)
- Deploy webhook (GitHub/GitLab push → otomatik deploy)
- Tekrarlayan faturalar
- Vergi oranları ve hizmet kategorileri yönetimi
- Entegrasyon başvuru sistemi

---

## 2. TEKNİK ALTYAPI

### Sunucu
| Öğe | Değer |
|-----|-------|
| İşletim Sistemi | AlmaLinux 9.7 |
| IP Adresi | `77.92.152.3` |
| SSH Portu | `2222` (ed25519 key auth) |
| SSH Kullanıcı | `root` |
| Web Sunucu | Nginx 1.20.1, port `3000` |
| PHP | 8.4.18 (PHP-FPM) |
| Veritabanı | MariaDB (MySQL uyumlu) |
| SELinux | Aktif (restorecon gerekli) |
| Firewall Portları | 2222/tcp, 3000/tcp, 8080/tcp |

### Framework & Dil
| Öğe | Değer |
|-----|-------|
| Framework | Laravel 12 |
| PHP | 8.4.18 |
| Veritabanı Adı | `emarefinance` |
| DB Kullanıcı | `emarefinance` |
| DB Şifre | `Emr3Fin2026` |
| DB Host | `127.0.0.1:3306` |
| DB Connection | `mysql` (MariaDB) |
| Proje Dizini (Sunucu) | `/var/www/emarefinance` |
| Proje Dizini (Lokal) | `/Users/emre/Desktop/Emare Finance` |

### Frontend Stack
| Kütüphane | Versiyon / CDN |
|-----------|----------------|
| Tailwind CSS | CDN — `cdn.tailwindcss.com` |
| Alpine.js | 3.x — `cdn.jsdelivr.net/npm/alpinejs@3.x.x` |
| Font Awesome | 6.5.0 — `cdnjs.cloudflare.com` |
| Chart.js | 4.x — `cdn.jsdelivr.net` |
| JsBarcode | 3.x — `cdn.jsdelivr.net` |
| Inter Font | Google Fonts |

### Tasarım Sistemi
- **Ana Marka Rengi:** Indigo `#6366f1` (brand-500)
- **Font:** Inter, system-ui, sans-serif
- **Referans Doküman:** `docs/DESIGN_GUIDE.md`

---

## 3. KİMLİK DOĞRULAMA & YETKİLENDİRME

### Test Kullanıcıları
| E-posta | Şifre | Rol | Detay |
|---------|-------|-----|-------|
| `emre@emareas.com` | `Emre2025` | Süper Admin | `is_super_admin=true`, user_id=1 |
| `testkayit@test.com` | `Test12345` | Admin (starter plan) | Test amaçlı oluşturuldu |

### Kayıt Akışı (Registration)
1. Kullanıcı `/kayit` sayfasında form doldurur (name, business_name, email, password, industry)
2. `RegisterController::register()` çalışır:
   - Starter plan bulunur (`Plan::where('code','starter')`)
   - Yeni `Tenant` oluşturulur (14 gün trial)
   - Varsayılan `Branch` oluşturulur
   - `User` oluşturulur (tenant_id + branch_id atanır)
   - `UserRole` oluşturulur (admin rolü, **`created_at => now()` zorunlu**)
   - `core_pos` modülü tenant'a aktive edilir
3. Otomatik login yapılır, `/panel`'e yönlendirilir

> **ÖNEMLİ BUG FIX:** `user_roles` tablosunda `created_at` alanı NOT NULL ama default değeri yok.  
> `UserRole` modeli `$timestamps = false` kullanıyor. Bu yüzden `'created_at' => now()` her zaman  
> explicit olarak verilmelidir.

### Middleware Sistemi
| Alias | Middleware Class | Açıklama |
|-------|-----------------|----------|
| `module` | `CheckModule` | Route'a erişim için modülün aktif olması gerekir |
| `permission` | `CheckPermission` | Kullanıcının ilgili izne sahip olması gerekir |
| `tenant` | `ResolveTenant` | Her request'te tenant context'i çözümler |
| `super_admin` | `SuperAdmin` | Sadece `is_super_admin=true` kullanıcılar |

- `ResolveTenant` middleware'i hem `web` hem `api` grubuna append edilmiştir
- Bootstrap: Laravel 12 style (`bootstrap/app.php`)

### Roller (5 adet)
| Code | Ad | Scope | Açıklama |
|------|----|-------|----------|
| `admin` | Yönetici | tenant | TÜM izinlere sahip |
| `manager` | Şube Müdürü | branch | Rol/modül/ayar/kullanıcı yönetimi HARİÇ her şey |
| `cashier` | Kasiyer | branch | Satış, ürün görüntüleme, müşteri, stok, temel raporlar |
| `accounting` | Muhasebe | tenant | Satış/gelir/gider/e-fatura/rapor/müşteri görüntüleme |
| `warehouse` | Depo Sorumlusu | branch | Ürün/stok/sayım/transfer/temel raporlar |

### İzinler (46 adet)
| Grup | İzinler |
|------|---------|
| Satış | `sales.view`, `sales.create`, `sales.cancel`, `sales.refund`, `sales.discount` |
| Ürün | `products.view`, `products.create`, `products.edit`, `products.delete` |
| Müşteri | `customers.view`, `customers.create`, `customers.edit`, `customers.delete` |
| Stok | `stock.view`, `stock.adjust`, `stock.count`, `stock.transfer` |
| Rapor | `reports.basic`, `reports.advanced`, `reports.export` |
| Gelir-Gider | `income.view`, `income.create`, `expense.view`, `expense.create` |
| E-Fatura | `einvoice.view`, `einvoice.create`, `einvoice.cancel` |
| Personel | `staff.view`, `staff.create`, `staff.edit` |
| Donanım | `hardware.view`, `hardware.manage` |
| Şube | `branches.view`, `branches.create`, `branches.edit` |
| Yönetim | `users.view`, `users.create`, `users.edit`, `roles.manage`, `modules.manage`, `settings.manage` |

---

## 4. MODÜL SİSTEMİ

### Modüller (10 adet)
| # | Code | Ad | Core? | Scope | Bağımlılıklar |
|---|------|----|-------|-------|----------------|
| 1 | `core_pos` | Temel POS | ✅ Evet | tenant | — |
| 2 | `hardware` | Donanım Sürücüleri | Hayır | branch | core_pos |
| 3 | `einvoice` | E-Fatura / E-Arşiv | Hayır | tenant | core_pos |
| 4 | `income_expense` | Gelir-Gider Yönetimi | Hayır | tenant | core_pos |
| 5 | `staff` | Personel Yönetimi | Hayır | both | core_pos |
| 6 | `advanced_reports` | Gelişmiş Raporlar | Hayır | tenant | core_pos |
| 7 | `api_access` | API Erişimi | Hayır | tenant | core_pos |
| 8 | `mobile_premium` | Mobil Premium | Hayır | tenant | core_pos, api_access |
| 9 | `marketing` | Pazarlama | Hayır | tenant | core_pos |
| 10 | `sms` | SMS Yönetimi | Hayır | tenant | core_pos |

### Planlar (3 adet)
| Code | Ad | Aylık | Yıllık | Limitler | Modüller |
|------|----|-------|--------|----------|----------|
| `starter` | Başlangıç | ₺299 | ₺2.990 | 1 şube, 3 kullanıcı, 500 ürün, 200 müşteri | core_pos |
| `business` | İşletme | ₺599 | ₺5.990 | 5 şube, 15 kullanıcı, 5000 ürün, 2000 müşteri | core_pos, hardware, einvoice, income_expense, staff |
| `enterprise` | Kurumsal | ₺1.299 | ₺12.990 | Sınırsız | TÜM modüller |

---

## 5. VERİTABANI ŞEMASI

### Core Tablolar (2026_02_28_000001)
| Tablo | Açıklama | Önemli Alanlar |
|-------|----------|----------------|
| `branches` | Şubeler | external_id, name, code, address, city, is_active, softDeletes |
| `categories` | Ürün grupları | parent_id (self-ref), sort_order |
| `products` | Ürünler | barcode, category_id, purchase_price, sale_price, vat_rate, stock_quantity, critical_stock, softDeletes |
| `branch_product` | Şube-ürün stok ilişkisi | branch_id, product_id, stock_quantity, sale_price (unique composite) |
| `customers` | Müşteriler/Cariler | type (individual/company), tax_number, balance, softDeletes |
| `sales` | Satışlar | receipt_no, branch_id, customer_id, payment_method, subtotal/vat/discount/grand_total, cash/card_amount, sold_at, status, softDeletes |
| `sale_items` | Satış kalemleri | sale_id, product_id, quantity, unit_price, discount, vat_rate/amount, total |
| `account_transactions` | Cari hesap hareketleri | customer_id, type (sale/payment/refund/adjustment), amount, balance_after |
| `staff` | Personeller | branch_id, role, total_sales, total_transactions |

### Ek Tablolar (2026_03_01_000001)
| Tablo | Açıklama |
|-------|----------|
| `firms` | Tedarikçiler/Firmalar — tax_number, balance, softDeletes |
| `purchase_invoices` | Alış faturaları — invoice_type, firm_id, branch_id, total_amount |
| `purchase_invoice_items` | Alış faturası kalemleri |
| `income_expense_types` | Gelir/gider türleri — direction (income/expense) |
| `incomes` | Gelirler — amount, payment_type, date |
| `expenses` | Giderler — amount, payment_type, date |
| `stock_movements` | Stok hareketleri — type (in/out/sale/refund/transfer/count), quantity, remaining |
| `stock_counts` | Stok sayımları — branch_id, status (draft/completed) |
| `stock_count_items` | Sayım kalemleri — system_quantity, counted_quantity, difference |
| `staff_motions` | Personel hareketleri — action (delete_item/delete_receipt/leave_page) |

### E-Fatura Tabloları (2026_03_01_000002)
| Tablo | Açıklama |
|-------|----------|
| `e_invoices` | E-Faturalar — direction (outgoing/incoming), type (invoice/return/withholding/exception/special), scenario (basic/commercial/export/e_arsiv), status (draft/sent/accepted/rejected/cancelled), customer_id, currency, vat_rate, meta JSON |
| `e_invoice_items` | E-Fatura kalemleri — product_id, quantity, unit_price, vat_rate, total |
| `e_invoice_settings` | E-Fatura ayarları — company_name, tax_number, integrator, api_key/secret, auto_send, default_scenario |

### e-Arşiv Ek Alanları (2026_03_03_000002 — e_invoices tablosuna eklendi)
| Alan | Tip | Açıklama |
|------|-----|----------|
| `recipient_type` | enum(individual/corporate) | Alıcı tipi |
| `is_internet_sale` | boolean | İnternet satışı mı? |
| `internet_sale_platform` | string | Platform adı |
| `internet_sale_url` | string | Satış URL'si |
| `payment_date` | date | Ödeme tarihi |
| `payment_platform` | string | Ödeme platformu |
| `tc_kimlik_no` | string(11) | TC Kimlik No |
| `earsiv_report_no` | string | e-Arşiv rapor numarası |

### Donanım Tabloları (2026_03_01_000002 & 000003)
| Tablo | Açıklama |
|-------|----------|
| `hardware_devices` | Kayıtlı cihazlar — type (printer/barcode_reader/scale/cash_register/display/other), brand, model, connection_type, ip_address, port |
| `hardware_drivers` | Sürücü kataloğu — type, manufacturer, model_name, supported_features JSON |

### Entegrasyon Tabloları (2026_03_01_121203 & 130000)
| Tablo | Açıklama |
|-------|----------|
| `integration_requests` | Entegrasyon başvuruları — user_id, tenant_id, integration_type, platform, status, admin_notes |
| `users.is_super_admin` | Boolean alan eklendi |

### Dijital Ekran Tabloları (2026_03_01_500001)
| Tablo | Açıklama |
|-------|----------|
| `signage_contents` | İçerikler — type (image/video/text/product/html/menu_board/announcement/qr_code) |
| `signage_devices` | Ekran cihazları — resolution, orientation, status |
| `signage_playlists` | Oynatma listeleri |
| `signage_playlist_items` | Playlist içerikleri — duration, sort_order |
| `signage_schedules` | Zamanlamalar — day_of_week, start_time, end_time |

### Vergi & Tekrarlayan (2026_03_02_000001)
| Tablo | Açıklama |
|-------|----------|
| `tax_rates` | Vergi oranları — type, name, rate, is_active |
| `service_categories` | Hizmet kategorileri |
| `recurring_invoices` | Tekrarlayan faturalar — frequency (monthly/quarterly/yearly), next_date, end_date |
| `recurring_invoice_items` | Tekrarlayan fatura kalemleri |
| `quotes` | Teklifler — status (draft/sent/accepted/rejected/expired) |
| `quote_items` | Teklif kalemleri |

### Multi-Tenant & RBAC Tabloları (2026_03_02_100001–100012)
| Tablo | Açıklama |
|-------|----------|
| `modules` | Modül tanımları — code, name, is_core, scope (tenant/branch/both), dependencies JSON |
| `plans` | Paket planları — price_monthly/yearly, limits JSON |
| `plan_modules` | Plan-modül pivot — hangi plan hangi modülleri içerir |
| `tenants` | Firmalar/kiracılar — name, slug, status (active/suspended/cancelled), plan_id, trial_ends_at, meta JSON |
| `tenant_modules` | Tenant-modül pivot — aktif modüller |
| `branch_modules` | Şube-modül pivot — şube bazlı modül kontrolü |
| `module_audit_logs` | Modül değişiklik logları |
| `roles` | Roller — code, name, scope (tenant/branch), is_system |
| `permissions` | İzinler — code, name, module_code, group |
| `role_permissions` | Rol-izin pivot |
| `user_roles` | Kullanıcı-rol pivot — user_id, role_id, tenant_id, branch_id, **created_at (NOT NULL, default yok!)** |
| `users` tablosuna eklenenler: `tenant_id`, `branch_id`, `role_id` (eski), `is_super_admin` |

### Pazarlama Tabloları (2026_03_02_300001)
| Tablo | Açıklama |
|-------|----------|
| `campaigns` | Kampanyalar — type, discount_type/value, start/end_date |
| `campaign_usages` | Kampanya kullanımları — sale_id, customer_id, discount_amount |
| `customer_segments` | Müşteri segmentleri — conditions JSON |
| `customer_segment_members` | Segment üyeleri pivot |
| `marketing_messages` | Pazarlama mesajları — channel (email/sms/push), target (all/segment/manual) |
| `marketing_message_logs` | Mesaj gönderim logları — status (sent/delivered/failed/read) |
| `loyalty_programs` | Sadakat programları — type (points/stamps), earn/redeem rules |
| `loyalty_points` | Puan/damga bakiyeleri — customer_id, points_balance |

### SMS Tabloları (2026_03_02_400001 & 500001)
| Tablo | Açıklama |
|-------|----------|
| `sms_settings` | SMS ayarları — provider (netgsm/iletimerkezi/mutlucell/twilio/custom), api credentials, sender_name, monthly_limit |
| `sms_templates` | SMS şablonları — category, variables JSON, character_count |
| `sms_scenarios` | SMS senaryoları — trigger_event, conditions JSON, delay_minutes |
| `sms_logs` | SMS gönderim logları — phone, message, status, provider_message_id, cost |
| `sms_blacklist` | Kara liste — phone, reason |
| `sms_automation_configs` | Otomasyon yapılandırmaları — type (14 tür), is_enabled, schedule, filters JSON |
| `sms_automation_queue` | Otomasyon kuyruğu — phone, message, scheduled_at, sent_at, status |

### Geri Bildirim Tablosu (2026_03_03_000003)
| Tablo | Açıklama |
|-------|----------|
| `feedback_messages` | user_id, page_url, category (bug/suggestion/question/other), priority (low/normal/high/critical), message, admin_reply, replied_by, replied_at, status (open/in_progress/resolved/closed), screenshot_path, meta JSON |

---

## 6. DOSYA YAPISI

### Controllers (32 adet)
```
app/Http/Controllers/
├── Controller.php                  # Ana base controller
├── ChatController.php              # AI sohbet (Gemini)
├── CustomerController.php          # Müşteri/cari CRUD
├── DashboardController.php         # Ana panel
├── DeployWebhookController.php     # GitHub/GitLab deploy webhook
├── EInvoiceController.php          # E-Fatura modülü
├── FaturaController.php            # Birleşik faturalar (giden/gelen/irsaliye/e-arşiv)
├── FeedbackController.php          # Geri bildirim (widget + admin)
├── FirmController.php              # Tedarikçi/firma listeleme
├── HardwareController.php          # Donanım + sürücü yönetimi + API
├── IncomeExpenseController.php     # Gelir-gider
├── IntegrationController.php       # Entegrasyon başvuruları
├── MarketingController.php         # Pazarlama (teklif/kampanya/segment/mesaj/sadakat)
├── MobileController.php            # Mobil (kamera/barkod/hızlı sipariş)
├── PaymentTypeController.php       # Ödeme tipleri
├── ProductController.php           # Ürün CRUD + grup/varyant/etiket/iade
├── PurchaseInvoiceController.php   # Alış faturaları
├── RecurringInvoiceController.php  # Tekrarlayan faturalar
├── ReportController.php            # 10 rapor türü
├── SaleController.php              # Satış listeleme + detay
├── ScreenController.php            # POS/sipariş/terminal ekranları
├── ServiceCategoryController.php   # Hizmet kategorileri
├── SignageController.php           # Dijital ekran CRUD + display
├── SmsController.php               # SMS (ayar/şablon/senaryo/otomasyon/log/kara liste/gönderim)
├── StaffController.php             # Personel listeleme
├── StockController.php             # Stok hareketleri + sayım
├── TaskController.php              # Görevler
├── TaxRateController.php           # Vergi oranları + API
├── Admin/
│   ├── IntegrationRequestController.php
│   ├── ModuleController.php        # Modül açma/kapama
│   ├── RoleController.php          # Rol & izin yönetimi
│   └── UserController.php          # Kullanıcı CRUD
├── Api/
│   ├── CustomerApiController.php
│   ├── DashboardApiController.php
│   ├── ProductApiController.php
│   ├── ReportApiController.php
│   ├── SaleApiController.php
│   └── StockApiController.php
├── Auth/
│   ├── ForgotPasswordController.php
│   ├── LoginController.php
│   └── RegisterController.php
└── SuperAdmin/
    └── FirmController.php          # Çoklu firma yönetimi (CRUD + kullanıcı/şube ekleme)
```

### Models (62 adet)
```
app/Models/
├── AccountTransaction.php    ├── Branch.php
├── BranchModule.php          ├── Campaign.php
├── CampaignUsage.php         ├── Category.php
├── Customer.php              ├── CustomerSegment.php
├── EInvoice.php              ├── EInvoiceItem.php
├── EInvoiceSetting.php       ├── Expense.php
├── FeedbackMessage.php       ├── Firm.php
├── HardwareDevice.php        ├── HardwareDriver.php
├── Income.php                ├── IncomeExpenseType.php
├── IntegrationRequest.php    ├── LoyaltyPoint.php
├── LoyaltyProgram.php        ├── MarketingMessage.php
├── MarketingMessageLog.php   ├── Module.php
├── ModuleAuditLog.php        ├── PaymentType.php
├── Permission.php            ├── Plan.php
├── Product.php               ├── PurchaseInvoice.php
├── PurchaseInvoiceItem.php   ├── Quote.php
├── QuoteItem.php             ├── RecurringInvoice.php
├── RecurringInvoiceItem.php  ├── Role.php
├── Sale.php                  ├── SaleItem.php
├── ServiceCategory.php       ├── SignageContent.php
├── SignageDevice.php         ├── SignagePlaylist.php
├── SignagePlaylistItem.php   ├── SignageSchedule.php
├── SmsAutomationConfig.php   ├── SmsAutomationQueue.php
├── SmsBlacklist.php          ├── SmsLog.php
├── SmsScenario.php           ├── SmsSetting.php
├── SmsTemplate.php           ├── Staff.php
├── StaffMotion.php           ├── StockCount.php
├── StockCountItem.php        ├── StockMovement.php
├── Task.php                  ├── TaxRate.php
├── Tenant.php                ├── TenantModule.php
├── User.php                  └── UserRole.php
```

### Services
```
app/Services/
├── GeminiService.php     # Google Gemini API entegrasyonu
├── ModuleService.php     # Modül aktiflik kontrolü
├── SmsService.php        # SMS gönderim servisi (multi-provider)
└── TenantContext.php     # Tenant context resolver (singleton)
```

### Middleware
```
app/Http/Middleware/
├── CheckModule.php       # Route'a erişim için modül aktif mi?
├── CheckPermission.php   # Kullanıcının izni var mı?
├── ResolveTenant.php     # Her request'te tenant context çözümle
└── SuperAdmin.php        # Sadece super admin erişimi
```

### Artisan Commands
```
app/Console/Commands/
├── ProcessSmsAutomations.php   # php artisan sms:process-automations [--dry-run]
└── TestAllModules.php          # php artisan test:all-modules
```

### View Dizini
```
resources/views/
├── welcome.blade.php           # Landing page
├── dashboard.blade.php         # Ana panel
├── layouts/
│   └── app.blade.php           # Ana layout (sidebar + header + content)
├── partials/
│   ├── chat-widget.blade.php   # AI sohbet widget'ı
│   └── feedback-widget.blade.php # Geri bildirim widget'ı
├── auth/                       # Giriş, kayıt, şifre sıfırlama
├── screens/
│   ├── pos.blade.php           # POS satış ekranı (12 ödeme yöntemi)
│   ├── order.blade.php         # Sipariş ekranı
│   ├── terminal.blade.php      # Terminal ekranı
│   └── menu.blade.php          # Ekran seçim menüsü
├── products/                   # Ürün listeleme, düzenleme, gruplar, etiket vb.
├── customers/                  # Müşteri/cari listeleme, detay, düzenleme
├── sales/                      # Satış listeleme, detay
├── reports/                    # 10 rapor görünümü
│   ├── index.blade.php         # Rapor ana sayfa
│   ├── daily.blade.php         # Günlük rapor
│   ├── historical.blade.php    # Tarihsel rapor
│   ├── products.blade.php      # Ürünsel rapor
│   ├── groups.blade.php        # Grupsal rapor
│   ├── correlation.blade.php   # Korelasyon rapor
│   ├── profit.blade.php        # Kâr analizi
│   ├── sales.blade.php         # Satış raporu
│   ├── stock-movement.blade.php # Stok hareket raporu
│   └── staff-movement.blade.php # Personel hareket raporu
├── faturalar/                  # Birleşik fatura modülü
│   ├── index.blade.php         # Ana sayfa (istatistikler)
│   ├── create.blade.php        # Yeni fatura oluşturma
│   ├── show.blade.php          # Fatura detay
│   ├── outgoing.blade.php      # Giden faturalar
│   ├── incoming.blade.php      # Gelen faturalar
│   ├── waybills.blade.php      # İrsaliyeler
│   └── earsiv.blade.php        # e-Arşiv faturalar
├── einvoices/                  # E-Fatura modülü (eski)
├── invoices/                   # Alış faturaları
├── firms/                      # Tedarikçi/firmalar
├── income-expense/             # Gelir-gider
├── stock/                      # Stok hareketleri + sayım
├── staff/                      # Personel yönetimi
├── hardware/                   # Donanım yönetimi
├── marketing/                  # Pazarlama (quotes/campaigns/segments/messages/loyalty)
├── sms/                        # SMS yönetimi (templates/scenarios/automations/logs/blacklist/compose)
├── signage/                    # Dijital ekran (display + yönetim)
├── mobile/                     # Mobil (kamera/barkod/sipariş)
├── chat/                       # AI sohbet
├── feedback/                   # Geri bildirim admin paneli
│   └── index.blade.php
├── integrations/               # Entegrasyon başvuruları
├── recurring-invoices/         # Tekrarlayan faturalar
├── tax-rates/                  # Vergi oranları
├── service-categories/         # Hizmet kategorileri
├── payment-types/              # Ödeme tipleri
├── tasks/                      # Görevler
├── admin/                      # Admin paneli (modüller/roller/kullanıcılar/entegrasyon)
├── super-admin/                # Süper admin paneli (firmalar/dashboard)
└── errors/                     # Hata sayfaları (403, 404, 419, 429, 500)
```

---

## 7. ROUTE YAPISI (URL'ler)

### Guest (Auth Gerektirmez)
| URL | Method | Controller@Action | Name |
|-----|--------|-------------------|------|
| `/giris` | GET/POST | LoginController | login |
| `/kayit` | GET/POST | RegisterController | register |
| `/sifremi-unuttum` | GET/POST | ForgotPasswordController | password.request/email |
| `/sifre-sifirla/{token}` | GET/POST | ForgotPasswordController | password.reset/update |
| `/` | GET | Closure → welcome | home |
| `/cikis` | POST | LoginController@logout | logout |

### Authenticated (Auth Gerekli)
| Prefix | Route Name | Controller | Açıklama |
|--------|-----------|------------|----------|
| `/panel` | dashboard | DashboardController | Ana panel |
| `/raporlar/*` | reports.* | ReportController | 10 rapor türü |
| `/cariler/*` | customers.* | CustomerController | Müşteri CRUD |
| `/urunler/*` | products.* | ProductController | Ürün CRUD + grup/varyant/etiket |
| `/satislar/*` | sales.* | SaleController | Satış listeleme |
| `/faturalar/*` | faturalar.* | FaturaController | Birleşik faturalar (giden/gelen/irsaliye/e-arşiv) |
| `/alis-faturalari/*` | invoices.* | PurchaseInvoiceController | Alış faturaları |
| `/firmalar/*` | firms.* | FirmController | Tedarikçiler |
| `/e-faturalar/*` | einvoices.* | EInvoiceController | E-Fatura modülü |
| `/stok/*` | stock.* | StockController | Stok hareketleri + sayım |
| `/gelir-gider/*` | income_expense.* | IncomeExpenseController | Gelir-gider |
| `/personeller/*` | staff.* | StaffController | Personel yönetimi |
| `/gorevler` | tasks.index | TaskController | Görevler |
| `/odeme-tipleri` | payment_types.index | PaymentTypeController | Ödeme tipleri |
| `/vergi-oranlari/*` | tax_rates.* | TaxRateController | Vergi oranları CRUD |
| `/hizmet-kategorileri/*` | service_categories.* | ServiceCategoryController | Hizmet kategorileri CRUD |
| `/tekrarlayan-faturalar/*` | recurring_invoices.* | RecurringInvoiceController | Tekrarlayan faturalar |
| `/donanim/*` | hardware.* | HardwareController | Donanım + sürücü CRUD |
| `/api/hardware/*` | — | HardwareController (API) | Cihaz/sürücü API (JS driver) |
| `/api/tax-rates/*` | — | TaxRateController (API) | Vergi API |
| `/entegrasyonlar/*` | integrations.* | IntegrationController | Entegrasyon başvuruları |
| `/ekranlar/*` | screens.* | ScreenController | POS/sipariş/terminal ekranları |
| `/dijital-ekran/*` | signage.* | SignageController | Digital signage CRUD + display + API |
| `/admin/*` | admin.* | Admin Controllers | Modül/rol/kullanıcı/entegrasyon yönetimi |
| `/pazarlama/*` | marketing.* | MarketingController | Teklifler/kampanyalar/segmentler/mesajlar/sadakat |
| `/sms/*` | sms.* | SmsController | SMS tüm yönetimi |
| `/sohbet/*` | chat.* | ChatController | AI sohbet |
| `/mobil/*` | mobile.* | MobileController | Mobil işlemler |
| `/geribildirim/*` | feedback.* | FeedbackController | Geri bildirim (submit/list/admin/status/reply) |

### Süper Admin
| Prefix | Controller | Açıklama |
|--------|------------|----------|
| `/super-admin/` | SuperAdmin\FirmController | Firma dashboard |
| `/super-admin/firmalar/*` | SuperAdmin\FirmController | Firma CRUD + kullanıcı/şube ekleme |

### API (v1 — Sanctum Auth)
| Prefix | Controller | Açıklama |
|--------|------------|----------|
| `/api/v1/dashboard` | DashboardApiController | Dashboard verileri |
| `/api/v1/products/*` | ProductApiController | Ürün listeleme + düşük stok + kategoriler |
| `/api/v1/sales/*` | SaleApiController | Satış listeleme + özet |
| `/api/v1/customers/*` | CustomerApiController | Müşteri listeleme + satışları |
| `/api/v1/reports/*` | ReportApiController | Günlük/top ürünler/gelir chart/ödeme yöntemleri |
| `/api/v1/stock/*` | StockApiController | Stok genel bakış/hareketler/uyarılar |

### Diğer
| URL | Açıklama |
|-----|----------|
| `/deploy/webhook` | GitHub/GitLab push webhook (CSRF bypass) |
| `/deploy/status` | Deploy durumu |

---

## 8. POS EKRANI (Detay)

### Ödeme Yöntemleri (12 adet)
POS ekranı (`/ekranlar/pos`) 12 ödeme yöntemini destekler:

| # | Yöntem | İkon | Açıklama |
|---|--------|------|----------|
| 1 | Nakit | fa-money-bill-wave | Nakit ödeme + para üstü hesaplama |
| 2 | Kredi Kartı | fa-credit-card | Direkt kart ödemesi |
| 3 | Veresiye | fa-handshake | Müşteri seçili olmalı |
| 4 | Havale | fa-university | Banka havalesi |
| 5 | EFT | fa-exchange-alt | Elektronik fon transferi |
| 6 | Sanal POS | fa-laptop | Online kart ödemesi |
| 7 | POS | fa-cash-register | Fiziksel POS cihazı |
| 8 | Yemek Kartı | fa-utensils | Yemek kartı/çeki |
| 9 | Setcard | fa-id-card | Setcard ödemesi |
| 10 | Pluspay | fa-mobile-alt | Pluspay mobil ödeme |
| 11 | Çoklu Ödeme | fa-layer-group | Split payment (birden fazla yöntem) |
| 12 | Diğer | fa-ellipsis-h | Diğer ödeme yöntemleri |

### POS Keyboard Shortcuts
| Tuş | İşlev |
|-----|-------|
| F5 | Nakit ödeme |
| F6 | Kart ödeme |
| F7 | Diğer yöntemler (modal aç) |
| Escape | Modal kapat |

### POS Alpine.js Component: `posApp()`
- `cartItems[]`, `selectedCategory`, `searchQuery`, `selectedCustomer`
- `paymentModal`, `selectedPaymentMethod`, `splitPaymentOpen`
- `splitPayments[]`, `cashReceived`, `paymentLabels{}`
- Methods: `addToCart()`, `quickPay()`, `openPaymentModal()`, `confirmPayment()`, `completeCashPayment()`, `openSplitPayment()`, `completeSplitPayment()`, `completeSale()`

---

## 9. GERİ BİLDİRİM SİSTEMİ

### Widget (`partials/feedback-widget.blade.php`)
- Konumu: `fixed bottom-6 right-24 z-[9998]` (AI chat widget'ın solunda)
- Amber renkli yuvarlak buton
- İki sekme: **Yeni** (gönderim) ve **Geçmiş** (kullanıcının kendi bildirimleri)
- Kategoriler: Bug 🐛, Öneri 💡, Soru ❓, Diğer 📝
- Öncelikler: Düşük, Normal, Yüksek, Kritik
- AJAX submit: `POST /geribildirim/gonder`
- AJAX history: `GET /geribildirim/benim`

### Admin Paneli (`/geribildirim/yonetim`)
- 6 istatistik kartı (toplam, açık, işlemde, çözülen, bug sayısı, bugünkü)
- Filtreler: arama, durum, kategori, öncelik
- Kart tabanlı mesaj listesi
- Durum değiştirme (open → in_progress → resolved → closed)
- Admin yanıtlama (inline form)

### FeedbackController Methodları
| Method | Route | Açıklama |
|--------|-------|----------|
| `index()` | GET /geribildirim/yonetim | Admin listesi + istatistikler |
| `store()` | POST /geribildirim/gonder | AJAX ile yeni feedback kaydet |
| `myFeedback()` | GET /geribildirim/benim | Kullanıcının son 20 feedback'i (JSON) |
| `updateStatus()` | PATCH /geribildirim/{id}/durum | Durum güncelleme |
| `reply()` | POST /geribildirim/{id}/yanit | Admin yanıtı |

---

## 10. SMS OTOMASYON SİSTEMİ

### 14 Otomasyon Türü
| Tür | Açıklama |
|-----|----------|
| birthday | Doğum günü tebriği |
| inactive_customer | Pasif müşteri hatırlatması |
| cargo_notification | Kargo bildirimi |
| payment_reminder | Ödeme hatırlatması |
| welcome_message | Hoşgeldin mesajı |
| after_sale | Satış sonrası teşekkür |
| low_stock_alert | Düşük stok uyarısı |
| appointment_reminder | Randevu hatırlatması |
| campaign_notification | Kampanya bildirimi |
| debt_reminder | Borç hatırlatması |
| daily_summary | Günlük özet |
| weekly_report | Haftalık rapor |
| seasonal_greeting | Mevsimsel kutlama |
| feedback_request | Geri bildirim talebi |

### SMS Providers
Desteklenen: `netgsm`, `iletimerkezi`, `mutlucell`, `twilio`, `custom`

---

## 11. BLADE DİREKTİFLERİ

### Özel Blade Direktifleri (AppServiceProvider'da tanımlı)
```blade
@money(1234.56)              → "1.234,56 ₺"
@tarih($date)                → "01 Mart 2026"
@tarihSaat($date)            → "01 Mart 2026 14:30"

@module('marketing')         → Modül aktif mi?
@permission('sales.create')  → İzin var mı?
@role('admin')               → Rol var mı?
@superadmin                  → Süper admin mi?
```

---

## 12. DEPLOY SÜRECİ

### Adım Adım Deploy
```bash
# 1. Dosyaları sunucuya yükle
scp -P 2222 <dosya> root@77.92.152.3:/tmp/<isim>

# 2. SSH ile bağlan ve dosyaları yerine koy
ssh -p 2222 root@77.92.152.3
cd /var/www/emarefinance
cp /tmp/<isim> <hedef_yol>

# 3. İzinleri düzelt
chown -R nginx:nginx .
restorecon -R .

# 4. Migration varsa çalıştır
php artisan migrate --force

# 5. Cache temizle
php artisan view:clear
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# 6. PHP-FPM yeniden başlat
systemctl restart php-fpm

# 7. Temp dosyaları temizle
rm -f /tmp/deploy_*.php
```

### Önemli Deploy Notları
- SELinux aktif olduğu için `restorecon -R .` şart
- `chown -R nginx:nginx .` dosya sahipliği nginx olmalı
- Çoklu dosya yüklerken her scp ayrı satırda yapılmalı (|| parsing sorunları)
- PHP-FPM restart sonrası opcache temizlenir

---

## 13. BİLİNEN SORUNLAR & FIX'LER

### Fix 1: Kayıt 500 Hatası ✅
- **Sorun:** `user_roles.created_at` alanı NOT NULL, default değer yok, `UserRole` modeli `$timestamps=false`
- **Çözüm:** `RegisterController.php` satır 95'te `'created_at' => now()` eklendi
- **Dosya:** `app/Http/Controllers/Auth/RegisterController.php`

### Fix 2: strftime MySQL Uyumsuzluk ✅
- **Sorun:** `SaleApiController` SQLite `strftime("%H", sold_at)` kullanıyordu, MySQL'de yok
- **Çözüm:** `HOUR(sold_at)` ile değiştirildi
- **Dosya:** `app/Http/Controllers/Api/SaleApiController.php`

### Fix 3: sms.logs Route Tanımsız (Önceki Oturumda Düzeltildi) ✅
- **Sorun:** Layout'ta `route('sms.logs')` referansı vardı ama route adı `sms.logs.index` idi
- **Çözüm:** Layout'taki referans düzeltildi

---

## 14. SIDEBAR YAPISI

Layout dosyasındaki (`layouts/app.blade.php`) sidebar menü sırası:

```
📊 Panel (dashboard)
📊 Raporlar (açılır) → Günlük, Tarihsel, Ürünsel, Grupsal, Korelasyon, Stok Hareket, Personel Hareket, Satışlar, Kâr
👥 Cariler (customers)
📦 Ürünler (açılır) → Liste, Gruplar, Alt Ürünler, Varyantlar, İadeler, İade Talepleri, Etiket, Etiket Tasarla, Terazi Barkod
🛒 Satışlar (sales)
📄 Faturalar (açılır) → Genel, Giden, Gelen, İrsaliyeler, e-Arşiv Faturalar
📥 Alış Faturaları
🏭 Firmalar
📋 E-Faturalar (modül: einvoice)
📦 Stok (açılır) → Hareketler, Sayım
💰 Gelir-Gider (modül: income_expense, açılır) → Gelirler, Giderler, Türler
👷 Personeller (modül: staff, açılır) → Liste, Hareketler
---
📋 Vergi & Hizmet (açılır) → Vergi Oranları, Hizmet Kategorileri, Tekrarlayan Faturalar
💳 Ödeme Tipleri
📋 Görevler
🖥️ Ekranlar (açılır) → POS, Sipariş, Terminal
🔧 Donanım (modül: hardware)
🔗 Entegrasyonlar
📊 Dijital Ekran (açılır) → Yönetim, Görüntüle
📣 Pazarlama (modül: marketing, açılır) → Dashboard, Teklifler, Kampanyalar, Segmentler, Mesajlar, Sadakat
📱 SMS Yönetimi (modül: sms, açılır) → Dashboard, Şablonlar, Senaryolar, Otomasyonlar, Hızlı Gönder, Loglar, Kara Liste, Ayarlar
📱 Mobil İşlemler (açılır) → Dashboard, Kamera Ürün Ekleme, Barkod Tarama, Hızlı Sipariş
💬 AI Asistan
---
🔧 Yönetim (admin rolü)
├── Modüller
├── Roller & İzinler
├── Kullanıcılar
├── Entegrasyon Başvuruları (bekleyen sayısı badge)
└── Geri Bildirimler (açık sayısı badge)
---
🛡️ Süper Admin (is_super_admin)
└── Süper Admin Paneli
```

---

## 15. SERVİS KATMANI

### TenantContext (Singleton)
- Auth user'dan `tenant_id` ve `branch_id` çözümler
- Her request'te `ResolveTenant` middleware ile set edilir
- Global scope'larda kullanılır

### ModuleService (Singleton)
- `isActive($moduleCode)`: Modül tenant/branch için aktif mi?
- `CheckModule` middleware ve `@module` Blade directive'i tarafından kullanılır

### GeminiService
- Google Gemini API ile AI sohbet
- `ChatController` tarafından kullanılır

### SmsService  
- Multi-provider SMS gönderim (netgsm, iletimerkezi, mutlucell, twilio, custom)
- `ProcessSmsAutomations` komutu ve `SmsController` tarafından kullanılır

---

## 16. SCRAPER SİSTEMİ

Proje kökünde `scraper/` dizininde Node.js tabanlı web scraping araçları:

```
scraper/
├── cookie-login.js       # Cookie ile giriş
├── explore-site.js       # Site keşfi
├── explore.js            # Genel keşif
├── helpers.js            # Yardımcı fonksiyonlar
├── login-test.js         # Giriş testi
├── scrape-branches.js    # Şube verisi çekme
├── scrape-categories.js  # Kategori verisi çekme
├── scrape-customers.js   # Müşteri verisi çekme
├── scrape-products.js    # Ürün verisi çekme
├── scrape-reports.js     # Rapor verisi çekme
├── scrape-sales.js       # Satış verisi çekme
├── scrape-staff.js       # Personel verisi çekme
├── scrape-stock.js       # Stok verisi çekme
└── screenshots/          # Scraping ekran görüntüleri
```

Ayrıca kök dizinde `scraper.py` (Python) ve `requirements.txt` mevcut.

---

## 17. SEEDERS

### DatabaseSeeder Çalıştırma Sırası
1. `ModuleSeeder` — 10 modül
2. `PlanSeeder` — 3 plan + plan_modules ilişkileri
3. `RoleSeeder` — 5 rol + 46 izin + role-permission ilişkileri
4. `HardwareDriverSeeder` — Donanım sürücü kataloğu
5. `TaxRateSeeder` — Varsayılan vergi oranları
6. `IndustryTemplateSeeder` — Sektör şablonları

### Ek Seeder'lar (manuel çalıştırılır)
- `DemoUserSeeder` — Demo kullanıcılar
- `ArchiveSeeder` — Arşiv test verileri

---

## 18. e-ARŞİV FATURA DURUMU

### Tamamlanan (4/9)
1. ✅ Migration — e-Arşiv alanları eklendi
2. ✅ EInvoice model — `scopeEarsiv()`, `scopeNotEarsiv()` scope'ları
3. ✅ FaturaController — `earsiv()` metodu
4. ✅ earsiv.blade.php — e-Arşiv listeleme görünümü

### Yapılacak (5/9)
5. ⬜ create.blade.php — e-Arşiv alanları formu (recipient_type, tc_kimlik_no, internet satış alanları)
6. ⬜ show.blade.php — e-Arşiv bilgilerini gösterme
7. ⬜ Route eklendi ✅ (`/faturalar/e-arsiv`)
8. ⬜ Sidebar linki eklendi ✅
9. ⬜ Dashboard'da e-Arşiv istatistikleri

---

## 19. MEVCUT DURUMDA DEPLOY EDİLMİŞ DOSYALAR

Son deploy tarihi: **3 Mart 2026, ~16:25 UTC**

Sunucuya deploy edilen dosyalar:
- `app/Http/Controllers/FeedbackController.php` ✅
- `app/Http/Controllers/Auth/RegisterController.php` ✅ (created_at fix)
- `app/Http/Controllers/Api/SaleApiController.php` ✅ (strftime fix)
- `app/Http/Controllers/FaturaController.php` ✅ (earsiv method)
- `app/Models/FeedbackMessage.php` ✅
- `app/Models/EInvoice.php` ✅ (earsiv scopes)
- `resources/views/screens/pos.blade.php` ✅ (12 ödeme yöntemi)
- `resources/views/partials/feedback-widget.blade.php` ✅
- `resources/views/feedback/index.blade.php` ✅
- `resources/views/layouts/app.blade.php` ✅ (sidebar + widget include)
- `resources/views/faturalar/earsiv.blade.php` ✅
- `resources/views/faturalar/index.blade.php` ✅
- `routes/web.php` ✅ (feedback + e-arşiv routes)
- `database/migrations/2026_03_03_000002_add_earsiv_fields_to_e_invoices.php` ✅ (migrate edildi)
- `database/migrations/2026_03_03_000003_create_feedback_messages_table.php` ✅ (migrate edildi)

---

## 20. NEREDE KALDIK? (SON DURUM)

### Tamamlanan İşler
1. ✅ **Kayıt sistemi düzeltildi** — Registration 500 hatası giderildi
2. ✅ **POS ödeme yöntemleri** — 12 ödeme yöntemi eklendi (modal + split payment + nakit para üstü)
3. ✅ **strftime MySQL fix** — SaleApiController'da HOUR() kullanıldı
4. ✅ **Geri bildirim sistemi** — FeedbackController, widget, admin paneli, routes, sidebar linki
5. ✅ **e-Arşiv route + sidebar** — `/faturalar/e-arsiv` route'u ve sidebar linki eklendi
6. ✅ **Tüm değişiklikler deploy edildi** — Sunucuda test edildi, çalışıyor

### Devam Edilecek İşler
1. ⬜ **e-Arşiv create form** — `create.blade.php`'ye e-Arşiv spesifik alanlar eklenecek:
   - recipient_type (bireysel/kurumsal) toggle
   - tc_kimlik_no alanı (bireysel alıcı için)
   - İnternet satışı checkbox + platform/URL alanları
   - Ödeme tarihi + platform alanları
2. ⬜ **e-Arşiv show view** — `show.blade.php` güncellenecek (e-Arşiv bilgileri gösterilecek)
3. ⬜ **Dashboard'da e-Arşiv istatistikleri** — Ana panelde e-Arşiv kartları

### Test Sonuçları (3 Mart 2026)
| Endpoint | Durum | HTTP Code |
|----------|-------|-----------|
| Kayıt (`POST /kayit`) | ✅ Çalışıyor | 302 → /panel |
| Dashboard (`/panel`) | ✅ Çalışıyor | 200 |
| POS (`/ekranlar/pos`) | ✅ Çalışıyor | 200 |
| Feedback Admin (`/geribildirim/yonetim`) | ✅ Çalışıyor | 200 |
| Feedback Submit (`POST /geribildirim/gonder`) | ✅ Çalışıyor | 200 (JSON) |
| Feedback My (`/geribildirim/benim`) | ✅ Çalışıyor | 200 |
| e-Arşiv (`/faturalar/e-arsiv`) | ✅ Çalışıyor | 200 |

---

*Bu dosya proje hafızası olarak kullanılır. Her önemli değişiklikte güncellenmelidir.*
