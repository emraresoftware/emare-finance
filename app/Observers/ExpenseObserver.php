<?php

namespace App\Observers;

use App\Models\Expense;
use App\Services\AccountingService;

class ExpenseObserver
{
    /**
     * Gider kaydedildiğinde otomatik yevmiye fişi oluştur.
     */
    public function created(Expense $expense): void
    {
        AccountingService::fromExpense($expense);
    }
}
