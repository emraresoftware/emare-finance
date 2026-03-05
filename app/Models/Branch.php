<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Branch extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'external_id', 'name', 'code', 'address',
        'phone', 'city', 'district', 'is_active',
        'tenant_id', 'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings'  => 'array',
    ];

    /* ─── Tenant İlişkisi ─── */

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /* ─── Modül İlişkisi ─── */

    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(Module::class, 'branch_modules')
            ->withPivot('is_active', 'activated_at', 'config')
            ->withTimestamps();
    }

    public function branchModules(): HasMany
    {
        return $this->hasMany(BranchModule::class);
    }

    /**
     * Bu şube için belirtilen modülün aktif olup olmadığını kontrol eder.
     */
    public function hasModule(string $moduleCode): bool
    {
        $module = Module::findByCode($moduleCode);
        if (!$module) {
            return false;
        }

        if ($module->is_core) {
            return true;
        }

        // Branch-level kontrol
        $bm = $this->branchModules()->where('module_id', $module->id)->first();
        if ($bm) {
            return $bm->is_active;
        }

        // Tenant-level fallback
        if ($this->tenant) {
            return $this->tenant->hasModule($moduleCode);
        }

        return false;
    }

    /* ─── Mevcut İlişkiler ─── */

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('stock_quantity', 'sale_price')
            ->withTimestamps();
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function staff(): HasMany
    {
        return $this->hasMany(Staff::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function purchaseInvoices(): HasMany
    {
        return $this->hasMany(PurchaseInvoice::class);
    }

    public function stockCounts(): HasMany
    {
        return $this->hasMany(StockCount::class);
    }

    public function hardwareDevices(): HasMany
    {
        return $this->hasMany(HardwareDevice::class);
    }
}
