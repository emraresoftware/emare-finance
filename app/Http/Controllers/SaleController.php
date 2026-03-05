<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Branch;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with(['customer', 'branch', 'items']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('receipt_no', 'like', "%{$search}%")
                    ->orWhere('staff_name', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($payment = $request->get('payment_method')) {
            $query->where('payment_method', $payment);
        }

        if ($startDate = $request->get('start_date')) {
            $query->whereDate('sold_at', '>=', $startDate);
        }

        if ($endDate = $request->get('end_date')) {
            $query->whereDate('sold_at', '<=', $endDate);
        }

        if ($branchId = $request->get('branch_id')) {
            $query->where('branch_id', $branchId);
        }

        // Sıralama
        $sortBy = $request->get('sort', 'sold_at');
        $sortDir = $request->get('dir', 'desc');
        $allowedSorts = ['sold_at', 'receipt_no', 'grand_total', 'total_items', 'created_at'];
        if (!in_array($sortBy, $allowedSorts)) $sortBy = 'sold_at';
        $query->orderBy($sortBy, $sortDir === 'asc' ? 'asc' : 'desc');

        $sales = $query->paginate(25)->appends($request->query());
        $branches = Branch::orderBy('name')->get();

        $stats = [
            'total_sales' => Sale::where('status', 'completed')->count(),
            'total_revenue' => Sale::where('status', 'completed')->sum('grand_total'),
            'avg_sale' => Sale::where('status', 'completed')->avg('grand_total') ?? 0,
            'today_count' => Sale::where('status', 'completed')->whereDate('sold_at', today())->count(),
            'today_revenue' => Sale::where('status', 'completed')->whereDate('sold_at', today())->sum('grand_total'),
            'cancelled' => Sale::where('status', 'cancelled')->count(),
        ];

        return view('sales.index', compact('sales', 'branches', 'stats'));
    }

    public function show(Sale $sale)
    {
        $sale->load(['customer', 'branch', 'items.product', 'user']);

        $saleStats = [
            'item_count' => $sale->items->count(),
            'total_quantity' => $sale->items->sum('quantity'),
            'total_discount' => $sale->items->sum('discount'),
            'profit' => $sale->items->sum(function ($item) {
                $cost = $item->product ? $item->product->purchase_price * $item->quantity : 0;
                return $item->total - $cost;
            }),
        ];

        return view('sales.show', compact('sale', 'saleStats'));
    }

    public function export(Request $request): StreamedResponse
    {
        $query = Sale::with(['customer', 'branch'])->where('status', 'completed');

        if ($startDate = $request->get('start_date')) {
            $query->whereDate('sold_at', '>=', $startDate);
        }
        if ($endDate = $request->get('end_date')) {
            $query->whereDate('sold_at', '<=', $endDate);
        }

        $sales = $query->latest('sold_at')->get();

        return response()->streamDownload(function () use ($sales) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Fiş No', 'Tarih', 'Müşteri', 'Şube', 'Ödeme', 'Kalem', 'Toplam', 'Durum'], ';');
            foreach ($sales as $sale) {
                fputcsv($handle, [
                    $sale->receipt_no ?? '', $sale->sold_at?->format('d.m.Y H:i') ?? '',
                    $sale->customer?->name ?? '', $sale->branch?->name ?? '',
                    $sale->payment_method, $sale->total_items,
                    number_format($sale->grand_total, 2, ',', ''), $sale->status,
                ], ';');
            }
            fclose($handle);
        }, 'satislar-' . date('Y-m-d') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
