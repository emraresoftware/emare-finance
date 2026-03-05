<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use App\Services\TenantContext;
use App\Services\ModuleService;
use App\Models\Sale;
use App\Models\Expense;
use App\Observers\SaleObserver;
use App\Observers\ExpenseObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // TenantContext singleton kayıt
        $this->app->singleton(TenantContext::class, function () {
            return new TenantContext();
        });

        // ModuleService singleton kayıt
        $this->app->singleton(ModuleService::class, function ($app) {
            return new ModuleService($app->make(TenantContext::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Türkçe locale ayarı
        Carbon::setLocale('tr');
        setlocale(LC_TIME, 'tr_TR.UTF-8', 'tr_TR', 'turkish');

        // Tailwind pagination
        Paginator::useTailwind();

        // ── Muhasebe Observer'ları ──────────────────────────────
        // Satış/gider oluşturulunca otomatik yevmiye fişi yarat
        Sale::observe(SaleObserver::class);
        Expense::observe(ExpenseObserver::class);

        // Özel Blade directive'leri

        // Para formatı: @money(1234.56) → "1.234,56 ₺"
        Blade::directive('money', function ($expression) {
            return "<?php echo number_format((float)($expression), 2, ',', '.') . ' ₺'; ?>";
        });

        // Tarih formatı: @tarih($date) → "01 Mart 2026"
        Blade::directive('tarih', function ($expression) {
            return "<?php echo ($expression) ? \Carbon\Carbon::parse($expression)->locale('tr')->translatedFormat('d F Y') : '-'; ?>";
        });

        // Tarih-saat formatı: @tarihSaat($date) → "01 Mart 2026 14:30"
        Blade::directive('tarihSaat', function ($expression) {
            return "<?php echo ($expression) ? \Carbon\Carbon::parse($expression)->locale('tr')->translatedFormat('d F Y H:i') : '-'; ?>";
        });

        // Modül kontrolü: @module('hardware') ... @endmodule
        Blade::if('module', function (string $moduleCode) {
            $user = auth()->user();
            return $user && $user->hasModule($moduleCode);
        });

        // Yetki kontrolü: @permission('sales.create') ... @endpermission
        Blade::if('permission', function (string $permissionCode) {
            $user = auth()->user();
            return $user && $user->hasPermission($permissionCode);
        });

        // Rol kontrolü: @role('admin') ... @endrole
        Blade::if('role', function (string $roleCode) {
            $user = auth()->user();
            return $user && $user->hasRole($roleCode);
        });

        // Süper admin kontrolü: @superadmin ... @endsuperadmin
        Blade::if('superadmin', function () {
            $user = auth()->user();
            return $user && $user->isSuperAdmin();
        });
    }
}
