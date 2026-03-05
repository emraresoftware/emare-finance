<?php

namespace App\Observers;

use App\Models\Sale;
use App\Services\AccountingService;

class SaleObserver
{
    /**
     * Satış tamamlandığında otomatik yevmiye fişi oluştur.
     */
    public function created(Sale $sale): void
    {
        if ($sale->status === 'completed' || $sale->status === null) {
            AccountingService::fromSale($sale);
        }
    }

    public function updated(Sale $sale): void
    {
        // Durum "completed"a geçince fiş yok ise oluştur
        if ($sale->wasChanged('status') && $sale->status === 'completed') {
            AccountingService::fromSale($sale);
        }
    }
}
