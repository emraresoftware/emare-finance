<?php

namespace App\Http\Controllers;

use App\Models\TaxRate;
use Illuminate\Http\Request;

class TaxRateController extends Controller
{
    public function index(Request $request)
    {
        $query = TaxRate::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%");
            });
        }

        if ($request->filled('code')) {
            $query->where('code', $request->code);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $taxRates = $query->orderBy('sort_order')->orderBy('code')->orderBy('rate')->get();

        $grouped = TaxRate::active()->orderBy('sort_order')->orderBy('rate')->get()->groupBy('code');

        $stats = [
            'total' => TaxRate::count(),
            'active' => TaxRate::where('is_active', true)->count(),
            'tax_codes' => TaxRate::distinct('code')->count('code'),
            'default_kdv' => TaxRate::where('code', 'KDV')->where('is_default', true)->first(),
        ];

        $taxCodes = TaxRate::distinct()->pluck('code')->sort()->values();

        return view('tax-rates.index', compact('taxRates', 'grouped', 'stats', 'taxCodes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20',
            'rate' => 'required|numeric|min:0|max:999',
            'type' => 'required|in:percentage,fixed',
            'description' => 'nullable|string|max:500',
            'is_default' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $validated['is_default'] = $request->has('is_default');
        $validated['is_active'] = $request->has('is_active') || !$request->has('_submit');

        // Eğer varsayılan olarak işaretlenmişse ve KDV ise, diğer KDV varsayılanlarını kaldır
        if ($validated['is_default'] && $validated['code'] === 'KDV') {
            TaxRate::where('code', 'KDV')->where('is_default', true)->update(['is_default' => false]);
        }

        TaxRate::create($validated);

        return redirect()->route('tax_rates.index')->with('success', 'Vergi oranı başarıyla eklendi.');
    }

    public function update(Request $request, TaxRate $taxRate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20',
            'rate' => 'required|numeric|min:0|max:999',
            'type' => 'required|in:percentage,fixed',
            'description' => 'nullable|string|max:500',
            'is_default' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $validated['is_default'] = $request->has('is_default');
        $validated['is_active'] = $request->has('is_active');

        if ($validated['is_default'] && $validated['code'] === 'KDV') {
            TaxRate::where('code', 'KDV')->where('is_default', true)->where('id', '!=', $taxRate->id)->update(['is_default' => false]);
        }

        $taxRate->update($validated);

        return redirect()->route('tax_rates.index')->with('success', 'Vergi oranı başarıyla güncellendi.');
    }

    public function destroy(TaxRate $taxRate)
    {
        $taxRate->delete();
        return redirect()->route('tax_rates.index')->with('success', 'Vergi oranı başarıyla silindi.');
    }

    // ── API Endpoints ──

    public function apiList(Request $request)
    {
        $query = TaxRate::active()->orderBy('sort_order')->orderBy('rate');

        if ($request->filled('code')) {
            $query->where('code', strtoupper($request->code));
        }

        return response()->json($query->get());
    }

    public function apiGrouped()
    {
        $grouped = TaxRate::groupedByCode();
        return response()->json($grouped);
    }
}
