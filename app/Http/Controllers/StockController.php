<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\StockCount;
use App\Models\Product;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function movements(Request $request)
    {
        $query = StockMovement::with('product');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%")
                    ->orWhere('transaction_code', 'like', "%{$search}%");
            });
        }

        if ($type = $request->get('type')) {
            $query->where('type', $type);
        }

        if ($startDate = $request->get('start_date')) {
            $query->whereDate('movement_date', '>=', $startDate);
        }
        if ($endDate = $request->get('end_date')) {
            $query->whereDate('movement_date', '<=', $endDate);
        }

        $movements = $query->latest('movement_date')->paginate(25)->appends($request->query());

        $stats = [
            'total_movements' => StockMovement::count(),
            'total_in' => StockMovement::where('type', 'in')->sum('quantity'),
            'total_out' => StockMovement::where('type', 'out')->sum('quantity'),
            'total_value' => StockMovement::sum('total'),
            'waste_count' => StockMovement::where('type', 'waste')->count(),
            'transfer_count' => StockMovement::where('type', 'transfer')->count(),
        ];

        return view('stock.movements', compact('movements', 'stats'));
    }

    public function counts(Request $request)
    {
        $query = StockCount::with('branch')->withCount('items');

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $counts = $query->latest('counted_at')->paginate(25)->appends($request->query());

        $countStats = [
            'total' => StockCount::count(),
            'completed' => StockCount::where('status', 'completed')->count(),
            'in_progress' => StockCount::where('status', 'in_progress')->count(),
            'total_items' => \App\Models\StockCountItem::count(),
        ];

        return view('stock.counts', compact('counts', 'countStats'));
    }

    public function countShow(StockCount $stockCount)
    {
        $stockCount->load(['branch', 'items.product']);

        $diffStats = [
            'total_items' => $stockCount->items->count(),
            'matched' => $stockCount->items->filter(fn($i) => ($i->counted_quantity ?? 0) == ($i->system_quantity ?? 0))->count(),
            'surplus' => $stockCount->items->filter(fn($i) => ($i->counted_quantity ?? 0) > ($i->system_quantity ?? 0))->count(),
            'deficit' => $stockCount->items->filter(fn($i) => ($i->counted_quantity ?? 0) < ($i->system_quantity ?? 0))->count(),
        ];

        return view('stock.count-show', compact('stockCount', 'diffStats'));
    }
}
