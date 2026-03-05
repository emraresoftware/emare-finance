<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MobileController extends Controller
{
    /**
     * Mobil Ana Menü
     */
    public function index()
    {
        $stats = [
            'total_products' => Product::where('is_active', true)->count(),
            'low_stock'      => Product::where('is_active', true)
                ->whereColumn('stock_quantity', '<=', 'critical_stock')
                ->where('critical_stock', '>', 0)
                ->count(),
            'today_sales'    => Sale::whereDate('sold_at', today())->count(),
            'today_revenue'  => Sale::whereDate('sold_at', today())->sum('grand_total'),
        ];

        return view('mobile.index', compact('stats'));
    }

    // ══════════════════════════════════════════════════════════════
    // KAMERADAN ÜRÜN EKLEME
    // ══════════════════════════════════════════════════════════════

    /**
     * Kamera ile ürün ekleme sayfası
     */
    public function cameraAdd()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('mobile.camera-add', compact('categories'));
    }

    /**
     * Fotoğraf yükleme (AJAX)
     */
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|max:10240', // max 10MB
        ]);

        $file = $request->file('photo');
        $filename = 'products/' . Str::uuid() . '.' . $file->getClientOriginalExtension();

        Storage::disk('public')->put($filename, file_get_contents($file));

        $url = Storage::disk('public')->url($filename);

        return response()->json([
            'success' => true,
            'url'     => $url,
            'path'    => $filename,
        ]);
    }

    /**
     * Fotoğraflı ürün kaydet
     */
    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'barcode'        => 'nullable|string|max:50|unique:products,barcode',
            'category_id'    => 'nullable|exists:categories,id',
            'unit'           => 'nullable|string|max:50',
            'purchase_price' => 'nullable|numeric|min:0',
            'sale_price'     => 'nullable|numeric|min:0',
            'vat_rate'       => 'nullable|integer|min:0|max:100',
            'stock_quantity' => 'nullable|numeric|min:0',
            'critical_stock' => 'nullable|numeric|min:0',
            'description'    => 'nullable|string',
            'image_path'     => 'nullable|string',
        ]);

        $validated['unit'] = $validated['unit'] ?? 'Adet';
        $validated['vat_rate'] = $validated['vat_rate'] ?? 20;
        $validated['is_active'] = true;

        // Fotoğraf path'i → image_url olarak kaydet
        if (!empty($validated['image_path'])) {
            $validated['image_url'] = Storage::disk('public')->url($validated['image_path']);
        }
        unset($validated['image_path']);

        $product = Product::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'product' => $product,
                'message' => 'Ürün başarıyla oluşturuldu.',
            ]);
        }

        return redirect()->route('mobile.index')
            ->with('success', "'{$product->name}' başarıyla eklendi!");
    }

    // ══════════════════════════════════════════════════════════════
    // BARKOD TARAMA
    // ══════════════════════════════════════════════════════════════

    /**
     * Barkod tarama sayfası
     */
    public function barcodeScan()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('mobile.barcode-scan', compact('categories'));
    }

    /**
     * Barkod ile ürün ara (AJAX)
     */
    public function barcodeSearch(Request $request)
    {
        $barcode = $request->input('barcode');

        $product = Product::where('barcode', $barcode)->first();

        if ($product) {
            return response()->json([
                'found'   => true,
                'product' => [
                    'id'             => $product->id,
                    'name'           => $product->name,
                    'barcode'        => $product->barcode,
                    'sale_price'     => $product->sale_price,
                    'purchase_price' => $product->purchase_price,
                    'stock_quantity' => $product->stock_quantity,
                    'image_url'      => $product->image_url,
                    'category'       => $product->category?->name,
                    'unit'           => $product->unit,
                    'vat_rate'       => $product->vat_rate,
                ],
            ]);
        }

        return response()->json([
            'found'   => false,
            'barcode' => $barcode,
            'message' => 'Ürün bulunamadı. Yeni ürün ekleyebilirsiniz.',
        ]);
    }

    // ══════════════════════════════════════════════════════════════
    // HIZLI SİPARİŞ / SATIŞ
    // ══════════════════════════════════════════════════════════════

    /**
     * Hızlı sipariş sayfası
     */
    public function quickOrder()
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        $products = Product::where('is_active', true)
            ->select('id', 'name', 'barcode', 'sale_price', 'image_url', 'category_id', 'unit', 'vat_rate', 'stock_quantity')
            ->orderBy('name')
            ->get();
        $customers = Customer::orderBy('name')->get(['id', 'name', 'phone']);

        return view('mobile.quick-order', compact('categories', 'products', 'customers'));
    }

    /**
     * Ürün ara (AJAX) — quick order'da arama
     */
    public function searchProducts(Request $request)
    {
        $query = $request->input('q', '');

        $products = Product::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('barcode', 'like', "%{$query}%");
            })
            ->select('id', 'name', 'barcode', 'sale_price', 'image_url', 'category_id', 'unit', 'vat_rate', 'stock_quantity')
            ->limit(20)
            ->get();

        return response()->json($products);
    }

    /**
     * Siparişi kaydet
     */
    public function storeOrder(Request $request)
    {
        $request->validate([
            'items'          => 'required|array|min:1',
            'items.*.id'     => 'required|exists:products,id',
            'items.*.qty'    => 'required|numeric|min:0.01',
            'items.*.price'  => 'required|numeric|min:0',
            'customer_id'    => 'nullable|exists:customers,id',
            'payment_method' => 'required|in:cash,card,mixed,credit',
            'notes'          => 'nullable|string|max:500',
            'discount'       => 'nullable|numeric|min:0',
        ]);

        $subtotal = 0;
        $vatTotal = 0;
        $totalItems = 0;
        $saleItems = [];

        foreach ($request->items as $item) {
            $product = Product::find($item['id']);
            if (!$product) continue;

            $qty = (float) $item['qty'];
            $unitPrice = (float) $item['price'];
            $lineTotal = $qty * $unitPrice;
            $vatRate = $product->vat_rate ?? 20;
            $vatAmount = $lineTotal * $vatRate / (100 + $vatRate);

            $saleItems[] = [
                'product_id'   => $product->id,
                'product_name' => $product->name,
                'barcode'      => $product->barcode,
                'quantity'     => $qty,
                'unit_price'   => $unitPrice,
                'discount'     => 0,
                'vat_rate'     => $vatRate,
                'vat_amount'   => round($vatAmount, 2),
                'total'        => round($lineTotal, 2),
            ];

            $subtotal += $lineTotal;
            $vatTotal += $vatAmount;
            $totalItems += $qty;

            // Stok düş
            $product->decrement('stock_quantity', $qty);
        }

        $discount = (float) ($request->discount ?? 0);
        $grandTotal = $subtotal - $discount;

        $sale = Sale::create([
            'receipt_no'     => 'MB-' . date('Ymd') . '-' . str_pad(Sale::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT),
            'branch_id'      => auth()->user()->branch_id,
            'customer_id'    => $request->customer_id,
            'user_id'        => auth()->id(),
            'payment_method' => $request->payment_method,
            'subtotal'       => round($subtotal, 2),
            'vat_total'      => round($vatTotal, 2),
            'discount_total' => round($discount, 2),
            'discount'       => round($discount, 2),
            'grand_total'    => round($grandTotal, 2),
            'cash_amount'    => $request->payment_method === 'cash' ? round($grandTotal, 2) : 0,
            'card_amount'    => $request->payment_method === 'card' ? round($grandTotal, 2) : 0,
            'total_items'    => $totalItems,
            'status'         => 'completed',
            'notes'          => $request->notes,
            'application'    => 'mobile',
            'sold_at'        => now(),
        ]);

        foreach ($saleItems as $item) {
            $item['sale_id'] = $sale->id;
            SaleItem::create($item);
        }

        return response()->json([
            'success'    => true,
            'sale_id'    => $sale->id,
            'receipt_no' => $sale->receipt_no,
            'total'      => $sale->grand_total,
            'message'    => 'Sipariş başarıyla oluşturuldu!',
        ]);
    }

    // ══════════════════════════════════════════════════════════════
    // ÜRÜN DETAY (Mobil)
    // ══════════════════════════════════════════════════════════════

    /**
     * Ürün detay (mobil görünüm)
     */
    public function productDetail(Product $product)
    {
        $product->load('category', 'saleItems');

        return view('mobile.product-detail', compact('product'));
    }
}
