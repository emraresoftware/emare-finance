<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'external_id', 'income_expense_type_id', 'type_name',
        'note', 'amount', 'payment_type', 'date', 'time',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    public function type()
    {
        return $this->belongsTo(IncomeExpenseType::class, 'income_expense_type_id');
    }
}
