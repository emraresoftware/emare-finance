# 📁 Emare Finance — Dosya Yapısı

> **Oluşturulma:** Otomatik  
> **Amaç:** Yapay zekalar kod yazmadan önce mevcut dosya yapısını incelemeli

---

## Proje Dosya Ağacı

```
/Users/emre/Desktop/Emare/Emare Finance
├── .DS_Store
├── .editorconfig
├── .env
├── .env.example
├── .gitattributes
├── .gitignore
├── DOSYA_YAPISI.md
├── EMARE_AI_COLLECTIVE.md
├── EMARE_ANAYASA.md
├── EMARE_ORTAK_CALISMA -> /Users/emre/Desktop/Emare/EMARE_ORTAK_CALISMA
├── EMARE_ORTAK_HAFIZA.md
├── EmareHup
│   ├── .DS_Store
│   ├── .env
│   ├── .gitignore
│   ├── DevM
│   │   ├── .DS_Store
│   │   ├── .cursor
│   │   │   └── rules
│   │   ├── README.md
│   │   ├── TALIMATLAR.md
│   │   ├── apps
│   │   │   └── web
│   │   ├── context
│   │   │   ├── DECISIONS.md
│   │   │   ├── PROMPT-BOOTSTRAP.md
│   │   │   ├── SESSION-CONTEXT.md
│   │   │   └── TASKS.md
│   │   ├── docs
│   │   │   ├── AGENT-PROJE-KLASORLERI.md
│   │   │   ├── DB-SCHEMA.md
│   │   │   ├── MASTER-ARCHITECTURE.md
│   │   │   ├── ROADMAP-90D.md
│   │   │   └── TALIMATLAR-SISTEMI.md
│   │   ├── örnek proje
│   │   │   ├── .DS_Store
│   │   │   ├── TALIMATLAR.md
│   │   │   ├── projects
│   │   │   ├── ws-1-orchestrator
│   │   │   ├── ws-2-model-broker
│   │   │   ├── ws-3-ide-runner
│   │   │   └── ws-cursor
│   │   ├── package.json
│   │   ├── scripts
│   │   │   ├── run-talimatlar-ai.js
│   │   │   ├── run-talimatlar.js
│   │   │   └── watch-talimatlar.js
│   │   └── services
│   │       ├── ide-runner
│   │       ├── model-broker
│   │       └── orchestrator
│   ├── README.md
│   ├── config.yaml
│   ├── data
│   │   └── registry.json
│   ├── devm_bridge.py
│   ├── docs
│   │   ├── ARCHITECTURE.md
│   │   ├── DECISIONS.md
│   │   ├── INDEX.md
│   │   ├── MODULLER.md
│   │   ├── PROJE_GELISTIRME.md
│   │   └── SESSION-CONTEXT.md
│   ├── emare_core.py
│   ├── factory_worker.py
│   ├── logs
│   │   ├── emare_hub.log
│   │   └── talimat_runner.log
│   ├── main.py
│   ├── modules
│   │   ├── cagri_merkezi
│   │   │   ├── README.md
│   │   │   ├── TALIMATLAR.md
│   │   │   ├── __init__.py
│   │   │   ├── main.py
│   │   │   └── manifest.json
│   │   └── crm
│   │       ├── README.md
│   │       ├── TALIMATLAR.md
│   │       ├── __init__.py
│   │       ├── main.py
│   │       └── manifest.json
│   └── scripts
│       └── talimat_runner.py
├── README.md
├── TECHNICAL_SPEC.md
├── app
│   ├── Console
│   │   └── Commands
│   │       ├── ProcessSmsAutomations.php
│   │       └── TestAllModules.php
│   ├── Http
│   │   ├── Controllers
│   │   │   ├── Admin
│   │   │   ├── Api
│   │   │   ├── Auth
│   │   │   ├── ChatController.php
│   │   │   ├── Controller.php
│   │   │   ├── CustomerController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── DeployWebhookController.php
│   │   │   ├── EInvoiceController.php
│   │   │   ├── FaturaController.php
│   │   │   ├── FeedbackController.php
│   │   │   ├── FirmController.php
│   │   │   ├── HardwareController.php
│   │   │   ├── IncomeExpenseController.php
│   │   │   ├── IntegrationController.php
│   │   │   ├── MarketingController.php
│   │   │   ├── MobileController.php
│   │   │   ├── PaymentTypeController.php
│   │   │   ├── ProductController.php
│   │   │   ├── PurchaseInvoiceController.php
│   │   │   ├── RecurringInvoiceController.php
│   │   │   ├── ReportController.php
│   │   │   ├── SaleController.php
│   │   │   ├── ScreenController.php
│   │   │   ├── ServiceCategoryController.php
│   │   │   ├── SignageController.php
│   │   │   ├── SmsController.php
│   │   │   ├── StaffController.php
│   │   │   ├── StockController.php
│   │   │   ├── SuperAdmin
│   │   │   ├── TaskController.php
│   │   │   └── TaxRateController.php
│   │   └── Middleware
│   │       ├── CheckModule.php
│   │       ├── CheckPermission.php
│   │       ├── ResolveTenant.php
│   │       └── SuperAdmin.php
│   ├── Models
│   │   ├── AccountTransaction.php
│   │   ├── Branch.php
│   │   ├── BranchModule.php
│   │   ├── Campaign.php
│   │   ├── CampaignUsage.php
│   │   ├── Category.php
│   │   ├── Customer.php
│   │   ├── CustomerSegment.php
│   │   ├── EInvoice.php
│   │   ├── EInvoiceItem.php
│   │   ├── EInvoiceSetting.php
│   │   ├── Expense.php
│   │   ├── FeedbackMessage.php
│   │   ├── Firm.php
│   │   ├── HardwareDevice.php
│   │   ├── HardwareDriver.php
│   │   ├── Income.php
│   │   ├── IncomeExpenseType.php
│   │   ├── IntegrationRequest.php
│   │   ├── LoyaltyPoint.php
│   │   ├── LoyaltyProgram.php
│   │   ├── MarketingMessage.php
│   │   ├── MarketingMessageLog.php
│   │   ├── Module.php
│   │   ├── ModuleAuditLog.php
│   │   ├── PaymentType.php
│   │   ├── Permission.php
│   │   ├── Plan.php
│   │   ├── Product.php
│   │   ├── PurchaseInvoice.php
│   │   ├── PurchaseInvoiceItem.php
│   │   ├── Quote.php
│   │   ├── QuoteItem.php
│   │   ├── RecurringInvoice.php
│   │   ├── RecurringInvoiceItem.php
│   │   ├── Role.php
│   │   ├── Sale.php
│   │   ├── SaleItem.php
│   │   ├── ServiceCategory.php
│   │   ├── SignageContent.php
│   │   ├── SignageDevice.php
│   │   ├── SignagePlaylist.php
│   │   ├── SignagePlaylistItem.php
│   │   ├── SignageSchedule.php
│   │   ├── SmsAutomationConfig.php
│   │   ├── SmsAutomationQueue.php
│   │   ├── SmsBlacklist.php
│   │   ├── SmsLog.php
│   │   ├── SmsScenario.php
│   │   ├── SmsSetting.php
│   │   ├── SmsTemplate.php
│   │   ├── Staff.php
│   │   ├── StaffMotion.php
│   │   ├── StockCount.php
│   │   ├── StockCountItem.php
│   │   ├── StockMovement.php
│   │   ├── Task.php
│   │   ├── TaxRate.php
│   │   ├── Tenant.php
│   │   ├── TenantModule.php
│   │   ├── User.php
│   │   └── UserRole.php
│   ├── Providers
│   │   └── AppServiceProvider.php
│   ├── Services
│   │   ├── GeminiService.php
│   │   ├── ModuleService.php
│   │   ├── SmsService.php
│   │   └── TenantContext.php
│   └── Traits
│       └── BelongsToTenant.php
├── artisan
├── bootstrap
│   ├── app.php
│   ├── cache
│   │   ├── .gitignore
│   │   ├── packages.php
│   │   └── services.php
│   └── providers.php
├── composer.json
├── composer.lock
├── config
│   ├── app.php
│   ├── auth.php
│   ├── cache.php
│   ├── database.php
│   ├── filesystems.php
│   ├── hardware.php
│   ├── industry.php
│   ├── logging.php
│   ├── mail.php
│   ├── modules.php
│   ├── queue.php
│   ├── services.php
│   └── session.php
├── database
│   ├── .gitignore
│   ├── data
│   │   └── hardware-drivers.json
│   ├── database.sqlite
│   ├── factories
│   │   └── UserFactory.php
│   ├── migrations
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   ├── 0001_01_01_000002_create_jobs_table.php
│   │   ├── 2026_02_28_000001_create_core_tables.php
│   │   ├── 2026_03_01_000001_create_additional_tables.php
│   │   ├── 2026_03_01_000002_add_variant_type_to_products_table.php
│   │   ├── 2026_03_01_000002_create_e_invoices_table.php
│   │   ├── 2026_03_01_000002_create_hardware_devices_table.php
│   │   ├── 2026_03_01_000003_create_hardware_drivers_table.php
│   │   ├── 2026_03_01_121203_create_integration_requests_table.php
│   │   ├── 2026_03_01_130000_add_is_super_admin_to_users_table.php
│   │   ├── 2026_03_01_500001_create_signage_tables.php
│   │   ├── 2026_03_02_000001_create_tax_and_recurring_tables.php
│   │   ├── 2026_03_02_100001_create_modules_table.php
│   │   ├── 2026_03_02_100002_create_plans_table.php
│   │   ├── 2026_03_02_100003_create_plan_modules_table.php
│   │   ├── 2026_03_02_100004_create_tenants_table.php
│   │   ├── 2026_03_02_100005_create_tenant_modules_table.php
│   │   ├── 2026_03_02_100006_create_branch_modules_table.php
│   │   ├── 2026_03_02_100007_create_module_audit_logs_table.php
│   │   ├── 2026_03_02_100008_create_roles_table.php
│   │   ├── 2026_03_02_100009_create_permissions_table.php
│   │   ├── 2026_03_02_100010_create_role_permissions_table.php
│   │   ├── 2026_03_02_100011_create_user_roles_table.php
│   │   ├── 2026_03_02_100012_add_tenant_and_rbac_columns.php
│   │   ├── 2026_03_02_300001_create_marketing_tables.php
│   │   ├── 2026_03_02_400001_create_sms_tables.php
│   │   ├── 2026_03_02_500001_add_sms_automations.php
│   │   ├── 2026_03_03_000001_add_waybill_fields_to_e_invoices.php
│   │   ├── 2026_03_03_000002_add_earsiv_fields_to_e_invoices.php
│   │   └── 2026_03_03_000003_create_feedback_messages_table.php
│   └── seeders
│       ├── ArchiveSeeder.php
│       ├── DatabaseSeeder.php
│       ├── DemoUserSeeder.php
│       ├── HardwareDriverSeeder.php
│       ├── IndustryTemplateSeeder.php
│       ├── ModuleSeeder.php
│       ├── PlanSeeder.php
│       ├── RoleSeeder.php
│       └── TaxRateSeeder.php
├── deploy-zero.sh
├── deploy.sh
├── docs
│   ├── 01-genel-bakis.md
│   ├── 02-veritabani-ve-modeller.md
│   ├── 03-web-uygulamasi.md
│   ├── 04-api-ve-mobil.md
│   ├── 05-donanim-suruculeri.md
│   ├── 06-proje-gelistirmeleri.md
│   ├── DESIGN_GUIDE.md
│   ├── emarefinance-hafiza.md
│   └── planning
│       ├── 01-modul-bazli-veritabani-tasarimi.md
│       ├── 02-paketleme-ve-fiyatlandirma-stratejisi.md
│       ├── 03-sektorel-versiyonlar.md
│       ├── 04-rol-ve-yetki-sistemi-rbac.md
│       └── 05-saas-donusum-plani.md
├── git-hooks
│   └── post-receive
├── health-check.sh
├── keep-alive.sh
├── mobile
│   ├── .DS_Store
│   ├── .expo
│   │   ├── README.md
│   │   └── devices.json
│   ├── App.js
│   ├── README.md
│   ├── app.json
│   ├── assets
│   │   ├── adaptive-icon.png
│   │   ├── favicon.png
│   │   ├── icon.png
│   │   └── splash-icon.png
│   ├── babel.config.js
│   ├── package-lock.json
│   ├── package.json
│   └── src
│       ├── api
│       │   └── client.js
│       ├── components
│       │   ├── CustomerCard.js
│       │   ├── EmptyState.js
│       │   ├── LoadingState.js
│       │   ├── ProductCard.js
│       │   ├── SaleCard.js
│       │   ├── SearchBar.js
│       │   ├── SectionHeader.js
│       │   └── StatCard.js
│       ├── screens
│       │   ├── CustomerDetailScreen.js
│       │   ├── CustomersScreen.js
│       │   ├── DashboardScreen.js
│       │   ├── MoreScreen.js
│       │   ├── ProductDetailScreen.js
│       │   ├── ProductsScreen.js
│       │   ├── ReportsScreen.js
│       │   ├── SaleDetailScreen.js
│       │   ├── SalesScreen.js
│       │   ├── SettingsScreen.js
│       │   └── StockScreen.js
│       ├── theme
│       │   └── index.js
│       └── utils
│           └── formatters.js
├── nginx.conf
├── package-lock.json
├── package.json
├── php-fpm.conf
├── phpunit.xml
├── public
│   ├── .htaccess
│   ├── favicon.ico
│   ├── index.php
│   ├── js
│   │   └── hardware-drivers.js
│   └── robots.txt
├── requirements.txt
├── resources
│   ├── css
│   │   └── app.css
│   ├── js
│   │   ├── app.js
│   │   └── bootstrap.js
│   └── views
│       ├── admin
│       │   ├── integration-requests
│       │   ├── modules
│       │   ├── roles
│       │   └── users
│       ├── auth
│       │   ├── forgot-password.blade.php
│       │   ├── login.blade.php
│       │   ├── register.blade.php
│       │   └── reset-password.blade.php
│       ├── chat
│       │   └── index.blade.php
│       ├── customers
│       │   ├── create.blade.php
│       │   ├── edit.blade.php
│       │   ├── index.blade.php
│       │   └── show.blade.php
│       ├── dashboard.blade.php
│       ├── einvoices
│       │   ├── create.blade.php
│       │   ├── incoming.blade.php
│       │   ├── index.blade.php
│       │   ├── outgoing.blade.php
│       │   ├── settings.blade.php
│       │   └── show.blade.php
│       ├── errors
│       │   ├── 403.blade.php
│       │   ├── 404.blade.php
│       │   ├── 419.blade.php
│       │   ├── 429.blade.php
│       │   └── 500.blade.php
│       ├── faturalar
│       │   ├── create.blade.php
│       │   ├── earsiv.blade.php
│       │   ├── incoming.blade.php
│       │   ├── index.blade.php
│       │   ├── outgoing.blade.php
│       │   ├── show.blade.php
│       │   └── waybills.blade.php
│       ├── feedback
│       │   └── index.blade.php
│       ├── firms
│       │   ├── index.blade.php
│       │   └── show.blade.php
│       ├── hardware
│       │   ├── create.blade.php
│       │   ├── edit.blade.php
│       │   └── index.blade.php
│       ├── income-expense
│       │   ├── expenses.blade.php
│       │   ├── incomes.blade.php
│       │   └── types.blade.php
│       ├── integrations
│       │   ├── index.blade.php
│       │   └── my-requests.blade.php
│       ├── invoices
│       │   ├── index.blade.php
│       │   └── show.blade.php
│       ├── layouts
│       │   ├── app.blade.php
│       │   ├── guest.blade.php
│       │   └── public.blade.php
│       ├── marketing
│       │   ├── campaigns
│       │   ├── index.blade.php
│       │   ├── loyalty
│       │   ├── messages
│       │   ├── quotes
│       │   └── segments
│       ├── mobile
│       │   ├── barcode-scan.blade.php
│       │   ├── camera-add.blade.php
│       │   ├── index.blade.php
│       │   ├── product-detail.blade.php
│       │   └── quick-order.blade.php
│       ├── partials
│       │   ├── chat-widget.blade.php
│       │   └── feedback-widget.blade.php
│       ├── payment-types
│       │   └── index.blade.php
│       ├── products
│       │   ├── create-variant.blade.php
│       │   ├── create.blade.php
│       │   ├── edit.blade.php
│       │   ├── groups.blade.php
│       │   ├── index.blade.php
│       │   ├── label-designer.blade.php
│       │   ├── labels.blade.php
│       │   ├── refund-requests.blade.php
│       │   ├── refunds.blade.php
│       │   ├── scale-barcode.blade.php
│       │   ├── show.blade.php
│       │   ├── sub-products.blade.php
│       │   └── variants.blade.php
│       ├── recurring-invoices
│       │   ├── create.blade.php
│       │   ├── index.blade.php
│       │   └── show.blade.php
│       ├── reports
│       │   ├── correlation.blade.php
│       │   ├── daily.blade.php
│       │   ├── groups.blade.php
│       │   ├── historical.blade.php
│       │   ├── index.blade.php
│       │   ├── products.blade.php
│       │   ├── profit.blade.php
│       │   ├── sales.blade.php
│       │   ├── staff-movement.blade.php
│       │   └── stock-movement.blade.php
│       ├── sales
│       │   ├── index.blade.php
│       │   └── show.blade.php
│       ├── screens
│       │   ├── menu.blade.php
│       │   ├── order.blade.php
│       │   ├── pos.blade.php
│       │   └── terminal.blade.php
│       ├── service-categories
│       │   └── index.blade.php
│       ├── signage
│       │   ├── display.blade.php
│       │   └── index.blade.php
│       ├── sms
│       │   ├── automations
│       │   ├── blacklist
│       │   ├── compose.blade.php
│       │   ├── index.blade.php
│       │   ├── logs
│       │   ├── scenarios
│       │   ├── settings.blade.php
│       │   └── templates
│       ├── staff
│       │   ├── index.blade.php
│       │   ├── motions.blade.php
│       │   └── show.blade.php
│       ├── stock
│       │   ├── count-show.blade.php
│       │   ├── counts.blade.php
│       │   └── movements.blade.php
│       ├── super-admin
│       │   ├── dashboard.blade.php
│       │   ├── firms
│       │   └── layout.blade.php
│       ├── tasks
│       │   └── index.blade.php
│       ├── tax-rates
│       │   └── index.blade.php
│       └── welcome.blade.php
├── routes
│   ├── api.php
│   ├── console.php
│   └── web.php
├── server-setup.sh
├── start.sh
├── storage
│   ├── app
│   │   ├── .gitignore
│   │   ├── private
│   │   │   └── .gitignore
│   │   └── public
│   │       └── .gitignore
│   ├── framework
│   │   ├── .gitignore
│   │   ├── cache
│   │   │   ├── .gitignore
│   │   │   └── data
│   │   ├── sessions
│   │   │   └── .gitignore
│   │   ├── testing
│   │   │   └── .gitignore
│   │   └── views
│   │       ├── .gitignore
│   │       ├── 0ca89a4c91cf6712f9370bae25541e51.php
│   │       ├── 0e483d7d79ce9ff9fcfea1e6caed68fb.php
│   │       ├── 0ea9b9e53f6a6a846b4d93edcdb4057d.php
│   │       ├── 12b028a0bdad1c2d17327bed122cfedc.php
│   │       ├── 16b9506959524e202fdf6ac3f71d902a.php
│   │       ├── 1ded5907e68ac19dc8cada31d0d2f54b.php
│   │       ├── 236f49664fc0344d1190b5feff219d62.php
│   │       ├── 28774c6e1572abfd3e7b585dd36473ab.php
│   │       ├── 29109f66005cc7521cda76d44e8c5664.php
│   │       ├── 2e3150dc6a3f35213e4aa6e6fe26a9b1.php
│   │       ├── 3d166c2a4bd8acabacb62f18291c76f4.php
│   │       ├── 44fd2528bcb8d08e60a3d5d9efc490c1.php
│   │       ├── 49ffae6f88e24cac3d0dcd94590521be.php
│   │       ├── 4b154fc14fab2c65537beab1b2504347.php
│   │       ├── 524190934eba1638dc422db1d0c0ea8f.php
│   │       ├── 52484775274ee8349a0511c59c893aaf.php
│   │       ├── 53fc74a4cda054d61b7ad33bebeb5ea9.php
│   │       ├── 55935b385dc40e025f6b0e260794a6fa.php
│   │       ├── 5a267f632afab1813cf468dbf34fc721.php
│   │       ├── 5db40457f0edeaee0bc5e05f616f7b8d.php
│   │       ├── 5edcffde62cd4f1d49375219cdf7bc84.php
│   │       ├── 648eccaaf5e834b2a2880927f09a95a4.php
│   │       ├── 6760ff5b671b43a8b94ce4c5e744c296.php
│   │       ├── 68efd17c48f0a6d445e4e4bd5685d6f7.php
│   │       ├── 69787dbce60e014321000dc995496113.php
│   │       ├── 6cd419287e0dabf8851002bd1b2f3b81.php
│   │       ├── 789e6059145ae56d78dbbf82b3526c57.php
│   │       ├── 799b9f79647b79de5b7db921fa7ee7f0.php
│   │       ├── 7c15ac4e933790d66e9ab4baf856778f.php
│   │       ├── 7d05663e0a21ad1b132c8e515c549b9a.php
│   │       ├── 7e809ca7aa96edddbf3597fc2328bb49.php
│   │       ├── 7eb44fae49a59b3be4e1bc9be7e59982.php
│   │       ├── 816bd14fbeee58db4b4bcff05c92020d.php
│   │       ├── 82a44c8dfad4dbee5040d576a5bbe211.php
│   │       ├── 8524980a31332ee06a21912c004a290d.php
│   │       ├── 8729e472e8de6b94759d1dfe640d9b3c.php
│   │       ├── 87c0a69de9dda1170b12dc65eb08b92a.php
│   │       ├── 89634fc9c177d6d53f5a2887dddb9f3a.php
│   │       ├── 8fc3d5ee0cedba6af2017c6a542daa35.php
│   │       ├── 948ea2b748255f9e32096a18214a04b7.php
│   │       ├── 9bf65ae84cf5c70693bce6815c4482b7.php
│   │       ├── 9d43616eeb7ccd18cec428d952f5ae55.php
│   │       ├── 9ef9c303a6bb85edeb270d3aeabb8594.php
│   │       ├── a0c622fe043167e4c70e3aa61257a742.php
│   │       ├── a16a1a9f2fa0548a0f4c0b6d9e895e30.php
│   │       ├── a697fe72301d2c9d39bbbf547e1769d0.php
│   │       ├── b06ef5bd65a386c243871c4d073cf9c2.php
│   │       ├── b57a06c6f04cae4ffb30880ee3b79a7c.php
│   │       ├── b782f774d37bd0a08edb29a125a8e77a.php
│   │       ├── b7c4478075a27885a1152cdbad38218b.php
│   │       ├── bc7ade29288e628e39f2996af62ac765.php
│   │       ├── be13f7f2df33417bd3e154d84dbf071b.php
│   │       ├── be4ca5513bdd26eb30c0695622cfc81b.php
│   │       ├── c6a34e90fbc365e08d098bac3b66ec76.php
│   │       ├── c743b1278337654100be6edcf6d536ce.php
│   │       ├── c81f9dd0606bee18c771c0494c9a0d7d.php
│   │       ├── c99581d6d33ee12dfd91d0215467a528.php
│   │       ├── d15df84aeff08bb7dfa1de9a6678c203.php
│   │       ├── dce564450a57d72e4239f17dde0d74f0.php
│   │       ├── df03b5981358298f6dfe66a9e6c79159.php
│   │       ├── e241346c5b66568bc92ea9cf302a2b7c.php
│   │       ├── e26e0c1306c90daf02ae08ce40a67ffe.php
│   │       ├── e4bde63fcb45c35943be60c62c0696a0.php
│   │       ├── e5c38bfe5e4bec02796bff0acc9c97ab.php
│   │       ├── e8feef7ed251e14e8369da591d553c05.php
│   │       ├── ea70c5f632346c1fb69c41fc8e06ad97.php
│   │       ├── f2283de0b511c9df41916cf1fd96205e.php
│   │       ├── f38ed8bd2d1daeb888cbbc3b1ab86636.php
│   │       ├── f393b8805a13d32ec229dae1fbaaa110.php
│   │       ├── f60a5727e077d85bf01f3bfb2875f066.php
│   │       ├── f7942c34d442907171d6d9efa37e2a8b.php
│   │       ├── f98305909b8a9374ac3ddeef8fde2f54.php
│   │       └── fbb0f33cb2d8c46a68ca6e7e9f390b09.php
│   └── logs
│       ├── .gitignore
│       ├── keepalive.log
│       ├── laravel.log
│       └── server.log
├── supervisor.conf
├── tests
│   ├── Feature
│   │   └── ExampleTest.php
│   ├── TestCase.php
│   ├── Unit
│   │   └── ExampleTest.php
│   └── test_all_modules.php
└── vite.config.js

129 directories, 491 files

```

---

## 📌 Kullanım Talimatları (AI İçin)

Bu dosya, kod üretmeden önce projenin mevcut yapısını kontrol etmek içindir:

1. **Yeni dosya oluşturmadan önce:** Bu ağaçta benzer bir dosya var mı kontrol et
2. **Yeni klasör oluşturmadan önce:** Mevcut klasör yapısına uygun mu kontrol et
3. **Import/require yapmadan önce:** Dosya yolu doğru mu kontrol et
4. **Kod kopyalamadan önce:** Aynı fonksiyon başka dosyada var mı kontrol et

**Örnek:**
- ❌ "Yeni bir auth.py oluşturalım" → ✅ Kontrol et, zaten `app/auth.py` var mı?
- ❌ "config/ klasörü oluşturalım" → ✅ Kontrol et, zaten `config/` var mı?
- ❌ `from utils import helper` → ✅ Kontrol et, `utils/helper.py` gerçekten var mı?

---

**Not:** Bu dosya otomatik oluşturulmuştur. Proje yapısı değiştikçe güncellenmelidir.

```bash
# Güncelleme komutu
python3 /Users/emre/Desktop/Emare/create_dosya_yapisi.py
```
