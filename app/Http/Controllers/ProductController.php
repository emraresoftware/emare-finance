<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        // Arama
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        // Kategori filtresi
        if ($categoryId = $request->get('category_id')) {
            $query->where('category_id', $categoryId);
        }

        // Durum filtresi
        if ($request->has('status')) {
            $status = $request->get('status');
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'passive') {
                $query->where('is_active', false);
            }
        }

        // Stok durumu filtresi
        if ($request->get('low_stock')) {
            $query->whereColumn('stock_quantity', '<=', 'critical_stock')
                ->where('critical_stock', '>', 0);
        }

        // Stok yok filtresi
        if ($request->get('out_of_stock')) {
            $query->where('stock_quantity', '<=', 0);
        }

        // Fiyat aralığı
        if ($minPrice = $request->get('min_price')) {
            $query->where('sale_price', '>=', $minPrice);
        }
        if ($maxPrice = $request->get('max_price')) {
            $query->where('sale_price', '<=', $maxPrice);
        }

        // Sıralama
        $sortBy = $request->get('sort', 'name');
        $sortDir = $request->get('dir', 'asc');
        $allowedSorts = ['name', 'barcode', 'sale_price', 'purchase_price', 'stock_quantity', 'created_at'];
        if (!in_array($sortBy, $allowedSorts)) $sortBy = 'name';
        $query->orderBy($sortBy, $sortDir === 'desc' ? 'desc' : 'asc');

        $products = $query->paginate(25)->appends($request->query());
        $categories = Category::orderBy('name')->get();

        $stats = [
            'total' => Product::count(),
            'active' => Product::where('is_active', true)->count(),
            'low_stock' => Product::whereColumn('stock_quantity', '<=', 'critical_stock')
                ->where('critical_stock', '>', 0)->count(),
            'out_of_stock' => Product::where('stock_quantity', '<=', 0)->count(),
            'total_value' => Product::selectRaw('SUM(stock_quantity * sale_price) as value')->value('value') ?? 0,
            'total_cost' => Product::selectRaw('SUM(stock_quantity * purchase_price) as value')->value('value') ?? 0,
        ];

        return view('products.index', compact('products', 'categories', 'stats'));
    }

    public function show(Product $product)
    {
        $product->load(['category', 'variants', 'parent']);

        // Son satışlar
        $salesHistory = $product->saleItems()
            ->with('sale')
            ->latest()
            ->take(20)
            ->get();

        // Stok hareketleri
        $stockMovements = StockMovement::where('product_id', $product->id)
            ->orWhere('barcode', $product->barcode)
            ->orderByDesc('movement_date')
            ->take(20)
            ->get();

        // Alış fatura kalemleri
        $purchaseItems = $product->purchaseInvoiceItems()
            ->with('purchaseInvoice.firm')
            ->latest()
            ->take(20)
            ->get();

        // Şube stokları
        $branchStocks = $product->branches()->get();

        // Satış istatistikleri
        $salesStats = [
            'total_quantity' => $product->saleItems()->sum('quantity'),
            'total_revenue' => $product->saleItems()->sum('total'),
            'avg_price' => $product->saleItems()->avg('unit_price') ?? 0,
            'sale_count' => $product->saleItems()->distinct('sale_id')->count('sale_id'),
            'last_30_days' => $product->saleItems()
                ->whereHas('sale', fn($q) => $q->where('sold_at', '>=', now()->subDays(30)))
                ->sum('quantity'),
        ];

        // Son 12 ay satış trendi
        $monthlySales = $product->saleItems()
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.sold_at', '>=', now()->subMonths(12))
            ->selectRaw("DATE_FORMAT(sales.sold_at, '%Y-%m') as month, SUM(sale_items.quantity) as qty, SUM(sale_items.total) as revenue")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('products.show', compact(
            'product', 'salesHistory', 'stockMovements', 'purchaseItems',
            'branchStocks', 'salesStats', 'monthlySales'
        ));
    }

    public function groups()
    {
        $categories = Category::withCount('products')
            ->with('children')
            ->orderBy('name')
            ->get();

        // Kategori bazlı istatistikler
        $categoryStats = Category::select('categories.id', 'categories.name')
            ->leftJoin('products', 'products.category_id', '=', 'categories.id')
            ->selectRaw('COUNT(products.id) as product_count')
            ->selectRaw('COALESCE(SUM(products.stock_quantity * products.sale_price), 0) as stock_value')
            ->selectRaw('COALESCE(AVG(products.sale_price), 0) as avg_price')
            ->selectRaw('COALESCE(SUM(products.stock_quantity), 0) as total_stock')
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('categories.name')
            ->get();

        $totalStats = [
            'total_categories' => Category::count(),
            'active_categories' => Category::where('is_active', true)->count(),
            'total_stock_value' => Product::selectRaw('SUM(stock_quantity * sale_price) as val')->value('val') ?? 0,
            'avg_products_per_category' => $categories->count() > 0
                ? round($categories->avg('products_count'), 1)
                : 0,
        ];

        return view('products.groups', compact('categories', 'categoryStats', 'totalStats'));
    }

    /**
     * Yeni Kategori Ekle
     */
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Category::create($validated);

        return redirect()->route('products.groups')->with('success', 'Kategori başarıyla eklendi.');
    }

    /**
     * Kategori Güncelle
     */
    public function updateCategory(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Kendini üst kategori yapmasını engelle
        if (isset($validated['parent_id']) && $validated['parent_id'] == $category->id) {
            $validated['parent_id'] = null;
        }

        $category->update($validated);

        return redirect()->route('products.groups')->with('success', 'Kategori başarıyla güncellendi.');
    }

    /**
     * Kategori Sil
     */
    public function destroyCategory(Category $category)
    {
        // Alt kategorileri üst kategoriye taşı
        $category->children()->update(['parent_id' => $category->parent_id]);

        // Ürünlerin kategori bağlantısını kaldır
        $category->products()->update(['category_id' => null]);

        $category->delete();

        return redirect()->route('products.groups')->with('success', 'Kategori başarıyla silindi.');
    }

    public function variants(Request $request)
    {
        $query = Product::with(['category', 'parent', 'variants'])
            ->where(function ($q) {
                $q->whereNotNull('variant_type')
                    ->where('variant_type', '!=', '')
                    ->orWhereNotNull('parent_id')
                    ->orWhereHas('variants');
            });

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%")
                    ->orWhere('variant_type', 'like', "%{$search}%");
            });
        }

        if ($categoryId = $request->get('category_id')) {
            $query->where('category_id', $categoryId);
        }

        $variants = $query->orderBy('name')->paginate(25)->appends($request->query());
        $categories = Category::orderBy('name')->get();

        $stats = [
            'total_variants' => Product::whereNotNull('variant_type')->where('variant_type', '!=', '')->count(),
            'parent_products' => Product::whereHas('variants')->count(),
            'variant_types' => Product::whereNotNull('variant_type')
                ->where('variant_type', '!=', '')
                ->distinct('variant_type')
                ->count(),
        ];

        return view('products.variants', compact('variants', 'categories', 'stats'));
    }

    public function refunds(Request $request)
    {
        // İade edilmiş satışlardan ürün iade verilerini çek
        $query = SaleItem::with(['sale', 'product'])
            ->whereHas('sale', function ($q) {
                $q->where('status', 'refunded');
            });

        // Tarih filtresi
        if ($startDate = $request->get('start_date')) {
            $query->whereHas('sale', fn($q) => $q->where('sold_at', '>=', $startDate));
        }
        if ($endDate = $request->get('end_date')) {
            $query->whereHas('sale', fn($q) => $q->where('sold_at', '<=', $endDate . ' 23:59:59'));
        }

        // Arama
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%")
                    ->orWhereHas('sale', fn($sq) => $sq->where('receipt_no', 'like', "%{$search}%"));
            });
        }

        $refunds = $query->latest()->paginate(25)->appends($request->query());

        // Negatif miktarlı stok hareketlerini de çek (refund tipi)
        $refundMovementsQuery = StockMovement::where('type', 'refund');
        if ($startDate) {
            $refundMovementsQuery->where('movement_date', '>=', $startDate);
        }
        if ($endDate) {
            $refundMovementsQuery->where('movement_date', '<=', $endDate . ' 23:59:59');
        }
        $refundMovements = $refundMovementsQuery->orderByDesc('movement_date')->take(50)->get();

        $stats = [
            'total_refunded_sales' => Sale::where('status', 'refunded')->count(),
            'total_refunded_amount' => Sale::where('status', 'refunded')->sum('grand_total'),
            'total_refunded_items' => SaleItem::whereHas('sale', fn($q) => $q->where('status', 'refunded'))->sum('quantity'),
            'this_month' => Sale::where('status', 'refunded')
                ->where('sold_at', '>=', now()->startOfMonth())
                ->sum('grand_total'),
        ];

        return view('products.refunds', compact('refunds', 'refundMovements', 'stats'));
    }

    public function refundRequests(Request $request)
    {
        // İptal edilmiş satışları iade talepleri olarak göster
        $query = Sale::with(['branch', 'customer'])
            ->where('status', 'cancelled');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('receipt_no', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn($sq) => $sq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($status = $request->get('status')) {
            // cancelled satışları farklı bir filtreleme mantığıyla göster
            if ($status === 'pending') {
                $query->whereNull('notes');
            } elseif ($status === 'approved') {
                $query->whereNotNull('notes')->where('notes', 'like', '%onay%');
            } elseif ($status === 'rejected') {
                $query->whereNotNull('notes')->where('notes', 'like', '%red%');
            }
        }

        $requests = $query->latest('sold_at')->paginate(25)->appends($request->query());

        $stats = [
            'total' => Sale::where('status', 'cancelled')->count(),
            'total_amount' => Sale::where('status', 'cancelled')->sum('grand_total'),
        ];

        return view('products.refund-requests', compact('requests', 'stats'));
    }

    public function labels(Request $request)
    {
        $products = collect();
        if ($search = $request->get('search')) {
            $products = Product::where('name', 'like', "%{$search}%")
                ->orWhere('barcode', 'like', "%{$search}%")
                ->limit(24)->get();
        }
        return view('products.labels', compact('products'));
    }

    /**
     * Ürünleri CSV olarak dışa aktar
     */
    public function export(Request $request): StreamedResponse
    {
        $query = Product::with('category');

        if ($categoryId = $request->get('category_id')) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->orderBy('name')->get();

        return response()->streamDownload(function () use ($products) {
            $handle = fopen('php://output', 'w');

            // BOM for UTF-8
            fwrite($handle, "\xEF\xBB\xBF");

            // Başlık satırı
            fputcsv($handle, [
                'Barkod', 'Ürün Adı', 'Kategori', 'Birim',
                'Alış Fiyatı', 'Satış Fiyatı', 'KDV %',
                'Stok Miktarı', 'Kritik Stok', 'Durum',
            ], ';');

            foreach ($products as $product) {
                fputcsv($handle, [
                    $product->barcode ?? '',
                    $product->name,
                    $product->category?->name ?? '',
                    $product->unit,
                    number_format($product->purchase_price, 2, ',', ''),
                    number_format($product->sale_price, 2, ',', ''),
                    $product->vat_rate,
                    $product->stock_quantity,
                    $product->critical_stock,
                    $product->is_active ? 'Aktif' : 'Pasif',
                ], ';');
            }

            fclose($handle);
        }, 'urunler-' . date('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Alt Ürün Tanımları - parent_id olan ürünler
     */
    public function subProducts(Request $request)
    {
        // Ana ürünleri (parent) ve alt ürünlerini listele
        $query = Product::with(['parent', 'variants', 'category'])
            ->where(function ($q) {
                $q->whereNotNull('parent_id')
                  ->orWhereHas('variants');
            });

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($categoryId = $request->get('category_id')) {
            $query->where('category_id', $categoryId);
        }

        // Sadece ana ürünleri göster
        if ($request->get('only_parents')) {
            $query->whereNull('parent_id')->whereHas('variants');
        }

        // Sadece alt ürünleri göster
        if ($request->get('only_children')) {
            $query->whereNotNull('parent_id');
        }

        $products = $query->orderBy('name')->paginate(25)->appends($request->query());
        $categories = Category::orderBy('name')->get();

        $stats = [
            'total_parents' => Product::whereHas('variants')->count(),
            'total_children' => Product::whereNotNull('parent_id')->count(),
            'total_relations' => Product::whereNotNull('parent_id')->count() + Product::whereHas('variants')->count(),
            'categories_with_subs' => Product::whereNotNull('parent_id')
                ->distinct('category_id')
                ->count('category_id'),
        ];

        return view('products.sub-products', compact('products', 'categories', 'stats'));
    }

    /**
     * Ürün Ekle - Form göster
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $branches = Branch::orderBy('name')->get();
        $serviceCategories = ServiceCategory::active()->orderBy('name')->get();
        $parentProducts = Product::whereHas('variants')
            ->orWhereNull('parent_id')
            ->orderBy('name')
            ->get(['id', 'name', 'barcode']);

        return view('products.create', compact('categories', 'branches', 'parentProducts', 'serviceCategories'));
    }

    /**
     * Ürün Kaydet
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'barcode' => 'nullable|string|max:50|unique:products,barcode',
            'category_id' => 'nullable|exists:categories,id',
            'service_category_id' => 'nullable|exists:service_categories,id',
            'unit' => 'nullable|string|max:50',
            'purchase_price' => 'nullable|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'vat_rate' => 'nullable|integer|min:0|max:100',
            'stock_quantity' => 'nullable|numeric|min:0',
            'critical_stock' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'is_service' => 'nullable|boolean',
            'image_url' => 'nullable|url|max:500',
            'additional_taxes' => 'nullable|array',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['is_service'] = $request->has('is_service');
        $validated['unit'] = $validated['unit'] ?? 'Adet';
        $validated['vat_rate'] = $validated['vat_rate'] ?? 20;

        // Ek vergileri JSON formatına dönüştür
        if (!empty($validated['additional_taxes'])) {
            $taxes = [];
            foreach ($validated['additional_taxes'] as $code => $taxRateId) {
                if ($taxRateId) {
                    $taxRate = \App\Models\TaxRate::find($taxRateId);
                    if ($taxRate) {
                        $taxes[] = [
                            'tax_rate_id' => $taxRate->id,
                            'code' => $taxRate->code,
                            'rate' => (float) $taxRate->rate,
                        ];
                    }
                }
            }
            $validated['additional_taxes'] = !empty($taxes) ? $taxes : null;
        }

        $product = Product::create($validated);

        return redirect()->route('products.show', $product)
            ->with('success', 'Ürün başarıyla oluşturuldu.');
    }

    /**
     * Ürün Düzenle - Form göster
     */
    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        $branches = Branch::orderBy('name')->get();
        $serviceCategories = ServiceCategory::where('is_active', true)->orderBy('name')->get();

        return view('products.edit', compact('product', 'categories', 'branches', 'serviceCategories'));
    }

    /**
     * Ürün Güncelle
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'barcode' => 'nullable|string|max:50|unique:products,barcode,' . $product->id,
            'category_id' => 'nullable|exists:categories,id',
            'service_category_id' => 'nullable|exists:service_categories,id',
            'unit' => 'nullable|string|max:50',
            'purchase_price' => 'nullable|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'vat_rate' => 'nullable|integer|min:0|max:100',
            'stock_quantity' => 'nullable|numeric|min:0',
            'critical_stock' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'is_service' => 'nullable|boolean',
            'image_url' => 'nullable|url|max:500',
            'additional_taxes' => 'nullable|array',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['is_service'] = $request->has('is_service');

        // Ek vergileri JSON formatına dönüştür
        if (!empty($validated['additional_taxes'])) {
            $taxes = [];
            foreach ($validated['additional_taxes'] as $code => $taxRateId) {
                if ($taxRateId) {
                    $taxRate = \App\Models\TaxRate::find($taxRateId);
                    if ($taxRate) {
                        $taxes[] = [
                            'tax_rate_id' => $taxRate->id,
                            'code' => $taxRate->code,
                            'rate' => (float) $taxRate->rate,
                        ];
                    }
                }
            }
            $validated['additional_taxes'] = !empty($taxes) ? $taxes : null;
        } else {
            $validated['additional_taxes'] = null;
        }

        $product->update($validated);

        return redirect()->route('products.show', $product)
            ->with('success', 'Ürün başarıyla güncellendi.');
    }

    /**
     * Varyantlı Ürün Ekle - Form göster
     */
    public function createVariant()
    {
        $categories = Category::orderBy('name')->get();
        $parentProducts = Product::whereNull('parent_id')
            ->orderBy('name')
            ->get(['id', 'name', 'barcode']);

        $variantTypes = Product::whereNotNull('variant_type')
            ->where('variant_type', '!=', '')
            ->distinct('variant_type')
            ->pluck('variant_type');

        return view('products.create-variant', compact('categories', 'parentProducts', 'variantTypes'));
    }

    /**
     * Varyantlı Ürün Kaydet
     */
    public function storeVariant(Request $request)
    {
        $validated = $request->validate([
            'parent_id' => 'required|exists:products,id',
            'variant_type' => 'required|string|max:100',
            'variants' => 'required|array|min:1',
            'variants.*.name' => 'required|string|max:255',
            'variants.*.barcode' => 'nullable|string|max:50',
            'variants.*.sale_price' => 'nullable|numeric|min:0',
            'variants.*.purchase_price' => 'nullable|numeric|min:0',
            'variants.*.stock_quantity' => 'nullable|numeric|min:0',
        ]);

        $parent = Product::findOrFail($validated['parent_id']);
        $created = 0;

        foreach ($validated['variants'] as $variantData) {
            Product::create([
                'name' => $variantData['name'],
                'barcode' => $variantData['barcode'] ?? null,
                'category_id' => $parent->category_id,
                'parent_id' => $parent->id,
                'variant_type' => $validated['variant_type'],
                'unit' => $parent->unit,
                'purchase_price' => $variantData['purchase_price'] ?? $parent->purchase_price,
                'sale_price' => $variantData['sale_price'] ?? $parent->sale_price,
                'vat_rate' => $parent->vat_rate,
                'stock_quantity' => $variantData['stock_quantity'] ?? 0,
                'critical_stock' => $parent->critical_stock,
                'is_active' => true,
            ]);
            $created++;
        }

        return redirect()->route('products.variants')
            ->with('success', "{$created} varyant başarıyla oluşturuldu.");
    }

    /**
     * Etiket Tasarla & Üret
     */
    public function labelDesigner(Request $request)
    {
        $products = collect();
        $selectedProducts = collect();

        if ($search = $request->get('search')) {
            $products = Product::with('category')
                ->where('name', 'like', "%{$search}%")
                ->orWhere('barcode', 'like', "%{$search}%")
                ->limit(50)->get();
        }

        // Seçilen ürün ID'leri
        if ($ids = $request->get('selected')) {
            $idArray = is_array($ids) ? $ids : explode(',', $ids);
            $selectedProducts = Product::whereIn('id', $idArray)->get();
        }

        return view('products.label-designer', compact('products', 'selectedProducts'));
    }

    /**
     * Barkodlu Terazi Çıktısı
     */
    public function scaleBarcode(Request $request)
    {
        $query = Product::query();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        // Sadece tartılabilir ürünler (kg, gr birim)
        if ($request->get('weighable')) {
            $query->where(function ($q) {
                $q->where('unit', 'like', '%kg%')
                  ->orWhere('unit', 'like', '%gr%')
                  ->orWhere('unit', 'like', '%Kg%')
                  ->orWhere('unit', 'like', '%Gram%');
            });
        }

        if ($categoryId = $request->get('category_id')) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->orderBy('name')->paginate(25)->appends($request->query());
        $categories = Category::orderBy('name')->get();

        $stats = [
            'total_products' => Product::count(),
            'weighable' => Product::where(function ($q) {
                $q->where('unit', 'like', '%kg%')
                  ->orWhere('unit', 'like', '%gr%')
                  ->orWhere('unit', 'like', '%Kg%')
                  ->orWhere('unit', 'like', '%Gram%');
            })->count(),
            'with_barcode' => Product::whereNotNull('barcode')->where('barcode', '!=', '')->count(),
        ];

        return view('products.scale-barcode', compact('products', 'categories', 'stats'));
    }

    // ══════════════════════════════════════════════════════════════
    // DOSYADAN ÜRÜN YÜKLEME (CSV / XLSX)
    // ══════════════════════════════════════════════════════════════

    public function importForm()
    {
        $categories = Category::orderBy('name')->get();
        return view('products.import', compact('categories'));
    }

    public function importTemplate()
    {
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="urun-sablonu.csv"',
        ];

        $columns = ['barkod', 'urun_adi', 'kategori_adi', 'birim', 'alis_fiyati', 'satis_fiyati', 'kdv_orani', 'stok_miktari', 'kritik_stok', 'aciklama'];

        $callback = function () use ($columns) {
            $out = fopen('php://output', 'w');
            // UTF-8 BOM — Excel'in Türkçe karakterleri doğru okuması için
            fputs($out, "\xEF\xBB\xBF");
            fputcsv($out, $columns, ';');
            // Örnek satır
            fputcsv($out, ['8690123456789', 'Örnek Ürün', 'Elektronik', 'adet', '50.00', '99.90', '20', '100', '10', 'Açıklama buraya'], ';');
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:10240',
        ], [
            'file.required' => 'Lütfen bir dosya seçin.',
            'file.mimes'    => 'Desteklenen formatlar: CSV, XLSX, XLS',
            'file.max'      => 'Dosya boyutu en fazla 10 MB olabilir.',
        ]);

        $file      = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());
        $rows      = [];

        // ── CSV okuma ──
        if (in_array($extension, ['csv', 'txt'])) {
            $handle = fopen($file->getRealPath(), 'r');
            // BOM temizle
            $bom = fread($handle, 3);
            if ($bom !== "\xEF\xBB\xBF") {
                rewind($handle);
            }
            // Ayırıcı otomatik tespit (noktalı virgül veya virgül)
            $firstLine = fgets($handle);
            rewind($handle);
            if ($bom === "\xEF\xBB\xBF") fread($handle, 3);
            $delimiter = substr_count($firstLine, ';') >= substr_count($firstLine, ',') ? ';' : ',';

            $header = null;
            while (($line = fgetcsv($handle, 0, $delimiter)) !== false) {
                if (!$header) { $header = array_map('trim', $line); continue; }
                if (count($line) < 2) continue;
                $rows[] = array_combine(array_slice($header, 0, count($line)), array_slice($line, 0, count($header)));
            }
            fclose($handle);
        }
        // ── XLSX okuma (PHPSpreadsheet yoksa ZipArchive ile basit DOM parse) ──
        elseif (in_array($extension, ['xlsx', 'xls'])) {
            try {
                $zip = new \ZipArchive();
                if ($zip->open($file->getRealPath()) !== true) {
                    return back()->withErrors(['file' => 'XLSX dosyası açılamadı.'])->withInput();
                }

                // Paylaşılan stringler
                $sharedStrings = [];
                $ssXml = $zip->getFromName('xl/sharedStrings.xml');
                if ($ssXml) {
                    $ss = new \SimpleXMLElement($ssXml);
                    foreach ($ss->si as $si) {
                        $sharedStrings[] = (string) ($si->t ?? implode('', array_map('strval', $si->r->t ?? [])));
                    }
                }

                // İlk sayfa
                $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
                $zip->close();

                if (!$sheetXml) {
                    return back()->withErrors(['file' => 'XLSX içinde veri sayfası bulunamadı.'])->withInput();
                }

                $sheet  = new \SimpleXMLElement($sheetXml);
                $header = null;
                foreach ($sheet->sheetData->row as $row) {
                    $rowData = [];
                    foreach ($row->c as $cell) {
                        $type  = (string) $cell['t'];
                        $value = (string) $cell->v;
                        if ($type === 's') {
                            $value = $sharedStrings[(int) $value] ?? '';
                        }
                        $rowData[] = $value;
                    }
                    if (!$header) { $header = array_map('trim', $rowData); continue; }
                    if (count(array_filter($rowData)) < 2) continue;
                    $rows[] = array_combine(array_slice($header, 0, count($rowData)), array_slice($rowData, 0, count($header)));
                }
            } catch (\Exception $e) {
                return back()->withErrors(['file' => 'XLSX dosyası işlenirken hata: ' . $e->getMessage()])->withInput();
            }
        }

        if (empty($rows)) {
            return back()->withErrors(['file' => 'Dosyada geçerli veri satırı bulunamadı.'])->withInput();
        }

        // ── Sütun eşleştirme (Türkçe/İngilizce başlıklar) ──
        $map = [
            'barcode'        => ['barkod', 'barcode', 'barkod_no'],
            'name'           => ['urun_adi', 'ad', 'name', 'ürün adı', 'urun adı', 'ürün_adı'],
            'category'       => ['kategori', 'kategori_adi', 'category', 'kategori adı'],
            'unit'           => ['birim', 'unit'],
            'purchase_price' => ['alis_fiyati', 'alis fiyati', 'alış fiyatı', 'purchase_price', 'maliyet'],
            'sale_price'     => ['satis_fiyati', 'satis fiyati', 'satış fiyatı', 'sale_price', 'fiyat'],
            'vat_rate'       => ['kdv_orani', 'kdv', 'vat_rate', 'kdv oranı'],
            'stock_quantity' => ['stok_miktari', 'stok', 'stock_quantity', 'miktar', 'stok miktarı'],
            'critical_stock' => ['kritik_stok', 'kritik stok', 'critical_stock'],
            'description'    => ['aciklama', 'açıklama', 'description', 'notlar'],
        ];

        $colMap = [];
        if (!empty($rows[0])) {
            foreach (array_keys($rows[0]) as $col) {
                $colLower = mb_strtolower(trim($col));
                foreach ($map as $field => $aliases) {
                    if (in_array($colLower, $aliases)) {
                        $colMap[$field] = $col;
                        break;
                    }
                }
            }
        }

        if (empty($colMap['name'])) {
            return back()->withErrors(['file' => '"urun_adi" veya "name" sütunu bulunamadı. Şablonu indirerek doğru format kullanın.'])->withInput();
        }

        // ── Kategori önbelleği ──
        $categoryCache = Category::pluck('id', 'name')->toArray();
        $updateExisting = $request->boolean('update_existing');

        $imported = 0;
        $updated  = 0;
        $skipped  = 0;
        $errors   = [];

        foreach ($rows as $i => $row) {
            $rowNum = $i + 2; // başlık = 1
            $name   = trim($row[$colMap['name']] ?? '');
            if (!$name) { $skipped++; continue; }

            $barcode = isset($colMap['barcode']) ? trim($row[$colMap['barcode']] ?? '') : null;

            // Kategori ID bul / oluştur
            $categoryId = null;
            if (isset($colMap['category'])) {
                $catName = trim($row[$colMap['category']] ?? '');
                if ($catName) {
                    if (!isset($categoryCache[$catName])) {
                        $cat = Category::create(['name' => $catName, 'type' => 'product']);
                        $categoryCache[$catName] = $cat->id;
                    }
                    $categoryId = $categoryCache[$catName];
                }
            }

            $data = [
                'name'           => $name,
                'barcode'        => $barcode ?: null,
                'category_id'    => $categoryId,
                'unit'           => trim($row[$colMap['unit']] ?? 'adet') ?: 'adet',
                'purchase_price' => (float) str_replace(',', '.', $row[$colMap['purchase_price']] ?? 0),
                'sale_price'     => (float) str_replace(',', '.', $row[$colMap['sale_price']] ?? 0),
                'vat_rate'       => (int) ($row[$colMap['vat_rate']] ?? 20),
                'stock_quantity' => (float) str_replace(',', '.', $row[$colMap['stock_quantity']] ?? 0),
                'critical_stock' => (float) str_replace(',', '.', $row[$colMap['critical_stock']] ?? 5),
                'description'    => trim($row[$colMap['description']] ?? '') ?: null,
                'is_active'      => true,
            ];

            try {
                // Barkod ile mevcut ürünü ara
                $existing = $barcode ? Product::where('barcode', $barcode)->first() : null;

                if ($existing) {
                    if ($updateExisting) {
                        $existing->update($data);
                        $updated++;
                    } else {
                        $skipped++;
                    }
                } else {
                    Product::create($data);
                    $imported++;
                }
            } catch (\Exception $e) {
                $errors[] = "Satır {$rowNum} ({$name}): " . $e->getMessage();
            }
        }

        $msg = "İşlem tamamlandı: {$imported} yeni ürün eklendi";
        if ($updated)  $msg .= ", {$updated} ürün güncellendi";
        if ($skipped)  $msg .= ", {$skipped} satır atlandı";
        if (!empty($errors)) $msg .= ", " . count($errors) . " hata oluştu";

        return redirect()->route('products.index')
            ->with('success', $msg)
            ->with('import_errors', $errors);
    }
}
