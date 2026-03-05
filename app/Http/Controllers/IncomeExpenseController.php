<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Expense;
use App\Models\IncomeExpenseType;
use Illuminate\Http\Request;

class IncomeExpenseController extends Controller
{
    public function incomes(Request $request)
    {
        $query = Income::with('type');

        if ($search = $request->get('search')) {
            $query->where('note', 'like', "%{$search}%");
        }

        if ($startDate = $request->get('start_date')) {
            $query->whereDate('date', '>=', $startDate);
        }
        if ($endDate = $request->get('end_date')) {
            $query->whereDate('date', '<=', $endDate);
        }
        if ($typeId = $request->get('type_id')) {
            $query->where('income_expense_type_id', $typeId);
        }

        $incomes = $query->latest('date')->paginate(25)->appends($request->query());
        $types = IncomeExpenseType::where('direction', 'income')->get();

        $stats = [
            'total' => Income::count(),
            'total_amount' => Income::sum('amount'),
            'avg_amount' => Income::avg('amount') ?? 0,
            'this_month' => Income::whereMonth('date', now()->month)->whereYear('date', now()->year)->sum('amount'),
        ];

        return view('income-expense.incomes', compact('incomes', 'types', 'stats'));
    }

    public function expenses(Request $request)
    {
        $query = Expense::with('type');

        if ($search = $request->get('search')) {
            $query->where('note', 'like', "%{$search}%");
        }

        if ($startDate = $request->get('start_date')) {
            $query->whereDate('date', '>=', $startDate);
        }
        if ($endDate = $request->get('end_date')) {
            $query->whereDate('date', '<=', $endDate);
        }
        if ($typeId = $request->get('type_id')) {
            $query->where('income_expense_type_id', $typeId);
        }

        $expenses = $query->latest('date')->paginate(25)->appends($request->query());
        $types = IncomeExpenseType::where('direction', 'expense')->get();

        $stats = [
            'total' => Expense::count(),
            'total_amount' => Expense::sum('amount'),
            'avg_amount' => Expense::avg('amount') ?? 0,
            'this_month' => Expense::whereMonth('date', now()->month)->whereYear('date', now()->year)->sum('amount'),
        ];

        return view('income-expense.expenses', compact('expenses', 'types', 'stats'));
    }

    public function types()
    {
        $incomeTypes = IncomeExpenseType::where('direction', 'income')
            ->withCount('incomes')
            ->withSum('incomes', 'amount')
            ->orderBy('name')->get();
        $expenseTypes = IncomeExpenseType::where('direction', 'expense')
            ->withCount('expenses')
            ->withSum('expenses', 'amount')
            ->orderBy('name')->get();
        return view('income-expense.types', compact('incomeTypes', 'expenseTypes'));
    }
}
