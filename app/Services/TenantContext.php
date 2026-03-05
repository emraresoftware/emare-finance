<?php

namespace App\Services;

use App\Models\Tenant;

/**
 * Mevcut isteğin tenant bağlamını tutar.
 * Singleton olarak IoC container'a kayıtlıdır.
 */
class TenantContext
{
    private ?Tenant $tenant = null;

    /**
     * Mevcut tenant'ı ayarlar.
     */
    public function setTenant(?Tenant $tenant): void
    {
        $this->tenant = $tenant;
    }

    /**
     * Mevcut tenant'ı döner.
     */
    public function getTenant(): ?Tenant
    {
        return $this->tenant;
    }

    /**
     * Mevcut tenant ID'sini döner.
     */
    public function getTenantId(): ?int
    {
        return $this->tenant?->id;
    }

    /**
     * Tenant bağlamının ayarlanmış olup olmadığını kontrol eder.
     */
    public function hasTenant(): bool
    {
        return $this->tenant !== null;
    }

    /**
     * Tenant bağlamını temizler.
     */
    public function clear(): void
    {
        $this->tenant = null;
    }

    /**
     * Tenant'ın belirtilen modüle sahip olup olmadığını kontrol eder.
     */
    public function hasModule(string $moduleCode): bool
    {
        if (!$this->hasTenant()) {
            return false;
        }

        return $this->tenant->hasModule($moduleCode);
    }
}
