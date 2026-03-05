<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\AccountTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::withCount('sales')->withSum('sales', 'grand_total');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($type = $request->get('type')) {
            $query->where('type', $type);
        }

        if ($request->get('has_debt')) {
            $query->where('balance', '<', 0);
        }

        if ($request->get('has_credit')) {
            $query->where('balance', '>', 0);
        }

        $sortBy = $request->get('sort', 'name');
        $sortDir = $request->get('dir', 'asc');
        $allowedSorts = ['name', 'balance', 'sales_count', 'created_at'];
        if (!in_array($sortBy, $allowedSorts)) $sortBy = 'name';
        $query->orderBy($sortBy, $sortDir === 'desc' ? 'desc' : 'asc');

        $customers = $query->paginate(25)->appends($request->query());

        $stats = [
            'total' => Customer::count(),
            'company' => Customer::where('type', 'company')->count(),
            'individual' => Customer::where('type', 'individual')->count(),
            'with_debt' => Customer::where('balance', '<', 0)->count(),
            'total_debt' => abs(Customer::where('balance', '<', 0)->sum('balance')),
            'total_credit' => Customer::where('balance', '>', 0)->sum('balance'),
        ];

        return view('customers.index', compact('customers', 'stats'));
    }

    /**
     * Yeni Müşteri Ekle - Form göster
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Yeni Müşteri Ekle - Kaydet
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:individual,company',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'tax_number' => 'nullable|string|max:20',
            'tax_office' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $validated['balance'] = 0;
        $validated['is_active'] = true;

        $customer = Customer::create($validated);

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Müşteri başarıyla eklendi.');
    }

    /**
     * Müşteri Sil (Soft Delete)
     */
    public function destroy(Customer $customer)
    {
        $customerName = $customer->name;
        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', "{$customerName} silindi.");
    }

    /**
     * Tahsilat (Ödeme) Kaydet
     */
    public function addPayment(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:500',
            'payment_date' => 'nullable|date',
        ]);

        DB::transaction(function () use ($customer, $validated) {
            $amount = $validated['amount'];
            $newBalance = $customer->balance + $amount;

            AccountTransaction::create([
                'customer_id' => $customer->id,
                'type' => 'payment',
                'amount' => $amount,
                'balance_after' => $newBalance,
                'description' => $validated['description'] ?? 'Manuel tahsilat',
                'transaction_date' => $validated['payment_date'] ?? now(),
            ]);

            $customer->update(['balance' => $newBalance]);
        });

        return redirect()->route('customers.show', $customer)
            ->with('success', '₺' . number_format($validated['amount'], 2, ',', '.') . ' tahsilat kaydedildi.');
    }

    public function show(Request $request, Customer $customer)
    {
        // Tarih filtreleri (varsayılan: 3 ay geriye)
        $startDate = $request->get('start_date', now()->subMonths(3)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        // Alışverişler (tarih filtreli)
        $salesQuery = $customer->sales()
            ->when($startDate, fn($q) => $q->where('sold_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->where('sold_at', '<=', $endDate . ' 23:59:59'));

        // Tüm işlemler modunda tarih filtresi yok
        if ($request->get('all')) {
            $salesQuery = $customer->sales();
        }

        $sales = $salesQuery->with('branch')
            ->latest('sold_at')
            ->paginate(25)
            ->appends($request->query());

        // Alacaklar (hesap hareketleri)
        $transactions = $customer->transactions()
            ->latest('transaction_date')
            ->paginate(10, ['*'], 'tx_page')
            ->appends($request->query());

        // İstatistikler (tarih aralığına göre)
        $filteredSales = $customer->sales()
            ->when(!$request->get('all'), function ($q) use ($startDate, $endDate) {
                $q->when($startDate, fn($q2) => $q2->where('sold_at', '>=', $startDate))
                  ->when($endDate, fn($q2) => $q2->where('sold_at', '<=', $endDate . ' 23:59:59'));
            });

        $totalSales = (clone $filteredSales)->where('status', 'completed')->sum('grand_total');
        $totalDebt = (clone $filteredSales)->where('payment_method', 'credit')->sum('grand_total');
        $totalPayments = $customer->transactions()
            ->where('type', 'payment')
            ->when(!$request->get('all'), function ($q) use ($startDate, $endDate) {
                $q->when($startDate, fn($q2) => $q2->where('transaction_date', '>=', $startDate))
                  ->when($endDate, fn($q2) => $q2->where('transaction_date', '<=', $endDate . ' 23:59:59'));
            })->sum('amount');

        $customerStats = [
            'total_sales' => $totalSales,
            'total_debt' => $totalDebt,
            'total_payments' => abs($totalPayments),
            'remaining_debt' => $customer->balance,
            'date_range' => $startDate . ' - ' . $endDate,
        ];

        return view('customers.show', compact('customer', 'customerStats', 'sales', 'transactions', 'startDate', 'endDate'));
    }

    /**
     * Müşteri Güncelle - Form göster
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * Müşteri Güncelle - Kaydet
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:individual,company',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'tax_number' => 'nullable|string|max:20',
            'tax_office' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $customer->update($validated);

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Müşteri bilgileri güncellendi.');
    }

    /**
     * Müşteri alışverişlerini Excel olarak indir
     */
    public function exportSales(Request $request, Customer $customer): StreamedResponse
    {
        $startDate = $request->get('start_date', now()->subMonths(3)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $sales = $customer->sales()
            ->when(!$request->get('all'), function ($q) use ($startDate, $endDate) {
                $q->when($startDate, fn($q2) => $q2->where('sold_at', '>=', $startDate))
                  ->when($endDate, fn($q2) => $q2->where('sold_at', '<=', $endDate . ' 23:59:59'));
            })
            ->latest('sold_at')->get();

        return response()->streamDownload(function () use ($sales) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Satış Kodu', 'Toplam Ürün', 'İskonto', 'Toplam Tutar', 'Kalan Borç', 'Ödeme Tipi', 'Not', 'Personel', 'Tarih', 'Saat'], ';');
            foreach ($sales as $sale) {
                fputcsv($handle, [
                    $sale->receipt_no ?? '#' . $sale->id,
                    $sale->total_items,
                    number_format($sale->discount_total, 2, ',', ''),
                    number_format($sale->grand_total, 2, ',', ''),
                    $sale->payment_method === 'credit' ? number_format($sale->grand_total, 2, ',', '') : '0,00',
                    $sale->payment_method_label,
                    $sale->notes ?? '',
                    $sale->staff_name ?? '',
                    $sale->sold_at?->format('d.m.Y') ?? '',
                    $sale->sold_at?->format('H:i') ?? '',
                ], ';');
            }
            fclose($handle);
        }, 'musteri-alisverisler-' . date('Y-m-d') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function export(Request $request): StreamedResponse
    {
        $customers = Customer::withCount('sales')->orderBy('name')->get();

        return response()->streamDownload(function () use ($customers) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Ad', 'Tür', 'Telefon', 'Email', 'Satış Sayısı', 'Bakiye'], ';');
            foreach ($customers as $c) {
                fputcsv($handle, [
                    $c->name, $c->type === 'company' ? 'Firma' : 'Bireysel',
                    $c->phone ?? '', $c->email ?? '',
                    $c->sales_count, number_format($c->balance, 2, ',', ''),
                ], ';');
            }
            fclose($handle);
        }, 'cariler-' . date('Y-m-d') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
