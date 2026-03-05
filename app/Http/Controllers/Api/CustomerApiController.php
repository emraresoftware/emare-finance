<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::where('is_active', true);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->get('has_debt')) {
            $query->where('balance', '<', 0);
        }

        $sort = $request->get('sort', 'name');
        $dir = $request->get('dir', 'asc');
        $query->orderBy($sort, $dir);

        $customers = $query->withCount('sales')
            ->paginate($request->get('per_page', 20));

        return response()->json($customers);
    }

    public function show(Customer $customer)
    {
        $customer->loadCount('sales');

        $stats = [
            'total_purchases' => (float) $customer->sales()->where('status', 'completed')->sum('grand_total'),
            'total_sales_count' => $customer->sales()->where('status', 'completed')->count(),
            'last_purchase' => $customer->sales()->latest('sold_at')->value('sold_at'),
            'avg_purchase' => (float) $customer->sales()->where('status', 'completed')->avg('grand_total'),
        ];

        return response()->json([
            'customer' => $customer,
            'stats' => $stats,
        ]);
    }

    public function sales(Customer $customer, Request $request)
    {
        $sales = $customer->sales()
            ->with('branch:id,name')
            ->where('status', 'completed')
            ->latest('sold_at')
            ->paginate($request->get('per_page', 20));

        return response()->json($sales);
    }
}
