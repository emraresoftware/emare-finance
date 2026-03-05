<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Category;
use App\Models\StockMovement;
use App\Models\StaffMotion;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    /**
     * Satış raporu
     */
    public function sales(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $groupBy = $request->get('group_by', 'day'); // day, week, month

        $dateFormat = match ($groupBy) {
            'day' => '%Y-%m-%d',
            'week' => '%Y-%u',
            'month' => '%Y-%m',
            default => '%Y-%m-%d',
        };

        $salesData = Sale::where('status', 'completed')
            ->whereBetween('sold_at', [$startDate, $endDate . ' 23:59:59'])
            ->groupBy('period')
            ->orderBy('period')
            ->get([
                DB::raw("DATE_FORMAT(sold_at, '{$dateFormat}') as period"),
                DB::raw('COUNT(*) as sale_count'),
                DB::raw('SUM(grand_total) as revenue'),
                DB::raw('SUM(discount_total) as discounts'),
                DB::raw('SUM(vat_total) as vat'),
                DB::raw('AVG(grand_total) as avg_sale'),
            ]);

        $summary = [
            'total_revenue' => $salesData->sum('revenue'),
            'total_sales' => $salesData->sum('sale_count'),
            'avg_daily' => $salesData->avg('revenue'),
            'total_discounts' => $salesData->sum('discounts'),
            'total_vat' => $salesData->sum('vat'),
        ];

        return view('reports.sales', compact('salesData', 'summary', 'startDate', 'endDate', 'groupBy'));
    }

    /**
     * Ürün raporu - en çok satanlar
     */
    public function products(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $topProducts = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status', 'completed')
            ->whereBetween('sales.sold_at', [$startDate, $endDate . ' 23:59:59'])
            ->groupBy('sale_items.product_name')
            ->orderByDesc('total_revenue')
            ->limit(50)
            ->get([
                'sale_items.product_name',
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.total) as total_revenue'),
                DB::raw('COUNT(DISTINCT sale_items.sale_id) as sale_count'),
            ]);

        return view('reports.products', compact('topProducts', 'startDate', 'endDate'));
    }

    /**
     * Kâr analizi
     */
    public function profit(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $profitData = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->leftJoin('products', 'sale_items.product_id', '=', 'products.id')
            ->where('sales.status', 'completed')
            ->whereBetween('sales.sold_at', [$startDate, $endDate . ' 23:59:59'])
            ->selectRaw('
                SUM(sale_items.total) as total_revenue,
                SUM(sale_items.quantity * COALESCE(products.purchase_price, 0)) as total_cost,
                SUM(sale_items.total) - SUM(sale_items.quantity * COALESCE(products.purchase_price, 0)) as gross_profit
            ')
            ->first();

        return view('reports.profit', compact('profitData', 'startDate', 'endDate'));
    }

    /**
     * Günlük Rapor - Bugünün satışları
     */
    public function daily(Request $request)
    {
        $date = $request->get('date', now()->format('Y-m-d'));

        $sales = Sale::with(['customer', 'branch'])
            ->whereDate('sold_at', $date)
            ->orderBy('sold_at', 'desc')
            ->paginate(25)->appends($request->query());

        $summary = [
            'total_sales' => Sale::whereDate('sold_at', $date)->where('status', 'completed')->count(),
            'total_revenue' => Sale::whereDate('sold_at', $date)->where('status', 'completed')->sum('grand_total'),
            'total_discount' => Sale::whereDate('sold_at', $date)->where('status', 'completed')->sum('discount'),
        ];

        return view('reports.daily', compact('sales', 'summary', 'date'));
    }

    /**
     * Tarihsel Rapor - Tarih aralığı bazlı satışlar
     */
    public function historical(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $sales = Sale::with(['customer', 'branch'])
            ->whereBetween('sold_at', [$startDate, $endDate . ' 23:59:59'])
            ->where('status', 'completed')
            ->orderBy('sold_at', 'desc')
            ->paginate(25)->appends($request->query());

        $summary = [
            'total_sales' => Sale::whereBetween('sold_at', [$startDate, $endDate . ' 23:59:59'])->where('status', 'completed')->count(),
            'total_revenue' => Sale::whereBetween('sold_at', [$startDate, $endDate . ' 23:59:59'])->where('status', 'completed')->sum('grand_total'),
        ];

        return view('reports.historical', compact('sales', 'summary', 'startDate', 'endDate'));
    }

    /**
     * Grupsal Rapor - Ürün grupları bazlı satış analizi
     */
    public function groups(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $groupData = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->leftJoin('products', 'sale_items.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->where('sales.status', 'completed')
            ->whereBetween('sales.sold_at', [$startDate, $endDate . ' 23:59:59'])
            ->groupBy('categories.name')
            ->orderByDesc('total_sales')
            ->get([
                DB::raw("COALESCE(categories.name, 'Grupsuz') as group_name"),
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.total) as total_sales'),
                DB::raw('SUM(sale_items.total) - SUM(sale_items.quantity * COALESCE(products.purchase_price, 0)) as total_profit'),
            ]);

        return view('reports.groups', compact('groupData', 'startDate', 'endDate'));
    }

    /**
     * Ürün Korelasyon Raporu
     */
    public function correlation(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $barcode = $request->get('barcode');

        $correlationData = collect();

        if ($barcode) {
            // Seçilen ürünle birlikte satılan diğer ürünleri bul
            $saleIds = DB::table('sale_items')
                ->where('barcode', $barcode)
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->whereBetween('sales.sold_at', [$startDate, $endDate . ' 23:59:59'])
                ->pluck('sale_items.sale_id');

            if ($saleIds->isNotEmpty()) {
                $correlationData = DB::table('sale_items')
                    ->whereIn('sale_id', $saleIds)
                    ->where('barcode', '!=', $barcode)
                    ->groupBy('barcode', 'product_name')
                    ->orderByDesc('sale_count')
                    ->limit(50)
                    ->get([
                        'barcode',
                        'product_name',
                        DB::raw('SUM(quantity) as total_quantity'),
                        DB::raw('SUM(total) as total_amount'),
                        DB::raw('COUNT(DISTINCT sale_id) as sale_count'),
                    ]);
            }
        }

        return view('reports.correlation', compact('correlationData', 'startDate', 'endDate', 'barcode'));
    }

    /**
     * Stok Hareket Raporu
     */
    public function stockMovement(Request $request)
    {
        $query = StockMovement::with('product');

        if ($startDate = $request->get('start_date')) {
            $query->whereDate('movement_date', '>=', $startDate);
        }
        if ($endDate = $request->get('end_date')) {
            $query->whereDate('movement_date', '<=', $endDate);
        }
        if ($type = $request->get('type')) {
            $query->where('type', $type);
        }

        $movements = $query->latest('movement_date')->paginate(25)->appends($request->query());

        return view('reports.stock-movement', compact('movements'));
    }

    /**
     * Personel Hareket Raporu
     */
    public function staffMovement(Request $request)
    {
        $query = StaffMotion::with('staff');

        if ($staffId = $request->get('staff_id')) {
            $query->where('staff_id', $staffId);
        }
        if ($action = $request->get('action')) {
            $query->where('action', $action);
        }

        $motions = $query->latest('action_date')->paginate(25)->appends($request->query());
        $staffList = Staff::orderBy('name')->get();

        return view('reports.staff-movement', compact('motions', 'staffList'));
    }
}
