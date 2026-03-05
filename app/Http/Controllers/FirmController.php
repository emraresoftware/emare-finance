<?php

namespace App\Http\Controllers;

use App\Models\Firm;
use Illuminate\Http\Request;

class FirmController extends Controller
{
    public function index(Request $request)
    {
        $query = Firm::withCount('purchaseInvoices')->withSum('purchaseInvoices', 'total_amount');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('tax_number', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->get('has_invoices')) {
            $query->has('purchaseInvoices');
        }

        $sortBy = $request->get('sort', 'name');
        $sortDir = $request->get('dir', 'asc');
        $allowedSorts = ['name', 'balance', 'purchase_invoices_count', 'created_at'];
        if (!in_array($sortBy, $allowedSorts)) $sortBy = 'name';
        $query->orderBy($sortBy, $sortDir === 'desc' ? 'desc' : 'asc');

        $firms = $query->paginate(25)->appends($request->query());

        $stats = [
            'total' => Firm::count(),
            'active' => Firm::where('is_active', true)->count(),
            'with_invoices' => Firm::has('purchaseInvoices')->count(),
            'total_balance' => Firm::sum('balance'),
        ];

        return view('firms.index', compact('firms', 'stats'));
    }

    public function show(Firm $firm)
    {
        $firm->loadCount('purchaseInvoices');
        $invoices = $firm->purchaseInvoices()->with('branch')->latest('invoice_date')->paginate(20);

        $firmStats = [
            'total_invoices' => $firm->purchase_invoices_count,
            'total_amount' => $firm->purchaseInvoices()->sum('total_amount'),
            'avg_amount' => $firm->purchaseInvoices()->avg('total_amount') ?? 0,
            'last_invoice' => $firm->purchaseInvoices()->latest('invoice_date')->value('invoice_date'),
        ];

        return view('firms.show', compact('firm', 'invoices', 'firmStats'));
    }
}
