<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomeExpenseType extends Model
{
    protected $fillable = ['name', 'direction', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function incomes()
    {
        return $this->hasMany(Income::class, 'income_expense_type_id');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'income_expense_type_id');
    }
}
