<?php

namespace App\Traits;

use App\Models\Tenant;
use App\Services\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Multi-tenant modellere tenant_id scope ve ilişki ekler.
 *
 * Kullanım: Model'e `use BelongsToTenant;` ekleyin.
 * Model'in tablosunda `tenant_id` sütunu olmalıdır.
 */
trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        // Global scope: her sorguya tenant_id filtresi ekle
        static::addGlobalScope('tenant', function (Builder $builder) {
            $tenantContext = app(TenantContext::class);

            if ($tenantContext->hasTenant()) {
                $builder->where(
                    $builder->getModel()->getTable() . '.tenant_id',
                    $tenantContext->getTenantId()
                );
            }
        });

        // Yeni kayıt oluşturulurken tenant_id otomatik ata
        static::creating(function (Model $model) {
            $tenantContext = app(TenantContext::class);

            if ($tenantContext->hasTenant() && empty($model->tenant_id)) {
                $model->tenant_id = $tenantContext->getTenantId();
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
