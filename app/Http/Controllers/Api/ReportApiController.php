<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportApiController extends Controller
{
    public function daily(Request $request)
    {
        $date = $request->get('date', now()->toDateString());

        $sales = Sale::where('status', 'completed')->whereDate('sold_at', $date);

        return response()->json([
            'date' => $date,
            'total_sales' => $sales->count(),
            'total_revenue' => (float) $sales->sum('grand_total'),
            'total_discount' => (float) $sales->sum('discount'),
            'cash' => (float) (clone $sales)->where('payment_method', 'cash')->sum('grand_total'),
            'card' => (float) (clone $sales)->where('payment_method', 'card')->sum('grand_total'),
            'credit' => (float) (clone $sales)->where('payment_method', 'credit')->sum('grand_total'),
        ]);
    }

    public function topProducts(Request $request)
    {
        $days = $request->get('days', 30);

        $products = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status', 'completed')
            ->where('sales.sold_at', '>=', now()->subDays($days))
            ->groupBy('products.id', 'products.name', 'products.barcode')
            ->orderByDesc('total_amount')
            ->limit($request->get('limit', 20))
            ->get([
                'products.id',
                'products.name',
                'products.barcode',
                DB::raw('SUM(sale_items.quantity) as total_qty'),
                DB::raw('SUM(sale_items.total) as total_amount'),
                DB::raw('COUNT(DISTINCT sales.id) as sale_count'),
            ]);

        return response()->json(['products' => $products]);
    }

    public function revenueChart(Request $request)
    {
        $days = $request->get('days', 30);

        $data = Sale::where('status', 'completed')
            ->where('sold_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get([
                DB::raw('DATE(sold_at) as date'),
                DB::raw('SUM(grand_total) as revenue'),
                DB::raw('COUNT(*) as count'),
            ]);

        return response()->json(['chart' => $data]);
    }

    public function paymentMethods(Request $request)
    {
        $days = $request->get('days', 30);

        $data = Sale::where('status', 'completed')
            ->where('sold_at', '>=', now()->subDays($days))
            ->groupBy('payment_method')
            ->get([
                'payment_method',
                DB::raw('SUM(grand_total) as total'),
                DB::raw('COUNT(*) as count'),
            ]);

        return response()->json(['methods' => $data]);
    }
}
