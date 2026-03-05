<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Staff;
use Illuminate\Support\Facades\DB;

class DashboardApiController extends Controller
{
    public function index()
    {
        $today = now()->startOfDay();
        $weekAgo = now()->subDays(7)->startOfDay();
        $monthStart = now()->startOfMonth();

        // Genel istatistikler
        $stats = [
            'total_products' => Product::where('is_active', true)->count(),
            'total_customers' => Customer::where('is_active', true)->count(),
            'total_branches' => Branch::count(),
            'total_staff' => Staff::where('is_active', true)->count(),
            'today_sales_count' => Sale::where('status', 'completed')->whereDate('sold_at', $today)->count(),
            'today_revenue' => (float) Sale::where('status', 'completed')->whereDate('sold_at', $today)->sum('grand_total'),
            'week_revenue' => (float) Sale::where('status', 'completed')->where('sold_at', '>=', $weekAgo)->sum('grand_total'),
            'month_revenue' => (float) Sale::where('status', 'completed')->where('sold_at', '>=', $monthStart)->sum('grand_total'),
            'total_revenue' => (float) Sale::where('status', 'completed')->sum('grand_total'),
            'low_stock_count' => Product::where('is_active', true)
                ->whereColumn('stock_quantity', '<=', 'critical_stock')
                ->where('critical_stock', '>', 0)->count(),
        ];

        // Son 7 gün grafik verisi
        $dailyChart = Sale::where('status', 'completed')
            ->where('sold_at', '>=', $weekAgo)
            ->groupBy('date')
            ->orderBy('date')
            ->get([
                DB::raw('DATE(sold_at) as date'),
                DB::raw('SUM(grand_total) as total'),
                DB::raw('COUNT(*) as count'),
            ]);

        // Son 5 satış
        $recentSales = Sale::with(['customer:id,name', 'branch:id,name'])
            ->where('status', 'completed')
            ->latest('sold_at')
            ->take(5)
            ->get(['id', 'receipt_no', 'customer_id', 'branch_id', 'grand_total', 'payment_method', 'sold_at']);

        // Düşük stok ürünler (ilk 5)
        $lowStock = Product::where('is_active', true)
            ->whereColumn('stock_quantity', '<=', 'critical_stock')
            ->where('critical_stock', '>', 0)
            ->orderBy('stock_quantity')
            ->take(5)
            ->get(['id', 'name', 'barcode', 'stock_quantity', 'critical_stock', 'unit']);

        return response()->json([
            'stats' => $stats,
            'daily_chart' => $dailyChart,
            'recent_sales' => $recentSales,
            'low_stock' => $lowStock,
        ]);
    }
}
