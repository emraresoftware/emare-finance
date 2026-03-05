<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category:id,name')
            ->where('is_active', true);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($categoryId = $request->get('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($request->get('low_stock')) {
            $query->whereColumn('stock_quantity', '<=', 'critical_stock')
                  ->where('critical_stock', '>', 0);
        }

        $sort = $request->get('sort', 'name');
        $dir = $request->get('dir', 'asc');
        $query->orderBy($sort, $dir);

        $products = $query->paginate($request->get('per_page', 20));

        return response()->json($products);
    }

    public function show(Product $product)
    {
        $product->load('category:id,name');

        // Son 30 gün satış istatistikleri
        $salesStats = $product->saleItems()
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status', 'completed')
            ->where('sales.sold_at', '>=', now()->subDays(30))
            ->selectRaw('SUM(sale_items.quantity) as total_qty, SUM(sale_items.total) as total_amount, COUNT(DISTINCT sales.id) as sale_count')
            ->first();

        return response()->json([
            'product' => $product,
            'sales_stats' => [
                'total_quantity' => (float) ($salesStats->total_qty ?? 0),
                'total_amount' => (float) ($salesStats->total_amount ?? 0),
                'sale_count' => (int) ($salesStats->sale_count ?? 0),
            ],
        ]);
    }

    public function lowStock()
    {
        $products = Product::with('category:id,name')
            ->where('is_active', true)
            ->whereColumn('stock_quantity', '<=', 'critical_stock')
            ->where('critical_stock', '>', 0)
            ->orderBy('stock_quantity')
            ->get(['id', 'name', 'barcode', 'stock_quantity', 'critical_stock', 'unit', 'sale_price', 'category_id']);

        return response()->json(['products' => $products]);
    }

    public function categories()
    {
        $categories = Category::withCount(['products' => function ($q) {
            $q->where('is_active', true);
        }])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name']);

        return response()->json(['categories' => $categories]);
    }
}
