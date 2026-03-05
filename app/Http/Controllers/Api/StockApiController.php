<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class StockApiController extends Controller
{
    public function overview()
    {
        $totalProducts = Product::where('is_active', true)->count();
        $lowStock = Product::where('is_active', true)
            ->whereColumn('stock_quantity', '<=', 'critical_stock')
            ->where('critical_stock', '>', 0)->count();
        $outOfStock = Product::where('is_active', true)->where('stock_quantity', '<=', 0)->count();
        $totalValue = (float) Product::where('is_active', true)
            ->selectRaw('SUM(stock_quantity * purchase_price) as value')->value('value');

        return response()->json([
            'total_products' => $totalProducts,
            'low_stock' => $lowStock,
            'out_of_stock' => $outOfStock,
            'total_value' => $totalValue,
        ]);
    }

    public function movements(Request $request)
    {
        $movements = StockMovement::with('product:id,name,barcode')
            ->latest()
            ->paginate($request->get('per_page', 20));

        return response()->json($movements);
    }

    public function alerts()
    {
        $products = Product::with('category:id,name')
            ->where('is_active', true)
            ->where(function ($q) {
                $q->where(function ($q2) {
                    $q2->whereColumn('stock_quantity', '<=', 'critical_stock')
                       ->where('critical_stock', '>', 0);
                })->orWhere('stock_quantity', '<=', 0);
            })
            ->orderBy('stock_quantity')
            ->get(['id', 'name', 'barcode', 'stock_quantity', 'critical_stock', 'unit', 'sale_price', 'category_id']);

        return response()->json(['alerts' => $products]);
    }
}
