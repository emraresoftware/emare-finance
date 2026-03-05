<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\EInvoice;
use App\Models\EInvoiceItem;
use App\Models\EInvoiceSetting;
use App\Models\Product;
use App\Models\PurchaseInvoice;
use Illuminate\Http\Request;

class FaturaController extends Controller
{
    // ══════════════════════════════════════════════════════════════
    // DASHBOARD — Ana sayfa
    // ══════════════════════════════════════════════════════════════
    public function index()
    {
        $stats = [
            // Giden faturalar
            'outgoing_total'  => EInvoice::outgoing()->fatura()->notEarsiv()->count(),
            'outgoing_amount' => EInvoice::outgoing()->fatura()->notEarsiv()->where('status', '!=', 'cancelled')->sum('grand_total'),
            'outgoing_draft'  => EInvoice::outgoing()->fatura()->notEarsiv()->where('status', 'draft')->count(),
            'outgoing_sent'   => EInvoice::outgoing()->fatura()->notEarsiv()->where('status', 'sent')->count(),
            // Gelen faturalar
            'incoming_total'  => EInvoice::incoming()->fatura()->notEarsiv()->count() + PurchaseInvoice::count(),
            'incoming_amount' => EInvoice::incoming()->fatura()->notEarsiv()->where('status', '!=', 'cancelled')->sum('grand_total') + PurchaseInvoice::sum('total_amount'),
            // İrsaliyeler
            'waybill_total'   => EInvoice::irsaliye()->count(),
            'waybill_amount'  => EInvoice::irsaliye()->where('status', '!=', 'cancelled')->sum('grand_total'),
            // e-Arşiv
            'earsiv_total'    => EInvoice::earsiv()->count(),
            'earsiv_amount'   => EInvoice::earsiv()->where('status', '!=', 'cancelled')->sum('grand_total'),
            'earsiv_draft'    => EInvoice::earsiv()->where('status', 'draft')->count(),
            'earsiv_internet' => EInvoice::earsiv()->where('is_internet_sale', true)->count(),
            // Bu ay
            'this_month_out'  => EInvoice::outgoing()->fatura()->whereMonth('invoice_date', now()->month)->whereYear('invoice_date', now()->year)->sum('grand_total'),
            'this_month_in'   => EInvoice::incoming()->fatura()->whereMonth('invoice_date', now()->month)->whereYear('invoice_date', now()->year)->sum('grand_total')
                               + PurchaseInvoice::whereMonth('invoice_date', now()->month)->whereYear('invoice_date', now()->year)->sum('total_amount'),
        ];

        // Son 10 fatura/irsaliye
        $recentInvoices = EInvoice::with('customer')
            ->latest('invoice_date')
            ->take(10)
            ->get();

        // Son 10 alış faturası
        $recentPurchases = PurchaseInvoice::with('firm')
            ->latest('invoice_date')
            ->take(5)
            ->get();

        return view('faturalar.index', compact('stats', 'recentInvoices', 'recentPurchases'));
    }

    // ══════════════════════════════════════════════════════════════
    // GİDEN FATURALAR
    // ══════════════════════════════════════════════════════════════
    public function outgoing(Request $request)
    {
        $query = EInvoice::outgoing()->fatura()->with('customer');

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
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $invoices = $query->latest('invoice_date')->paginate(25)->withQueryString();

        $stats = [
            'total'        => EInvoice::outgoing()->fatura()->count(),
            'draft'        => EInvoice::outgoing()->fatura()->where('status', 'draft')->count(),
            'sent'         => EInvoice::outgoing()->fatura()->where('status', 'sent')->count(),
            'total_amount' => EInvoice::outgoing()->fatura()->where('status', '!=', 'cancelled')->sum('grand_total'),
        ];

        return view('faturalar.outgoing', compact('invoices', 'stats'));
    }

    // ══════════════════════════════════════════════════════════════
    // GELEN FATURALAR (E-Fatura + Alış Faturaları birleşik)
    // ══════════════════════════════════════════════════════════════
    public function incoming(Request $request)
    {
        $tab = $request->input('tab', 'all');

        // E-Fatura gelen
        $eQuery = EInvoice::incoming()->fatura()->with('customer');
        // Alış faturaları
        $pQuery = PurchaseInvoice::with(['firm', 'branch'])->withCount('items');

        // Ortak filtreler
        if ($request->filled('start_date')) {
            $eQuery->whereDate('invoice_date', '>=', $request->start_date);
            $pQuery->whereDate('invoice_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $eQuery->whereDate('invoice_date', '<=', $request->end_date);
            $pQuery->whereDate('invoice_date', '<=', $request->end_date);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $eQuery->where(function ($q) use ($s) {
                $q->where('invoice_no', 'like', "%{$s}%")
                  ->orWhere('receiver_name', 'like', "%{$s}%");
            });
            $pQuery->where(function ($q) use ($s) {
                $q->where('invoice_no', 'like', "%{$s}%")
                  ->orWhereHas('firm', fn($q2) => $q2->where('name', 'like', "%{$s}%"));
            });
        }

        // Tab'a göre filtrele
        if ($tab === 'einvoice') {
            $eInvoices = $eQuery->latest('invoice_date')->paginate(25, ['*'], 'page')->withQueryString();
            $purchases = collect();
            $purchasePaginator = null;
        } elseif ($tab === 'purchase') {
            $eInvoices = collect();
            $purchases = $pQuery->latest('invoice_date')->paginate(25, ['*'], 'page')->withQueryString();
            $purchasePaginator = $purchases;
        } else {
            $eInvoices = $eQuery->latest('invoice_date')->paginate(15, ['*'], 'epage')->withQueryString();
            $purchases = $pQuery->latest('invoice_date')->paginate(15, ['*'], 'ppage')->withQueryString();
            $purchasePaginator = $purchases;
        }

        $stats = [
            'einvoice_total'  => EInvoice::incoming()->fatura()->count(),
            'einvoice_amount' => EInvoice::incoming()->fatura()->where('status', '!=', 'cancelled')->sum('grand_total'),
            'purchase_total'  => PurchaseInvoice::count(),
            'purchase_amount' => PurchaseInvoice::sum('total_amount'),
        ];

        return view('faturalar.incoming', compact('eInvoices', 'purchases', 'purchasePaginator', 'stats', 'tab'));
    }

    // ══════════════════════════════════════════════════════════════
    // İRSALİYELER
    // ══════════════════════════════════════════════════════════════
    public function waybills(Request $request)
    {
        $query = EInvoice::irsaliye()->with('customer');

        if ($request->filled('direction')) {
            $query->where('direction', $request->direction);
        }
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
                  ->orWhere('waybill_no', 'like', "%{$s}%")
                  ->orWhere('receiver_name', 'like', "%{$s}%")
                  ->orWhere('tracking_no', 'like', "%{$s}%");
            });
        }

        $waybills = $query->latest('invoice_date')->paginate(25)->withQueryString();

        $stats = [
            'total'          => EInvoice::irsaliye()->count(),
            'outgoing_count' => EInvoice::irsaliye()->outgoing()->count(),
            'incoming_count' => EInvoice::irsaliye()->incoming()->count(),
            'total_amount'   => EInvoice::irsaliye()->where('status', '!=', 'cancelled')->sum('grand_total'),
        ];

        return view('faturalar.waybills', compact('waybills', 'stats'));
    }

    // ══════════════════════════════════════════════════════════════
    // e-ARŞİV FATURALAR
    // ══════════════════════════════════════════════════════════════
    public function earsiv(Request $request)
    {
        $query = EInvoice::earsiv()->with('customer');

        if ($request->filled('direction')) {
            $query->where('direction', $request->direction);
        }
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
                  ->orWhere('receiver_tax_number', 'like', "%{$s}%")
                  ->orWhere('tc_kimlik_no', 'like', "%{$s}%")
                  ->orWhere('earsiv_report_no', 'like', "%{$s}%");
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('recipient_type')) {
            $query->where('recipient_type', $request->recipient_type);
        }
        if ($request->has('internet_sale')) {
            $query->where('is_internet_sale', true);
        }

        $invoices = $query->latest('invoice_date')->paginate(25)->withQueryString();

        $stats = [
            'total'             => EInvoice::earsiv()->count(),
            'draft'             => EInvoice::earsiv()->where('status', 'draft')->count(),
            'sent'              => EInvoice::earsiv()->where('status', 'sent')->count(),
            'total_amount'      => EInvoice::earsiv()->where('status', '!=', 'cancelled')->sum('grand_total'),
            'individual_count'  => EInvoice::earsiv()->where('recipient_type', 'individual')->count(),
            'corporate_count'   => EInvoice::earsiv()->where('recipient_type', 'corporate')->count(),
            'internet_count'    => EInvoice::earsiv()->where('is_internet_sale', true)->count(),
        ];

        return view('faturalar.earsiv', compact('invoices', 'stats'));
    }

    // ══════════════════════════════════════════════════════════════
    // YENİ FATURA KES
    // ══════════════════════════════════════════════════════════════
    public function create(Request $request)
    {
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        $branches  = Branch::orderBy('name')->get();
        $products  = Product::whereNull('parent_id')->where('is_active', true)->orderBy('name')->get();
        $settings  = EInvoiceSetting::current();

        // Satıştan fatura oluşturma
        $sale = null;
        if ($request->filled('sale_id')) {
            $sale = \App\Models\Sale::with('items.product', 'customer')->find($request->sale_id);
        }

        $documentType = $request->input('document_type', 'fatura');

        return view('faturalar.create', compact('customers', 'branches', 'products', 'settings', 'sale', 'documentType'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'document_type'       => 'required|in:fatura,irsaliye',
            'direction'           => 'required|in:outgoing,incoming',
            'type'                => 'required|string',
            'scenario'            => 'required|in:basic,commercial,export,e_arsiv',
            'customer_id'         => 'nullable|exists:customers,id',
            'receiver_name'       => 'required|string|max:255',
            'receiver_tax_number' => 'nullable|string|max:20',
            'tc_kimlik_no'        => 'nullable|string|max:11',
            'receiver_tax_office' => 'nullable|string|max:255',
            'receiver_address'    => 'nullable|string',
            'delivery_address'    => 'nullable|string',
            'recipient_type'      => 'nullable|in:individual,corporate',
            'is_internet_sale'    => 'nullable|boolean',
            'internet_sale_platform' => 'nullable|string|max:255',
            'internet_sale_url'   => 'nullable|string|max:500',
            'branch_id'           => 'nullable|exists:branches,id',
            'sale_id'             => 'nullable|exists:sales,id',
            'currency'            => 'nullable|string|max:5',
            'exchange_rate'       => 'nullable|numeric|min:0',
            'vat_rate'            => 'nullable|integer|in:0,1,10,20',
            'invoice_date'        => 'required|date',
            'payment_date'        => 'nullable|date',
            'payment_platform'    => 'nullable|string|max:100',
            'shipment_date'       => 'nullable|date',
            'waybill_no'          => 'nullable|string|max:50',
            'vehicle_plate'       => 'nullable|string|max:20',
            'driver_name'         => 'nullable|string|max:100',
            'driver_tc'           => 'nullable|string|max:11',
            'shipping_company'    => 'nullable|string|max:100',
            'tracking_no'         => 'nullable|string|max:100',
            'notes'               => 'nullable|string',
            'payment_method'      => 'nullable|string|max:50',
            'items'               => 'required|array|min:1',
            'items.*.product_name'  => 'required|string|max:255',
            'items.*.product_code'  => 'nullable|string|max:100',
            'items.*.unit'          => 'nullable|string|max:50',
            'items.*.quantity'      => 'required|numeric|min:0.001',
            'items.*.unit_price'    => 'required|numeric|min:0',
            'items.*.discount'      => 'nullable|numeric|min:0',
            'items.*.vat_rate'      => 'nullable|integer|in:0,1,10,20',
            'items.*.product_id'    => 'nullable|exists:products,id',
        ]);

        // Hesaplamalar
        $subtotal = 0;
        $vatTotal = 0;
        $discountTotal = 0;

        foreach ($validated['items'] as &$item) {
            $item['discount'] = $item['discount'] ?? 0;
            $item['vat_rate'] = $item['vat_rate'] ?? ($validated['vat_rate'] ?? 20);
            $item['unit']     = $item['unit'] ?? 'Adet';
            $lineTotal = ($item['quantity'] * $item['unit_price']) - $item['discount'];
            $item['vat_amount'] = round($lineTotal * $item['vat_rate'] / 100, 2);
            $item['total']      = round($lineTotal + $item['vat_amount'], 2);
            $subtotal      += ($item['quantity'] * $item['unit_price']);
            $discountTotal += $item['discount'];
            $vatTotal      += $item['vat_amount'];
        }

        // Numara oluştur
        $settings = EInvoiceSetting::current();
        $prefix   = $settings->invoice_prefix ?? 'EMR';
        $counter  = $settings->invoice_counter;
        $invoiceNo = $prefix . str_pad($counter, 9, '0', STR_PAD_LEFT);
        $settings->increment('invoice_counter');

        $invoice = EInvoice::create([
            'invoice_no'          => $invoiceNo,
            'document_type'       => $validated['document_type'],
            'direction'           => $validated['direction'],
            'type'                => $validated['type'],
            'scenario'            => $validated['scenario'],
            'status'              => 'draft',
            'customer_id'         => $validated['customer_id'],
            'receiver_name'       => $validated['receiver_name'],
            'receiver_tax_number' => $validated['receiver_tax_number'] ?? null,
            'tc_kimlik_no'        => $validated['tc_kimlik_no'] ?? null,
            'receiver_tax_office' => $validated['receiver_tax_office'] ?? null,
            'receiver_address'    => $validated['receiver_address'] ?? null,
            'delivery_address'    => $validated['delivery_address'] ?? null,
            'recipient_type'      => $validated['recipient_type'] ?? 'corporate',
            'is_internet_sale'    => $validated['is_internet_sale'] ?? false,
            'internet_sale_platform' => $validated['internet_sale_platform'] ?? null,
            'internet_sale_url'   => $validated['internet_sale_url'] ?? null,
            'branch_id'           => $validated['branch_id'] ?? null,
            'sale_id'             => $validated['sale_id'] ?? null,
            'currency'            => $validated['currency'] ?? 'TRY',
            'exchange_rate'       => $validated['exchange_rate'] ?? 1,
            'vat_rate'            => $validated['vat_rate'] ?? 20,
            'invoice_date'        => $validated['invoice_date'],
            'payment_date'        => $validated['payment_date'] ?? null,
            'payment_platform'    => $validated['payment_platform'] ?? null,
            'shipment_date'       => $validated['shipment_date'] ?? null,
            'waybill_no'          => $validated['waybill_no'] ?? null,
            'vehicle_plate'       => $validated['vehicle_plate'] ?? null,
            'driver_name'         => $validated['driver_name'] ?? null,
            'driver_tc'           => $validated['driver_tc'] ?? null,
            'shipping_company'    => $validated['shipping_company'] ?? null,
            'tracking_no'         => $validated['tracking_no'] ?? null,
            'notes'               => $validated['notes'] ?? null,
            'payment_method'      => $validated['payment_method'] ?? null,
            'subtotal'            => $subtotal,
            'vat_total'           => $vatTotal,
            'discount_total'      => $discountTotal,
            'grand_total'         => $subtotal - $discountTotal + $vatTotal,
        ]);

        foreach ($validated['items'] as $item) {
            $invoice->items()->create($item);
        }

        $docLabel = $validated['document_type'] === 'irsaliye' ? 'İrsaliye' : ($validated['scenario'] === 'e_arsiv' ? 'e-Arşiv Fatura' : 'Fatura');
        $redirect = $validated['document_type'] === 'irsaliye'
            ? route('faturalar.waybills')
            : ($validated['scenario'] === 'e_arsiv'
                ? route('faturalar.earsiv')
                : route('faturalar.outgoing'));

        return redirect($redirect)->with('success', "{$docLabel} {$invoiceNo} başarıyla oluşturuldu.");
    }

    // ══════════════════════════════════════════════════════════════
    // DETAY
    // ══════════════════════════════════════════════════════════════
    public function show($id)
    {
        $einvoice = EInvoice::with('items.product', 'customer', 'branch', 'sale')->findOrFail($id);
        return view('faturalar.show', compact('einvoice'));
    }

    // ══════════════════════════════════════════════════════════════
    // DURUM GÜNCELLE
    // ══════════════════════════════════════════════════════════════
    public function updateStatus(Request $request, $id)
    {
        $invoice = EInvoice::findOrFail($id);
        $request->validate([
            'status' => 'required|in:draft,sent,accepted,rejected,cancelled',
        ]);

        $invoice->update([
            'status' => $request->status,
            'sent_at' => $request->status === 'sent' ? now() : $invoice->sent_at,
        ]);

        return back()->with('success', 'Fatura durumu güncellendi: ' . $invoice->status_label);
    }

    // ══════════════════════════════════════════════════════════════
    // ALIŞ FATURASI DETAY (eski uyumluluk)
    // ══════════════════════════════════════════════════════════════
    public function purchaseShow(PurchaseInvoice $invoice)
    {
        $invoice->load(['firm', 'branch', 'items.product']);

        $invoiceStats = [
            'item_count'     => $invoice->items->count(),
            'total_quantity' => $invoice->items->sum('quantity'),
            'total_amount'   => $invoice->total_amount,
        ];

        return view('invoices.show', compact('invoice', 'invoiceStats'));
    }
}
