<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\ModuleController as AdminModuleController;
use App\Http\Controllers\Admin\RoleController as AdminRoleController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\IntegrationRequestController as AdminIntegrationRequestController;
use App\Http\Controllers\SuperAdmin\FirmController as SuperAdminFirmController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EInvoiceController;
use App\Http\Controllers\FirmController;
use App\Http\Controllers\HardwareController;
use App\Http\Controllers\IncomeExpenseController;
use App\Http\Controllers\PaymentTypeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseInvoiceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaxRateController;
use App\Http\Controllers\ServiceCategoryController;
use App\Http\Controllers\RecurringInvoiceController;
use App\Http\Controllers\IntegrationController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\ScreenController;
use App\Http\Controllers\SignageController;
use App\Http\Controllers\MobileController;
use App\Http\Controllers\FaturaController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\AccountingController;
use App\Http\Controllers\JournalEntryController;
use Illuminate\Support\Facades\Route;

// ══════════════════════════════════════════════════════════════
// GUEST (Misafir) Route'ları — Auth gerektirmez
// ══════════════════════════════════════════════════════════════
Route::middleware('guest')->group(function () {
    Route::get('/giris',  [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/giris', [LoginController::class, 'login']);

    Route::get('/kayit',  [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/kayit', [RegisterController::class, 'register']);

    Route::get('/sifremi-unuttum',  [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/sifremi-unuttum', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

    Route::get('/sifre-sifirla/{token}',  [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/sifre-sifirla',         [ForgotPasswordController::class, 'reset'])->name('password.update');
});

// ══════════════════════════════════════════════════════════════
// PUBLIC — Ana Sayfa (Landing Page)
// ══════════════════════════════════════════════════════════════
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
})->name('home');

// Çıkış — auth gerektirir
Route::post('/cikis', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// ══════════════════════════════════════════════════════════════
// AUTHENTICATED Route'ları — Auth + Tenant zorunlu
// ══════════════════════════════════════════════════════════════
Route::middleware(['auth'])->group(function () {

    // Dashboard (core — herkese açık)
    Route::get('/panel', [DashboardController::class, 'index'])->name('dashboard');

    // ─── Raporlar ─── (core — permission: reports.view)
    Route::prefix('raporlar')->name('reports.')->middleware('permission:reports.view')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/gunluk', [ReportController::class, 'daily'])->name('daily');
        Route::get('/tarihsel', [ReportController::class, 'historical'])->name('historical');
        Route::get('/urunsel', [ReportController::class, 'products'])->name('products');
        Route::get('/grupsal', [ReportController::class, 'groups'])->name('groups');
        Route::get('/korelasyon', [ReportController::class, 'correlation'])->name('correlation');
        Route::get('/stok-hareket', [ReportController::class, 'stockMovement'])->name('stock_movement');
        Route::get('/personel-hareket', [ReportController::class, 'staffMovement'])->name('staff_movement');
        Route::get('/satislar', [ReportController::class, 'sales'])->name('sales');
        Route::get('/kar', [ReportController::class, 'profit'])->name('profit');
    });

    // ─── Müşteriler ─── (core — permission: customers.*)
    Route::prefix('cariler')->name('customers.')->middleware('permission:customers.view')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/ekle', [CustomerController::class, 'create'])->middleware('permission:customers.create')->name('create');
        Route::post('/', [CustomerController::class, 'store'])->middleware('permission:customers.create')->name('store');
        Route::get('/export', [CustomerController::class, 'export'])->name('export');
        Route::get('/{customer}/duzenle', [CustomerController::class, 'edit'])->name('edit');
        Route::put('/{customer}', [CustomerController::class, 'update'])->middleware('permission:customers.update')->name('update');
        Route::delete('/{customer}', [CustomerController::class, 'destroy'])->middleware('permission:customers.delete')->name('destroy');
        Route::post('/{customer}/odeme', [CustomerController::class, 'addPayment'])->middleware('permission:customers.update')->name('add_payment');
        Route::get('/{customer}/export-sales', [CustomerController::class, 'exportSales'])->name('export_sales');
        Route::get('/{customer}', [CustomerController::class, 'show'])->name('show');
    });

    // ─── Ürünler ─── (core — permission: products.*)
    Route::prefix('urunler')->name('products.')->middleware('permission:products.view')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/ekle', [ProductController::class, 'create'])->middleware('permission:products.create')->name('create');
        Route::post('/ekle', [ProductController::class, 'store'])->middleware('permission:products.create')->name('store');
        Route::get('/gruplar', [ProductController::class, 'groups'])->name('groups');
        Route::post('/gruplar', [ProductController::class, 'storeCategory'])->middleware('permission:products.create')->name('store_category');
        Route::put('/gruplar/{category}', [ProductController::class, 'updateCategory'])->middleware('permission:products.create')->name('update_category');
        Route::delete('/gruplar/{category}', [ProductController::class, 'destroyCategory'])->middleware('permission:products.create')->name('destroy_category');
        Route::get('/alt-urunler', [ProductController::class, 'subProducts'])->name('sub_products');
        Route::get('/varyantlar', [ProductController::class, 'variants'])->name('variants');
        Route::get('/varyant-ekle', [ProductController::class, 'createVariant'])->middleware('permission:products.create')->name('create_variant');
        Route::post('/varyant-ekle', [ProductController::class, 'storeVariant'])->middleware('permission:products.create')->name('store_variant');
        Route::get('/iadeler', [ProductController::class, 'refunds'])->name('refunds');
        Route::get('/iade-talepleri', [ProductController::class, 'refundRequests'])->name('refund_requests');
        Route::get('/etiket', [ProductController::class, 'labels'])->name('labels');
        Route::get('/etiket-tasarla', [ProductController::class, 'labelDesigner'])->name('label_designer');
        Route::get('/terazi-barkod', [ProductController::class, 'scaleBarcode'])->name('scale_barcode');
        Route::get('/export', [ProductController::class, 'export'])->middleware('permission:products.export')->name('export');
        Route::get('/import', [ProductController::class, 'importForm'])->middleware('permission:products.create')->name('import');
        Route::post('/import', [ProductController::class, 'import'])->middleware('permission:products.create')->name('import.store');
        Route::get('/import/sablon', [ProductController::class, 'importTemplate'])->middleware('permission:products.create')->name('import.template');
        Route::get('/{product}/duzenle', [ProductController::class, 'edit'])->middleware('permission:products.update')->name('edit');
        Route::put('/{product}', [ProductController::class, 'update'])->middleware('permission:products.update')->name('update');
        Route::get('/{product}', [ProductController::class, 'show'])->name('show');
    });

    // ─── Satışlar ─── (core — permission: sales.view)
    Route::prefix('satislar')->name('sales.')->middleware('permission:sales.view')->group(function () {
        Route::get('/', [SaleController::class, 'index'])->name('index');
        Route::get('/export', [SaleController::class, 'export'])->middleware('permission:sales.export')->name('export');
        Route::get('/{sale}', [SaleController::class, 'show'])->name('show');
    });

    // ─── Faturalar (Birleşik) ───
    Route::prefix('faturalar')->name('faturalar.')->group(function () {
        Route::get('/', [FaturaController::class, 'index'])->name('index');
        Route::get('/yeni', [FaturaController::class, 'create'])->name('create');
        Route::post('/yeni', [FaturaController::class, 'store'])->name('store');
        Route::get('/giden', [FaturaController::class, 'outgoing'])->name('outgoing');
        Route::get('/gelen', [FaturaController::class, 'incoming'])->name('incoming');
        Route::get('/irsaliyeler', [FaturaController::class, 'waybills'])->name('waybills');
        Route::get('/e-arsiv', [FaturaController::class, 'earsiv'])->name('earsiv');
        Route::get('/alis/{invoice}', [FaturaController::class, 'purchaseShow'])->name('purchase.show');
        Route::patch('/{id}/durum', [FaturaController::class, 'updateStatus'])->name('status');
        Route::get('/{id}', [FaturaController::class, 'show'])->name('show');
    });

    // ─── Alış Faturaları ─── (core)
    Route::prefix('alis-faturalari')->name('invoices.')->group(function () {
        Route::get('/', [PurchaseInvoiceController::class, 'index'])->name('index');
        Route::get('/{invoice}', [PurchaseInvoiceController::class, 'show'])->name('show');
    });

    // ─── Firmalar ─── (core)
    Route::prefix('firmalar')->name('firms.')->group(function () {
        Route::get('/', [FirmController::class, 'index'])->name('index');
        Route::get('/{firm}', [FirmController::class, 'show'])->name('show');
    });

    // ─── E-Faturalar ─── (modül: einvoice)
    Route::prefix('e-faturalar')->name('einvoices.')->middleware('module:einvoice')->group(function () {
        Route::get('/', [EInvoiceController::class, 'index'])->middleware('permission:einvoice.view')->name('index');
        Route::get('/olustur', [EInvoiceController::class, 'create'])->middleware('permission:einvoice.create')->name('create');
        Route::post('/olustur', [EInvoiceController::class, 'store'])->middleware('permission:einvoice.create')->name('store');
        Route::get('/giden', [EInvoiceController::class, 'outgoing'])->middleware('permission:einvoice.view')->name('outgoing');
        Route::get('/gelen', [EInvoiceController::class, 'incoming'])->middleware('permission:einvoice.view')->name('incoming');
        Route::get('/ayarlar', [EInvoiceController::class, 'settings'])->middleware('permission:settings.manage')->name('settings');
        Route::post('/ayarlar', [EInvoiceController::class, 'updateSettings'])->middleware('permission:settings.manage')->name('settings.update');
        Route::get('/{einvoice}', [EInvoiceController::class, 'show'])->middleware('permission:einvoice.view')->name('show');
    });

    // ─── Stok ─── (core — permission: stock.view)
    Route::prefix('stok')->name('stock.')->middleware('permission:stock.view')->group(function () {
        Route::get('/hareketler', [StockController::class, 'movements'])->name('movements');
        Route::get('/sayim', [StockController::class, 'counts'])->name('counts');
        Route::get('/sayim/{stockCount}', [StockController::class, 'countShow'])->name('count_show');
    });

    // ─── Gelir / Giderler ─── (modül: income_expense)
    Route::prefix('gelir-gider')->name('income_expense.')->middleware('module:income_expense')->group(function () {
        Route::get('/gelirler', [IncomeExpenseController::class, 'incomes'])->name('incomes');
        Route::get('/giderler', [IncomeExpenseController::class, 'expenses'])->name('expenses');
        Route::get('/turler', [IncomeExpenseController::class, 'types'])->name('types');
    });

    // ─── Personeller ─── (modül: staff)
    Route::prefix('personeller')->name('staff.')->middleware('module:staff')->group(function () {
        Route::get('/', [StaffController::class, 'index'])->middleware('permission:staff.view')->name('index');
        Route::get('/hareketler', [StaffController::class, 'motions'])->middleware('permission:staff.view')->name('motions');
        Route::get('/{staff}', [StaffController::class, 'show'])->middleware('permission:staff.view')->name('show');
    });

    // ─── Görevler ───
    Route::get('/gorevler', [TaskController::class, 'index'])->name('tasks.index');

    // ─── Ödeme Tipleri ───
    Route::get('/odeme-tipleri', [PaymentTypeController::class, 'index'])->name('payment_types.index');

    // ─── Vergi Oranları ─── (permission: settings.manage)
    Route::prefix('vergi-oranlari')->name('tax_rates.')->middleware('permission:settings.manage')->group(function () {
        Route::get('/', [TaxRateController::class, 'index'])->name('index');
        Route::post('/', [TaxRateController::class, 'store'])->name('store');
        Route::put('/{taxRate}', [TaxRateController::class, 'update'])->name('update');
        Route::delete('/{taxRate}', [TaxRateController::class, 'destroy'])->name('destroy');
    });

    // ─── Hizmet Kategorileri ─── (permission: settings.manage)
    Route::prefix('hizmet-kategorileri')->name('service_categories.')->middleware('permission:settings.manage')->group(function () {
        Route::get('/', [ServiceCategoryController::class, 'index'])->name('index');
        Route::post('/', [ServiceCategoryController::class, 'store'])->name('store');
        Route::put('/{serviceCategory}', [ServiceCategoryController::class, 'update'])->name('update');
        Route::delete('/{serviceCategory}', [ServiceCategoryController::class, 'destroy'])->name('destroy');
    });

// ─── Tekrarlayan Faturalar ─── (modül: einvoice)
    Route::prefix('tekrarlayan-faturalar')->name('recurring_invoices.')->middleware('module:einvoice')->group(function () {
        Route::get('/', [RecurringInvoiceController::class, 'index'])->name('index');
        Route::get('/olustur', [RecurringInvoiceController::class, 'create'])->name('create');
        Route::post('/olustur', [RecurringInvoiceController::class, 'store'])->name('store');
        Route::post('/vadesi-gelenler', [RecurringInvoiceController::class, 'generateDueInvoices'])->name('generate_due');
        Route::get('/{recurringInvoice}', [RecurringInvoiceController::class, 'show'])->name('show');
        Route::post('/{recurringInvoice}/durum', [RecurringInvoiceController::class, 'updateStatus'])->name('update_status');
        Route::post('/{recurringInvoice}/fatura-olustur', [RecurringInvoiceController::class, 'generateSingle'])->name('generate_single');
        Route::delete('/{recurringInvoice}', [RecurringInvoiceController::class, 'destroy'])->name('destroy');
    });

    // ─── Vergi API ───
    Route::prefix('api/tax-rates')->group(function () {
        Route::get('/', [TaxRateController::class, 'apiList']);
        Route::get('/grouped', [TaxRateController::class, 'apiGrouped']);
    });

    // ─── Donanım Yönetimi ─── (modül: hardware)
    Route::prefix('donanim')->name('hardware.')->middleware('module:hardware')->group(function () {
        Route::get('/', [HardwareController::class, 'index'])->middleware('permission:hardware.manage')->name('index');
        Route::get('/ekle', [HardwareController::class, 'create'])->middleware('permission:hardware.manage')->name('create');
        Route::post('/ekle', [HardwareController::class, 'store'])->middleware('permission:hardware.manage')->name('store');
        Route::get('/{device}/duzenle', [HardwareController::class, 'edit'])->middleware('permission:hardware.manage')->name('edit');
        Route::put('/{device}', [HardwareController::class, 'update'])->middleware('permission:hardware.manage')->name('update');
        Route::delete('/{device}', [HardwareController::class, 'destroy'])->middleware('permission:hardware.manage')->name('destroy');
        Route::post('/{device}/status', [HardwareController::class, 'updateStatus'])->middleware('permission:hardware.manage')->name('status');
        Route::post('/{device}/default', [HardwareController::class, 'setDefault'])->middleware('permission:hardware.manage')->name('set_default');
    });

    // ─── Donanım API (JS Driver) ─── (modül: hardware)
    Route::prefix('api/hardware')->middleware('module:hardware')->group(function () {
        Route::get('/devices', [HardwareController::class, 'apiDevices']);
        Route::post('/print-network', [HardwareController::class, 'apiPrintNetwork'])->middleware('permission:hardware.print');

        // Sürücü Kataloğu API
        Route::get('/drivers', [HardwareController::class, 'apiDrivers']);
        Route::get('/drivers/stats', [HardwareController::class, 'apiDriverStats']);
        Route::get('/drivers/manufacturers', [HardwareController::class, 'apiDriverManufacturers']);
        Route::get('/drivers/models', [HardwareController::class, 'apiDriverModels']);
        Route::get('/drivers/{driver}', [HardwareController::class, 'apiDriverShow']);
    });

    // ─── Entegrasyonlar ───
    Route::get('/entegrasyonlar', [IntegrationController::class, 'index'])->name('integrations.index');
    Route::post('/entegrasyonlar/basvuru', [IntegrationController::class, 'requestIntegration'])->name('integrations.request');
    Route::get('/entegrasyonlar/basvurularim', [IntegrationController::class, 'myRequests'])->name('integrations.my_requests');

    // ─── Ekranlar ─── (core — POS ekranları)
    Route::prefix('ekranlar')->name('screens.')->group(function () {
        Route::get('/', [ScreenController::class, 'menu'])->name('menu');
        Route::get('/pos', [ScreenController::class, 'pos'])->middleware('permission:sales.create')->name('pos');
        Route::get('/siparis', [ScreenController::class, 'order'])->middleware('permission:sales.create')->name('order');
        Route::get('/terminal', [ScreenController::class, 'terminal'])->middleware('permission:sales.create')->name('terminal');
    });

    // ─── Dijital Ekran (Digital Signage) ───
    Route::prefix('dijital-ekran')->name('signage.')->group(function () {
        Route::get('/', [SignageController::class, 'index'])->name('index');
        Route::get('/goruntule/{template?}', [SignageController::class, 'display'])->name('display');
        Route::get('/onizleme/{template}', [SignageController::class, 'preview'])->name('preview');

        // İçerik CRUD
        Route::post('/icerik', [SignageController::class, 'contentStore'])->name('content.store');
        Route::put('/icerik/{content}', [SignageController::class, 'contentUpdate'])->name('content.update');
        Route::delete('/icerik/{content}', [SignageController::class, 'contentDestroy'])->name('content.destroy');

        // Cihaz CRUD
        Route::post('/cihaz', [SignageController::class, 'deviceStore'])->name('device.store');
        Route::put('/cihaz/{device}', [SignageController::class, 'deviceUpdate'])->name('device.update');
        Route::delete('/cihaz/{device}', [SignageController::class, 'deviceDestroy'])->name('device.destroy');
        Route::post('/cihaz/{device}/playlist', [SignageController::class, 'deviceAssignPlaylist'])->name('device.assign-playlist');

        // Playlist CRUD
        Route::post('/playlist', [SignageController::class, 'playlistStore'])->name('playlist.store');
        Route::put('/playlist/{playlist}', [SignageController::class, 'playlistUpdate'])->name('playlist.update');
        Route::delete('/playlist/{playlist}', [SignageController::class, 'playlistDestroy'])->name('playlist.destroy');

        // Zamanlama CRUD
        Route::post('/zamanlama', [SignageController::class, 'scheduleStore'])->name('schedule.store');
        Route::delete('/zamanlama/{schedule}', [SignageController::class, 'scheduleDestroy'])->name('schedule.destroy');

        // API — Cihaz Ping
        Route::post('/api/ping', [SignageController::class, 'devicePing'])->name('api.ping');
    });

    // ══════════════════════════════════════════════════════════════
    // ADMIN Paneli — sadece admin rolü
    // ══════════════════════════════════════════════════════════════
    Route::prefix('admin')->name('admin.')->middleware('permission:settings.manage')->group(function () {

        // ─── Modül Yönetimi ───
        Route::prefix('moduller')->name('modules.')->group(function () {
            Route::get('/', [AdminModuleController::class, 'index'])->name('index');
            Route::post('/{module}/toggle', [AdminModuleController::class, 'toggle'])->name('toggle');
            Route::get('/branch/{branch}', [AdminModuleController::class, 'branchModules'])->name('branch');
            Route::post('/branch/{branch}/{module}/toggle', [AdminModuleController::class, 'branchToggle'])->name('branch.toggle');
        });

        // ─── Rol Yönetimi ───
        Route::prefix('roller')->name('roles.')->group(function () {
            Route::get('/', [AdminRoleController::class, 'index'])->name('index');
            Route::get('/{role}', [AdminRoleController::class, 'show'])->name('show');
            Route::post('/{role}/permissions', [AdminRoleController::class, 'updatePermissions'])->name('permissions.update');
        });

        // ─── Entegrasyon Başvuruları ───
        Route::prefix('entegrasyon-basvurulari')->name('integration-requests.')->group(function () {
            Route::get('/', [AdminIntegrationRequestController::class, 'index'])->name('index');
            Route::post('/{integrationRequest}/onayla', [AdminIntegrationRequestController::class, 'approve'])->name('approve');
            Route::post('/{integrationRequest}/reddet', [AdminIntegrationRequestController::class, 'reject'])->name('reject');
            Route::get('/{integrationRequest}', [AdminIntegrationRequestController::class, 'show'])->name('show');
        });

        // ─── Kullanıcı Yönetimi ───
        Route::prefix('kullanicilar')->name('users.')->group(function () {
            Route::get('/', [AdminUserController::class, 'index'])->name('index');
            Route::get('/ekle', [AdminUserController::class, 'create'])->name('create');
            Route::post('/ekle', [AdminUserController::class, 'store'])->name('store');
            Route::get('/{user}/duzenle', [AdminUserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [AdminUserController::class, 'update'])->name('update');
            Route::delete('/{user}', [AdminUserController::class, 'destroy'])->name('destroy');
            Route::post('/{user}/role', [AdminUserController::class, 'updateRole'])->name('role.update');
        });
    });

    // ─── Pazarlama ─── (modül: marketing)
    Route::prefix('pazarlama')->name('marketing.')->middleware('module:marketing')->group(function () {
        Route::get('/', [MarketingController::class, 'index'])->name('index');

        // Teklifler
        Route::get('/teklifler', [MarketingController::class, 'quotes'])->name('quotes.index');
        Route::get('/teklifler/olustur', [MarketingController::class, 'quoteCreate'])->name('quotes.create');
        Route::post('/teklifler', [MarketingController::class, 'quoteStore'])->name('quotes.store');
        Route::get('/teklifler/{quote}', [MarketingController::class, 'quoteShow'])->name('quotes.show');
        Route::post('/teklifler/{quote}/gonder', [MarketingController::class, 'quoteSend'])->name('quotes.send');
        Route::patch('/teklifler/{quote}/durum', [MarketingController::class, 'quoteUpdateStatus'])->name('quotes.status');
        Route::post('/teklifler/{quote}/kopyala', [MarketingController::class, 'quoteDuplicate'])->name('quotes.duplicate');

        // Kampanyalar
        Route::get('/kampanyalar', [MarketingController::class, 'campaigns'])->name('campaigns.index');
        Route::get('/kampanyalar/olustur', [MarketingController::class, 'campaignCreate'])->name('campaigns.create');
        Route::post('/kampanyalar', [MarketingController::class, 'campaignStore'])->name('campaigns.store');
        Route::get('/kampanyalar/{campaign}', [MarketingController::class, 'campaignShow'])->name('campaigns.show');
        Route::post('/kampanyalar/{campaign}/toggle', [MarketingController::class, 'campaignToggle'])->name('campaigns.toggle');

        // Müşteri Segmentleri
        Route::get('/segmentler', [MarketingController::class, 'segments'])->name('segments.index');
        Route::post('/segmentler', [MarketingController::class, 'segmentStore'])->name('segments.store');
        Route::get('/segmentler/{segment}', [MarketingController::class, 'segmentShow'])->name('segments.show');
        Route::post('/segmentler/{segment}/uye-ekle', [MarketingController::class, 'segmentAddMembers'])->name('segments.add_members');
        Route::delete('/segmentler/{segment}/uye/{customer}', [MarketingController::class, 'segmentRemoveMember'])->name('segments.remove_member');

        // Mesajlar
        Route::get('/mesajlar', [MarketingController::class, 'messages'])->name('messages.index');
        Route::get('/mesajlar/olustur', [MarketingController::class, 'messageCreate'])->name('messages.create');
        Route::post('/mesajlar', [MarketingController::class, 'messageStore'])->name('messages.store');
        Route::get('/mesajlar/{message}', [MarketingController::class, 'messageShow'])->name('messages.show');
        Route::post('/mesajlar/{message}/gonder', [MarketingController::class, 'messageSend'])->name('messages.send');

        // Sadakat Programı
        Route::get('/sadakat', [MarketingController::class, 'loyalty'])->name('loyalty.index');
        Route::post('/sadakat', [MarketingController::class, 'loyaltyStore'])->name('loyalty.store');
    });

    // ─── SMS Yönetimi ─── (modül: sms)
    Route::prefix('sms')->name('sms.')->middleware('module:sms')->group(function () {
        Route::get('/', [SmsController::class, 'index'])->name('index');

        // Ayarlar
        Route::get('/ayarlar', [SmsController::class, 'settings'])->name('settings');
        Route::post('/ayarlar', [SmsController::class, 'settingsUpdate'])->name('settings.update');
        Route::post('/test', [SmsController::class, 'testSms'])->name('test');
        Route::post('/bakiye', [SmsController::class, 'checkBalance'])->name('balance');

        // Şablonlar
        Route::get('/sablonlar', [SmsController::class, 'templates'])->name('templates.index');
        Route::get('/sablonlar/olustur', [SmsController::class, 'templateCreate'])->name('templates.create');
        Route::post('/sablonlar', [SmsController::class, 'templateStore'])->name('templates.store');
        Route::get('/sablonlar/{template}/duzenle', [SmsController::class, 'templateEdit'])->name('templates.edit');
        Route::put('/sablonlar/{template}', [SmsController::class, 'templateUpdate'])->name('templates.update');
        Route::delete('/sablonlar/{template}', [SmsController::class, 'templateDestroy'])->name('templates.destroy');

        // Senaryolar
        Route::get('/senaryolar', [SmsController::class, 'scenarios'])->name('scenarios.index');
        Route::get('/senaryolar/olustur', [SmsController::class, 'scenarioCreate'])->name('scenarios.create');
        Route::post('/senaryolar', [SmsController::class, 'scenarioStore'])->name('scenarios.store');
        Route::get('/senaryolar/{scenario}/duzenle', [SmsController::class, 'scenarioEdit'])->name('scenarios.edit');
        Route::put('/senaryolar/{scenario}', [SmsController::class, 'scenarioUpdate'])->name('scenarios.update');
        Route::post('/senaryolar/{scenario}/toggle', [SmsController::class, 'scenarioToggle'])->name('scenarios.toggle');
        Route::delete('/senaryolar/{scenario}', [SmsController::class, 'scenarioDestroy'])->name('scenarios.destroy');

        // Otomasyonlar
        Route::get('/otomasyonlar', [SmsController::class, 'automations'])->name('automations.index');
        Route::post('/otomasyonlar/{type}/toggle', [SmsController::class, 'automationToggle'])->name('automations.toggle');
        Route::put('/otomasyonlar/{type}', [SmsController::class, 'automationUpdate'])->name('automations.update');
        Route::post('/otomasyonlar/{type}/calistir', [SmsController::class, 'automationRunNow'])->name('automations.run');
        Route::get('/otomasyonlar/kuyruk', [SmsController::class, 'automationQueue'])->name('automations.queue');

        // Loglar
        Route::get('/loglar', [SmsController::class, 'logs'])->name('logs.index');

        // Kara Liste
        Route::get('/kara-liste', [SmsController::class, 'blacklist'])->name('blacklist.index');
        Route::post('/kara-liste', [SmsController::class, 'blacklistStore'])->name('blacklist.store');
        Route::delete('/kara-liste/{blacklist}', [SmsController::class, 'blacklistDestroy'])->name('blacklist.destroy');

        // Hızlı Gönderim
        Route::get('/gonder', [SmsController::class, 'compose'])->name('compose');
        Route::post('/gonder', [SmsController::class, 'send'])->name('send');
    });

    // ─── AI Sohbet (Gemini) ───
    Route::prefix('sohbet')->name('chat.')->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('index');
        Route::post('/gonder', [ChatController::class, 'send'])->name('send');
        Route::post('/gonder-sync', [ChatController::class, 'sendSync'])->name('send-sync');
    });

    // ─── Mobil İşlemler ─── (kamera ile ürün ekleme, barkod tarama, hızlı sipariş)
    Route::prefix('mobil')->name('mobile.')->group(function () {
        Route::get('/', [MobileController::class, 'index'])->name('index');
        Route::get('/kamera-ekle', [MobileController::class, 'cameraAdd'])->name('camera-add');
        Route::post('/fotograf-yukle', [MobileController::class, 'uploadPhoto'])->name('upload-photo');
        Route::post('/urun-kaydet', [MobileController::class, 'storeProduct'])->name('store-product');
        Route::get('/barkod-tara', [MobileController::class, 'barcodeScan'])->name('barcode-scan');
        Route::post('/barkod-ara', [MobileController::class, 'barcodeSearch'])->name('barcode-search');
        Route::get('/hizli-siparis', [MobileController::class, 'quickOrder'])->name('quick-order');
        Route::get('/urun-ara', [MobileController::class, 'searchProducts'])->name('search-products');
        Route::post('/siparis-kaydet', [MobileController::class, 'storeOrder'])->name('store-order');
        Route::get('/urun/{product}', [MobileController::class, 'productDetail'])->name('product-detail');
    });

    // ─── Geri Bildirimler ───
    Route::prefix('geribildirim')->name('feedback.')->group(function () {
        Route::post('/gonder', [FeedbackController::class, 'store'])->name('store');
        Route::get('/benim', [FeedbackController::class, 'myFeedback'])->name('my');
        Route::get('/yonetim', [FeedbackController::class, 'index'])->name('index');
        Route::patch('/{feedback}/durum', [FeedbackController::class, 'updateStatus'])->name('status');
        Route::post('/{feedback}/yanit', [FeedbackController::class, 'reply'])->name('reply');
    });

    // ─── Muhasebe ───
    Route::prefix('muhasebe')->name('accounting.')->group(function () {
        Route::get('/', [AccountingController::class, 'dashboard'])->name('dashboard');
        Route::post('/setup', [AccountingController::class, 'setup'])->name('setup');
        Route::get('/mizan', [AccountingController::class, 'trialBalance'])->name('trial-balance');
        Route::get('/bilanco', [AccountingController::class, 'balanceSheet'])->name('balance-sheet');
        Route::get('/gelir-tablosu', [AccountingController::class, 'incomeStatement'])->name('income-statement');
        Route::get('/hesap-plani', [AccountingController::class, 'accountPlan'])->name('account-plan');
        Route::post('/hesap-plani', [AccountingController::class, 'accountPlanStore'])->name('account-plan.store');
        Route::put('/hesap-plani/{account}', [AccountingController::class, 'accountPlanUpdate'])->name('account-plan.update');
        Route::post('/ornek-fisler', [AccountingController::class, 'sampleEntries'])->name('sample-entries');
        Route::get('/tara', [AccountingController::class, 'scan'])->name('scan');
        Route::get('/tara/sonu', [AccountingController::class, 'scanResult'])->name('scan.result');
        Route::prefix('yevmiye')->name('journal.')->group(function () {
            Route::get('/', [JournalEntryController::class, 'index'])->name('index');
            Route::get('/olustur', [JournalEntryController::class, 'create'])->name('create');
            Route::post('/', [JournalEntryController::class, 'store'])->name('store');
            Route::get('/kamera', [JournalEntryController::class, 'camera'])->name('camera');
            Route::post('/kamera', [JournalEntryController::class, 'cameraStore'])->name('camera.store');
            Route::get('/{entry}', [JournalEntryController::class, 'show'])->name('show');
            Route::post('/{entry}/kesinlestir', [JournalEntryController::class, 'post'])->name('post');
            Route::delete('/{entry}', [JournalEntryController::class, 'destroy'])->name('destroy');
        });
    });

}); // end auth middleware

// ══════════════════════════════════════════════════════════════
// SÜPER ADMİN Route'ları — sadece is_super_admin kullanıcılar
// ══════════════════════════════════════════════════════════════
Route::middleware(['auth', 'super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {

    // Dashboard
    Route::get('/', [SuperAdminFirmController::class, 'dashboard'])->name('dashboard');

    // Firma CRUD
    Route::prefix('firmalar')->name('firms.')->group(function () {
        Route::get('/', [SuperAdminFirmController::class, 'index'])->name('index');
        Route::get('/olustur', [SuperAdminFirmController::class, 'create'])->name('create');
        Route::post('/olustur', [SuperAdminFirmController::class, 'store'])->name('store');
        Route::get('/{tenant}', [SuperAdminFirmController::class, 'show'])->name('show');
        Route::get('/{tenant}/duzenle', [SuperAdminFirmController::class, 'edit'])->name('edit');
        Route::put('/{tenant}', [SuperAdminFirmController::class, 'update'])->name('update');
        Route::patch('/{tenant}/durum', [SuperAdminFirmController::class, 'toggleStatus'])->name('toggle-status');
        Route::delete('/{tenant}', [SuperAdminFirmController::class, 'destroy'])->name('destroy');

        // Firma içi eklemeler
        Route::post('/{tenant}/kullanici-ekle', [SuperAdminFirmController::class, 'addUser'])->name('add-user');
        Route::post('/{tenant}/sube-ekle', [SuperAdminFirmController::class, 'addBranch'])->name('add-branch');
    });

    // Geri Bildirimler
    Route::prefix('geribildirim')->name('feedback.')->group(function () {
        Route::get('/', [FeedbackController::class, 'index'])->name('index');
        Route::patch('/{feedback}/durum', [FeedbackController::class, 'updateStatus'])->name('status');
        Route::post('/{feedback}/yanit', [FeedbackController::class, 'reply'])->name('reply');
    });
});

// ══════════════════════════════════════════════════════════════
// DEPLOY WEBHOOK — GitHub/GitLab push otomatik deploy
// ══════════════════════════════════════════════════════════════
Route::post('/deploy/webhook', [\App\Http\Controllers\DeployWebhookController::class, 'handle'])
    ->name('deploy.webhook')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

Route::get('/deploy/status', [\App\Http\Controllers\DeployWebhookController::class, 'status'])
    ->name('deploy.status')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);


