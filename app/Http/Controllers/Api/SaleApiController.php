<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with(['customer:id,name', 'branch:id,name'])
            ->where('status', '!=', 'cancelled');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('receipt_no', 'like', "%{$search}%")
                  ->orWhereHas('customer', fn($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        if ($startDate = $request->get('start_date')) {
            $query->where('sold_at', '>=', $startDate);
        }
        if ($endDate = $request->get('end_date')) {
            $query->where('sold_at', '<=', $endDate . ' 23:59:59');
        }

        if ($paymentMethod = $request->get('payment_method')) {
            $query->where('payment_method', $paymentMethod);
        }

        $sales = $query->latest('sold_at')
            ->paginate($request->get('per_page', 20));

        return response()->json($sales);
    }

    public function show(Sale $sale)
    {
        $sale->load(['customer:id,name,phone', 'branch:id,name', 'items.product:id,name,barcode']);

        return response()->json(['sale' => $sale]);
    }

    public function summary(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfDay()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());

        $query = Sale::where('status', 'completed')
            ->whereBetween('sold_at', [$startDate, $endDate . ' 23:59:59']);

        $summary = [
            'total_sales' => $query->count(),
            'total_revenue' => (float) $query->sum('grand_total'),
            'total_discount' => (float) $query->sum('discount'),
            'avg_sale' => (float) $query->avg('grand_total'),
            'cash_total' => (float) (clone $query)->where('payment_method', 'cash')->sum('grand_total'),
            'card_total' => (float) (clone $query)->where('payment_method', 'card')->sum('grand_total'),
            'credit_total' => (float) (clone $query)->where('payment_method', 'credit')->sum('grand_total'),
        ];

        // Saatlik dağılım
        $hourly = (clone $query)
            ->selectRaw('HOUR(sold_at) as hour, SUM(grand_total) as total, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        return response()->json([
            'summary' => $summary,
            'hourly' => $hourly,
        ]);
    }
}
