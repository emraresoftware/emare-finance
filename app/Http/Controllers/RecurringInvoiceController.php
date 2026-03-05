<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\EInvoice;
use App\Models\EInvoiceSetting;
use App\Models\Product;
use App\Models\RecurringInvoice;
use App\Models\RecurringInvoiceItem;
use App\Models\ServiceCategory;
use App\Models\TaxRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecurringInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = RecurringInvoice::with(['customer', 'serviceCategory', 'branch']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%")
                  ->orWhereHas('customer', fn($cq) => $cq->where('name', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('frequency')) {
            $query->where('frequency', $request->frequency);
        }

        if ($request->filled('service_category_id')) {
            $query->where('service_category_id', $request->service_category_id);
        }

        $recurringInvoices = $query->latest()->paginate(25)->withQueryString();

        $stats = [
            'total' => RecurringInvoice::count(),
            'active' => RecurringInvoice::where('status', 'active')->count(),
            'paused' => RecurringInvoice::where('status', 'paused')->count(),
            'due_today' => RecurringInvoice::due()->count(),
            'monthly_revenue' => RecurringInvoice::active()->where('frequency', 'monthly')->sum('grand_total'),
            'total_generated' => RecurringInvoice::sum('invoices_generated'),
        ];

        $serviceCategories = ServiceCategory::active()->orderBy('name')->get();

        return view('recurring-invoices.index', compact('recurringInvoices', 'stats', 'serviceCategories'));
    }

    public function create()
    {
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        $branches = Branch::orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $serviceCategories = ServiceCategory::active()->orderBy('name')->get();
        $taxRates = TaxRate::active()->orderBy('sort_order')->orderBy('rate')->get()->groupBy('code');

        return view('recurring-invoices.create', compact(
            'customers', 'branches', 'products', 'serviceCategories', 'taxRates'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'customer_id' => 'nullable|exists:customers,id',
            'branch_id' => 'nullable|exists:branches,id',
            'service_category_id' => 'nullable|exists:service_categories,id',
            'frequency' => 'required|in:weekly,monthly,bimonthly,quarterly,semiannual,annual',
            'frequency_day' => 'nullable|integer|min:1|max:31',
            'currency' => 'nullable|string|max:5',
            'payment_method' => 'nullable|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'max_invoices' => 'nullable|integer|min:1',
            'auto_send' => 'nullable|boolean',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_name' => 'required|string|max:255',
            'items.*.product_code' => 'nullable|string|max:100',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.taxes' => 'nullable|array',
            'items.*.product_id' => 'nullable|exists:products,id',
        ]);

        $subtotal = 0;
        $taxTotal = 0;
        $discountTotal = 0;

        foreach ($validated['items'] as &$item) {
            $item['discount'] = $item['discount'] ?? 0;
            $item['unit'] = $item['unit'] ?? 'Adet';
            $lineTotal = ($item['quantity'] * $item['unit_price']) - $item['discount'];
            $taxAmount = 0;

            if (!empty($item['taxes'])) {
                foreach ($item['taxes'] as &$tax) {
                    $taxRate = TaxRate::find($tax['tax_rate_id'] ?? 0);
                    if ($taxRate) {
                        $tax['code'] = $taxRate->code;
                        $tax['rate'] = (float) $taxRate->rate;
                        $tax['amount'] = $taxRate->calculateTax($lineTotal);
                        $taxAmount += $tax['amount'];
                    }
                }
            }

            $item['tax_amount'] = $taxAmount;
            $item['total'] = round($lineTotal + $taxAmount, 2);
            $subtotal += ($item['quantity'] * $item['unit_price']);
            $discountTotal += $item['discount'];
            $taxTotal += $taxAmount;
        }

        $invoice = RecurringInvoice::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'customer_id' => $validated['customer_id'] ?? null,
            'branch_id' => $validated['branch_id'] ?? null,
            'service_category_id' => $validated['service_category_id'] ?? null,
            'frequency' => $validated['frequency'],
            'frequency_day' => $validated['frequency_day'] ?? 1,
            'currency' => $validated['currency'] ?? 'TRY',
            'payment_method' => $validated['payment_method'] ?? null,
            'status' => 'active',
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'] ?? null,
            'next_invoice_date' => $validated['start_date'],
            'max_invoices' => $validated['max_invoices'] ?? null,
            'auto_send' => $request->has('auto_send'),
            'notes' => $validated['notes'] ?? null,
            'subtotal' => $subtotal,
            'tax_total' => $taxTotal,
            'discount_total' => $discountTotal,
            'grand_total' => $subtotal - $discountTotal + $taxTotal,
        ]);

        foreach ($validated['items'] as $item) {
            $invoice->items()->create($item);
        }

        return redirect()->route('recurring_invoices.index')
            ->with('success', "Tekrarlayan fatura \"{$invoice->title}\" başarıyla oluşturuldu.");
    }

    public function show(RecurringInvoice $recurringInvoice)
    {
        $recurringInvoice->load(['customer', 'branch', 'serviceCategory', 'items.product']);

        // Bu tekrarlayan faturadan oluşturulan e-faturalar
        $generatedInvoices = EInvoice::where('notes', 'like', "%[RF#{$recurringInvoice->id}]%")
            ->latest('invoice_date')
            ->get();

        return view('recurring-invoices.show', compact('recurringInvoice', 'generatedInvoices'));
    }

    public function updateStatus(Request $request, RecurringInvoice $recurringInvoice)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,paused,cancelled,completed',
        ]);

        $recurringInvoice->update(['status' => $validated['status']]);

        $statusLabels = [
            'active' => 'aktif hale getirildi',
            'paused' => 'duraklatıldı',
            'cancelled' => 'iptal edildi',
            'completed' => 'tamamlandı olarak işaretlendi',
        ];

        return redirect()->route('recurring_invoices.show', $recurringInvoice)
            ->with('success', "Tekrarlayan fatura {$statusLabels[$validated['status']]}.");
    }

    /**
     * Vadesi gelen tekrarlayan faturalardan e-fatura oluştur
     */
    public function generateDueInvoices()
    {
        $dueInvoices = RecurringInvoice::due()->with('items', 'customer')->get();
        $generated = 0;

        DB::beginTransaction();
        try {
            foreach ($dueInvoices as $recurring) {
                $this->generateInvoiceFrom($recurring);
                $generated++;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('recurring_invoices.index')
                ->with('error', "Fatura oluşturma hatası: {$e->getMessage()}");
        }

        return redirect()->route('recurring_invoices.index')
            ->with('success', "{$generated} adet tekrarlayan fatura için e-fatura oluşturuldu.");
    }

    /**
     * Tek bir tekrarlayan faturadan e-fatura oluştur
     */
    public function generateSingle(RecurringInvoice $recurringInvoice)
    {
        DB::beginTransaction();
        try {
            $this->generateInvoiceFrom($recurringInvoice);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('recurring_invoices.show', $recurringInvoice)
                ->with('error', "Fatura oluşturma hatası: {$e->getMessage()}");
        }

        return redirect()->route('recurring_invoices.show', $recurringInvoice)
            ->with('success', 'E-Fatura başarıyla oluşturuldu.');
    }

    /**
     * Tekrarlayan faturadan e-fatura oluştur (internal)
     */
    private function generateInvoiceFrom(RecurringInvoice $recurring): EInvoice
    {
        $settings = EInvoiceSetting::current();
        $prefix = $settings->invoice_prefix ?? 'EMR';
        $counter = $settings->invoice_counter;
        $invoiceNo = $prefix . str_pad($counter, 9, '0', STR_PAD_LEFT);
        $settings->increment('invoice_counter');

        $subtotal = 0;
        $vatTotal = 0;
        $additionalTaxTotal = 0;
        $discountTotal = 0;

        $itemsData = [];
        foreach ($recurring->items as $item) {
            $lineTotal = ($item->quantity * $item->unit_price) - $item->discount;
            $vatRate = 20;
            $additionalTaxes = [];

            if (!empty($item->taxes)) {
                foreach ($item->taxes as $tax) {
                    if (($tax['code'] ?? '') === 'KDV') {
                        $vatRate = $tax['rate'] ?? 20;
                    } else {
                        $additionalTaxes[] = $tax;
                    }
                }
            }

            $vatAmount = round($lineTotal * $vatRate / 100, 2);
            $addTaxAmount = 0;
            foreach ($additionalTaxes as &$at) {
                $at['amount'] = round($lineTotal * ($at['rate'] ?? 0) / 100, 2);
                $addTaxAmount += $at['amount'];
            }

            $itemsData[] = [
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'product_code' => $item->product_code,
                'unit' => $item->unit,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'discount' => $item->discount,
                'vat_rate' => $vatRate,
                'vat_amount' => $vatAmount,
                'additional_taxes' => $additionalTaxes,
                'additional_tax_amount' => $addTaxAmount,
                'total' => round($lineTotal + $vatAmount + $addTaxAmount, 2),
            ];

            $subtotal += ($item->quantity * $item->unit_price);
            $discountTotal += $item->discount;
            $vatTotal += $vatAmount;
            $additionalTaxTotal += $addTaxAmount;
        }

        $invoice = EInvoice::create([
            'invoice_no' => $invoiceNo,
            'direction' => 'outgoing',
            'type' => 'invoice',
            'scenario' => $settings->default_scenario ?? 'basic',
            'status' => $recurring->auto_send ? 'sent' : 'draft',
            'customer_id' => $recurring->customer_id,
            'receiver_name' => $recurring->customer?->name ?? '',
            'receiver_tax_number' => $recurring->customer?->tax_number,
            'receiver_tax_office' => $recurring->customer?->tax_office,
            'receiver_address' => $recurring->customer?->address,
            'branch_id' => $recurring->branch_id,
            'currency' => $recurring->currency,
            'exchange_rate' => 1,
            'vat_rate' => 20,
            'invoice_date' => now()->toDateString(),
            'notes' => "[RF#{$recurring->id}] {$recurring->title} - Otomatik oluşturuldu",
            'payment_method' => $recurring->payment_method,
            'subtotal' => $subtotal,
            'vat_total' => $vatTotal,
            'additional_tax_total' => $additionalTaxTotal,
            'discount_total' => $discountTotal,
            'grand_total' => $subtotal - $discountTotal + $vatTotal + $additionalTaxTotal,
        ]);

        foreach ($itemsData as $itemData) {
            $invoice->items()->create($itemData);
        }

        // Tekrarlayan fatura güncelle
        $nextDate = $recurring->calculateNextInvoiceDate();
        $recurring->update([
            'last_invoice_date' => now()->toDateString(),
            'next_invoice_date' => $nextDate?->toDateString(),
            'invoices_generated' => $recurring->invoices_generated + 1,
            'status' => $nextDate ? $recurring->status : 'completed',
        ]);

        return $invoice;
    }

    public function destroy(RecurringInvoice $recurringInvoice)
    {
        $recurringInvoice->delete();
        return redirect()->route('recurring_invoices.index')
            ->with('success', 'Tekrarlayan fatura başarıyla silindi.');
    }
}
