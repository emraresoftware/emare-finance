<?php

namespace App\Http\Controllers;

use App\Models\PurchaseInvoice;
use App\Models\Firm;
use App\Models\Branch;
use Illuminate\Http\Request;

class PurchaseInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseInvoice::with(['firm', 'branch'])->withCount('items');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                    ->orWhereHas('firm', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        }

        if ($firmId = $request->get('firm_id')) {
            $query->where('firm_id', $firmId);
        }

        if ($branchId = $request->get('branch_id')) {
            $query->where('branch_id', $branchId);
        }

        if ($startDate = $request->get('start_date')) {
            $query->whereDate('invoice_date', '>=', $startDate);
        }

        if ($endDate = $request->get('end_date')) {
            $query->whereDate('invoice_date', '<=', $endDate);
        }

        $sortBy = $request->get('sort', 'invoice_date');
        $sortDir = $request->get('dir', 'desc');
        $allowedSorts = ['invoice_date', 'invoice_no', 'total_amount', 'total_items'];
        if (!in_array($sortBy, $allowedSorts)) $sortBy = 'invoice_date';
        $query->orderBy($sortBy, $sortDir === 'asc' ? 'asc' : 'desc');

        $invoices = $query->paginate(25)->appends($request->query());
        $firms = Firm::orderBy('name')->get();
        $branches = Branch::orderBy('name')->get();

        $stats = [
            'total' => PurchaseInvoice::count(),
            'total_amount' => PurchaseInvoice::sum('total_amount'),
            'avg_amount' => PurchaseInvoice::avg('total_amount') ?? 0,
            'this_month' => PurchaseInvoice::whereMonth('invoice_date', now()->month)->whereYear('invoice_date', now()->year)->sum('total_amount'),
            'firm_count' => PurchaseInvoice::distinct('firm_id')->count('firm_id'),
        ];

        return view('invoices.index', compact('invoices', 'firms', 'branches', 'stats'));
    }

    public function show(PurchaseInvoice $invoice)
    {
        $invoice->load(['firm', 'branch', 'items.product']);

        $invoiceStats = [
            'item_count' => $invoice->items->count(),
            'total_quantity' => $invoice->items->sum('quantity'),
            'total_amount' => $invoice->total_amount,
        ];

        return view('invoices.show', compact('invoice', 'invoiceStats'));
    }
}
