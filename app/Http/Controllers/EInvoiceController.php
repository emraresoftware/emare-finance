<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\EInvoice;
use App\Models\EInvoiceItem;
use App\Models\EInvoiceSetting;
use App\Models\Product;
use Illuminate\Http\Request;

class EInvoiceController extends Controller
{
    /**
     * E-Fatura ana sayfası
     */
    public function index()
    {
        return view('einvoices.index');
    }

    /**
     * Giden E-Faturalar
     */
    public function outgoing(Request $request)
    {
        $query = EInvoice::outgoing()->with('customer');

        if ($request->filled('start_date')) {
            $query->whereDate('invoice_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('invoice_date', '<=', $request->end_date);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('invoice_no', 'like', "%{$s}%")
                  ->orWhere('receiver_name', 'like', "%{$s}%")
                  ->orWhere('receiver_tax_number', 'like', "%{$s}%");
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $invoices = $query->latest('invoice_date')->paginate(25)->withQueryString();

        $stats = [
            'total' => EInvoice::outgoing()->count(),
            'draft' => EInvoice::outgoing()->where('status', 'draft')->count(),
            'sent' => EInvoice::outgoing()->where('status', 'sent')->count(),
            'total_amount' => EInvoice::outgoing()->where('status', '!=', 'cancelled')->sum('grand_total'),
        ];

        return view('einvoices.outgoing', compact('invoices', 'stats'));
    }

    /**
     * Gelen E-Faturalar
     */
    public function incoming(Request $request)
    {
        $query = EInvoice::incoming()->with('customer');

        if ($request->filled('start_date')) {
            $query->whereDate('invoice_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('invoice_date', '<=', $request->end_date);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('invoice_no', 'like', "%{$s}%")
                  ->orWhere('receiver_name', 'like', "%{$s}%");
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $invoices = $query->latest('invoice_date')->paginate(25)->withQueryString();

        $stats = [
            'total' => EInvoice::incoming()->count(),
            'accepted' => EInvoice::incoming()->where('status', 'accepted')->count(),
            'rejected' => EInvoice::incoming()->where('status', 'rejected')->count(),
            'total_amount' => EInvoice::incoming()->where('status', 'accepted')->sum('grand_total'),
        ];

        return view('einvoices.incoming', compact('invoices', 'stats'));
    }

    /**
     * Yeni E-Fatura Oluştur (Form)
     */
    public function create(Request $request)
    {
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        $branches = Branch::orderBy('name')->get();
        $products = Product::whereNull('parent_id')->where('is_active', true)->orderBy('name')->get();
        $settings = EInvoiceSetting::current();

        // Satıştan fatura oluşturma
        $sale = null;
        if ($request->filled('sale_id')) {
            $sale = \App\Models\Sale::with('items.product', 'customer')->find($request->sale_id);
        }

        return view('einvoices.create', compact('customers', 'branches', 'products', 'settings', 'sale'));
    }

    /**
     * E-Fatura kaydet
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'direction' => 'required|in:outgoing,incoming',
            'type' => 'required|in:invoice,return,withholding,exception,special',
            'scenario' => 'required|in:basic,commercial,export',
            'customer_id' => 'nullable|exists:customers,id',
            'receiver_name' => 'required|string|max:255',
            'receiver_tax_number' => 'nullable|string|max:20',
            'receiver_tax_office' => 'nullable|string|max:255',
            'receiver_address' => 'nullable|string',
            'branch_id' => 'nullable|exists:branches,id',
            'sale_id' => 'nullable|exists:sales,id',
            'currency' => 'nullable|string|max:5',
            'exchange_rate' => 'nullable|numeric|min:0',
            'vat_rate' => 'nullable|integer|in:0,1,10,20',
            'invoice_date' => 'required|date',
            'notes' => 'nullable|string',
            'payment_method' => 'nullable|string|max:50',
            'items' => 'required|array|min:1',
            'items.*.product_name' => 'required|string|max:255',
            'items.*.product_code' => 'nullable|string|max:100',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.vat_rate' => 'nullable|integer|in:0,1,10,20',
            'items.*.product_id' => 'nullable|exists:products,id',
        ]);

        // Hesaplamalar
        $subtotal = 0;
        $vatTotal = 0;
        $discountTotal = 0;

        foreach ($validated['items'] as &$item) {
            $item['discount'] = $item['discount'] ?? 0;
            $item['vat_rate'] = $item['vat_rate'] ?? ($validated['vat_rate'] ?? 20);
            $item['unit'] = $item['unit'] ?? 'Adet';
            $lineTotal = ($item['quantity'] * $item['unit_price']) - $item['discount'];
            $item['vat_amount'] = round($lineTotal * $item['vat_rate'] / 100, 2);
            $item['total'] = round($lineTotal + $item['vat_amount'], 2);
            $subtotal += ($item['quantity'] * $item['unit_price']);
            $discountTotal += $item['discount'];
            $vatTotal += $item['vat_amount'];
        }

        // Fatura numarası oluştur
        $settings = EInvoiceSetting::current();
        $prefix = $settings->invoice_prefix ?? 'EMR';
        $counter = $settings->invoice_counter;
        $invoiceNo = $prefix . str_pad($counter, 9, '0', STR_PAD_LEFT);
        $settings->increment('invoice_counter');

        $invoice = EInvoice::create([
            'invoice_no' => $invoiceNo,
            'direction' => $validated['direction'],
            'type' => $validated['type'],
            'scenario' => $validated['scenario'],
            'status' => 'draft',
            'customer_id' => $validated['customer_id'],
            'receiver_name' => $validated['receiver_name'],
            'receiver_tax_number' => $validated['receiver_tax_number'] ?? null,
            'receiver_tax_office' => $validated['receiver_tax_office'] ?? null,
            'receiver_address' => $validated['receiver_address'] ?? null,
            'branch_id' => $validated['branch_id'] ?? null,
            'sale_id' => $validated['sale_id'] ?? null,
            'currency' => $validated['currency'] ?? 'TRY',
            'exchange_rate' => $validated['exchange_rate'] ?? 1,
            'vat_rate' => $validated['vat_rate'] ?? 20,
            'invoice_date' => $validated['invoice_date'],
            'notes' => $validated['notes'] ?? null,
            'payment_method' => $validated['payment_method'] ?? null,
            'subtotal' => $subtotal,
            'vat_total' => $vatTotal,
            'discount_total' => $discountTotal,
            'grand_total' => $subtotal - $discountTotal + $vatTotal,
        ]);

        foreach ($validated['items'] as $item) {
            $invoice->items()->create($item);
        }

        return redirect()->route('einvoices.outgoing')->with('success', "E-Fatura {$invoiceNo} başarıyla oluşturuldu.");
    }

    /**
     * E-Fatura detay
     */
    public function show(EInvoice $einvoice)
    {
        $einvoice->load('items.product', 'customer', 'branch', 'sale');
        return view('einvoices.show', compact('einvoice'));
    }

    /**
     * E-Fatura Ayarları
     */
    public function settings()
    {
        $settings = EInvoiceSetting::current();
        return view('einvoices.settings', compact('settings'));
    }

    /**
     * E-Fatura Ayarları güncelle
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:20',
            'tax_office' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|string|email|max:255',
            'web' => 'nullable|string|max:255',
            'integrator' => 'nullable|string|max:100',
            'api_key' => 'nullable|string|max:500',
            'api_secret' => 'nullable|string|max:500',
            'sender_alias' => 'nullable|string|max:255',
            'receiver_alias' => 'nullable|string|max:255',
            'auto_send' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'default_scenario' => 'nullable|in:basic,commercial,export',
            'default_currency' => 'nullable|string|max:5',
            'default_vat_rate' => 'nullable|integer|in:0,1,10,20',
            'invoice_prefix' => 'nullable|string|max:10',
        ]);

        $validated['auto_send'] = $request->has('auto_send');
        $validated['is_active'] = $request->has('is_active');

        // Boş bırakılan API key/secret mevcut değeri korusun
        if (empty($validated['api_key'])) {
            unset($validated['api_key']);
        }
        if (empty($validated['api_secret'])) {
            unset($validated['api_secret']);
        }

        $settings = EInvoiceSetting::current();
        $settings->update($validated);

        return redirect()->route('einvoices.settings')->with('success', 'E-Fatura ayarları başarıyla güncellendi.');
    }
}
