<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class ServiceCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = ServiceCategory::withCount('products', 'recurringInvoices');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $categories = $query->orderBy('sort_order')->orderBy('name')->get();

        $roots = ServiceCategory::roots()
            ->with('children')
            ->withCount('products', 'recurringInvoices')
            ->orderBy('sort_order')
            ->get();

        $stats = [
            'total' => ServiceCategory::count(),
            'active' => ServiceCategory::where('is_active', true)->count(),
            'with_products' => ServiceCategory::whereHas('products')->count(),
            'with_recurring' => ServiceCategory::whereHas('recurringInvoices')->count(),
        ];

        return view('service-categories.index', compact('categories', 'roots', 'stats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'parent_id' => 'nullable|exists:service_categories,id',
            'color' => 'nullable|string|max:20',
            'icon' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') || !$request->has('_submit');

        ServiceCategory::create($validated);

        return redirect()->route('service_categories.index')->with('success', 'Hizmet kategorisi başarıyla eklendi.');
    }

    public function update(Request $request, ServiceCategory $serviceCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'parent_id' => 'nullable|exists:service_categories,id',
            'color' => 'nullable|string|max:20',
            'icon' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Kendini üst kategori yapmasını engelle
        if (isset($validated['parent_id']) && $validated['parent_id'] == $serviceCategory->id) {
            $validated['parent_id'] = null;
        }

        $serviceCategory->update($validated);

        return redirect()->route('service_categories.index')->with('success', 'Hizmet kategorisi başarıyla güncellendi.');
    }

    public function destroy(ServiceCategory $serviceCategory)
    {
        // Alt kategorileri üst kategoriye taşı
        $serviceCategory->children()->update(['parent_id' => $serviceCategory->parent_id]);

        $serviceCategory->delete();

        return redirect()->route('service_categories.index')->with('success', 'Hizmet kategorisi başarıyla silindi.');
    }
}
