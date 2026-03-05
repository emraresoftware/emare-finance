<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'external_id', 'barcode', 'name', 'description', 'category_id',
        'service_category_id', 'variant_type', 'parent_id',
        'unit', 'purchase_price', 'sale_price', 'vat_rate', 'additional_taxes',
        'stock_quantity', 'critical_stock', 'image_url', 'is_active', 'is_service',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'stock_quantity' => 'decimal:2',
        'critical_stock' => 'decimal:2',
        'is_active' => 'boolean',
        'is_service' => 'boolean',
        'additional_taxes' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function serviceCategory(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    /**
     * Ana ürün (varyant ise)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'parent_id');
    }

    /**
     * Varyantlar
     */
    public function variants(): HasMany
    {
        return $this->hasMany(Product::class, 'parent_id');
    }

    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class)
            ->withPivot('stock_quantity', 'sale_price')
            ->withTimestamps();
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function purchaseInvoiceItems(): HasMany
    {
        return $this->hasMany(PurchaseInvoiceItem::class);
    }

    public function stockCountItems(): HasMany
    {
        return $this->hasMany(StockCountItem::class);
    }

    public function getProfitMarginAttribute(): float
    {
        if ($this->purchase_price <= 0) return 0;
        return round((($this->sale_price - $this->purchase_price) / $this->purchase_price) * 100, 2);
    }

    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->critical_stock;
    }
}
