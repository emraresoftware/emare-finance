<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Genel istatistikler
        $stats = [
            'total_products' => Product::count(),
            'total_customers' => Customer::count(),
            'total_sales' => Sale::count(),
            'total_branches' => Branch::count(),
            'total_revenue' => Sale::where('status', 'completed')->sum('grand_total'),
            'today_revenue' => Sale::where('status', 'completed')
                ->whereDate('sold_at', today())->sum('grand_total'),
            'month_revenue' => Sale::where('status', 'completed')
                ->whereMonth('sold_at', now()->month)
                ->whereYear('sold_at', now()->year)->sum('grand_total'),
            'low_stock_count' => Product::whereColumn('stock_quantity', '<=', 'critical_stock')
                ->where('critical_stock', '>', 0)->count(),
        ];

        // Son satışlar
        $recentSales = Sale::with(['customer', 'branch'])
            ->latest('sold_at')
            ->take(10)
            ->get();

        // Son 7 günlük satış grafiği
        $dailySales = Sale::where('status', 'completed')
            ->where('sold_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get([
                DB::raw('DATE(sold_at) as date'),
                DB::raw('SUM(grand_total) as total'),
                DB::raw('COUNT(*) as count'),
            ]);

        // Kategori bazlı satışlar
        $categorySales = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status', 'completed')
            ->groupBy('categories.name')
            ->orderByDesc('total')
            ->limit(10)
            ->get([
                'categories.name',
                DB::raw('SUM(sale_items.total) as total'),
                DB::raw('SUM(sale_items.quantity) as quantity'),
            ]);

        // Düşük stok uyarıları
        $lowStockProducts = Product::whereColumn('stock_quantity', '<=', 'critical_stock')
            ->where('critical_stock', '>', 0)
            ->orderBy('stock_quantity')
            ->take(10)
            ->get();

        return view('dashboard', compact(
            'stats', 'recentSales', 'dailySales',
            'categorySales', 'lowStockProducts'
        ));
    }
}
