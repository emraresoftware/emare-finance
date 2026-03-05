<?php

namespace App\Http\Controllers;

use App\Models\AccountPlan;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountingController extends Controller
{
    // ══════════════════════════════════════════════════════════════
    // DASHBOARD
    // ══════════════════════════════════════════════════════════════

    public function dashboard()
    {
        $year  = date('Y');
        $month = date('m');

        // Son 5 fiş
        $recentEntries = JournalEntry::with('lines')
            ->latest('date')
            ->take(5)
            ->get();

        // Bu ay özet
        $thisMonthStart = "{$year}-{$month}-01";
        $thisMonthEnd   = date('Y-m-t');

        $monthlyDebit  = JournalEntryLine::whereHas('journalEntry', fn($q) =>
            $q->where('is_posted', true)
              ->whereBetween('date', [$thisMonthStart, $thisMonthEnd])
        )->sum('debit');

        $monthlyCredit = JournalEntryLine::whereHas('journalEntry', fn($q) =>
            $q->where('is_posted', true)
              ->whereBetween('date', [$thisMonthStart, $thisMonthEnd])
        )->sum('credit');

        // Toplam fiş sayıları
        $stats = [
            'total_entries'   => JournalEntry::count(),
            'posted_entries'  => JournalEntry::where('is_posted', true)->count(),
            'draft_entries'   => JournalEntry::where('is_posted', false)->count(),
            'total_accounts'  => AccountPlan::where('is_active', true)->count(),
            'monthly_debit'   => $monthlyDebit,
            'monthly_credit'  => $monthlyCredit,
        ];

        // Hesap planı yüklü mü
        $accountPlanLoaded = AccountPlan::exists();

        return view('accounting.dashboard', compact('recentEntries', 'stats', 'accountPlanLoaded', 'year', 'month'));
    }

    // ══════════════════════════════════════════════════════════════
    // MİZAN (Trial Balance)
    // ══════════════════════════════════════════════════════════════

    public function trialBalance(Request $request)
    {
        $startDate = $request->input('start', date('Y-01-01'));
        $endDate   = $request->input('end',   date('Y-m-d'));

        $lines = JournalEntryLine::select(
                'account_code',
                DB::raw('SUM(debit) as total_debit'),
                DB::raw('SUM(credit) as total_credit')
            )
            ->whereHas('journalEntry', fn($q) =>
                $q->where('is_posted', true)
                  ->whereBetween('date', [$startDate, $endDate])
            )
            ->groupBy('account_code')
            ->orderBy('account_code')
            ->get();

        // Hesap planı ile birleştir
        $accounts = AccountPlan::where('is_active', true)
            ->orderBy('code')
            ->get()
            ->keyBy('code');

        $rows = $lines->map(function ($line) use ($accounts) {
            $acc      = $accounts[$line->account_code] ?? null;
            $debit    = (float) $line->total_debit;
            $credit   = (float) $line->total_credit;
            $balance  = $acc && $acc->normal_balance === 'debit'
                ? $debit - $credit
                : $credit - $debit;

            return [
                'code'          => $line->account_code,
                'name'          => $acc?->name ?? 'Bilinmeyen',
                'type'          => $acc?->type ?? '-',
                'total_debit'   => $debit,
                'total_credit'  => $credit,
                'balance'       => $balance,
                'normal_balance'=> $acc?->normal_balance ?? 'debit',
            ];
        });

        $grandDebit  = $rows->sum('total_debit');
        $grandCredit = $rows->sum('total_credit');

        return view('accounting.trial-balance', compact(
            'rows', 'grandDebit', 'grandCredit', 'startDate', 'endDate'
        ));
    }

    // ══════════════════════════════════════════════════════════════
    // BİLANÇO (Balance Sheet)
    // ══════════════════════════════════════════════════════════════

    public function balanceSheet(Request $request)
    {
        $asOf = $request->input('date', date('Y-m-d'));

        // Tüm aktif hesaplar için bakiye hesapla
        $lines = JournalEntryLine::select(
                'account_code',
                DB::raw('SUM(debit) as total_debit'),
                DB::raw('SUM(credit) as total_credit')
            )
            ->whereHas('journalEntry', fn($q) =>
                $q->where('is_posted', true)
                  ->where('date', '<=', $asOf)
            )
            ->groupBy('account_code')
            ->get()
            ->keyBy('account_code');

        $accounts = AccountPlan::where('is_active', true)
            ->whereIn('type', ['asset', 'liability', 'equity'])
            ->orderBy('code')
            ->get();

        $data = [];
        foreach ($accounts as $acc) {
            $line   = $lines[$acc->code] ?? null;
            $debit  = (float) ($line?->total_debit  ?? 0);
            $credit = (float) ($line?->total_credit ?? 0);
            $bal    = $acc->normal_balance === 'debit' ? $debit - $credit : $credit - $debit;

            if (abs($bal) < 0.001) continue; // Sıfır bakiyeli hesapları atla

            $data[$acc->type][] = [
                'code'    => $acc->code,
                'name'    => $acc->name,
                'level'   => $acc->level,
                'balance' => $bal,
            ];
        }

        $totalAssets      = collect($data['asset']    ?? [])->sum('balance');
        $totalLiabilities = collect($data['liability'] ?? [])->sum('balance');
        $totalEquity      = collect($data['equity']    ?? [])->sum('balance');
        $totalPasif       = $totalLiabilities + $totalEquity;

        return view('accounting.balance-sheet', compact(
            'data', 'totalAssets', 'totalLiabilities', 'totalEquity', 'totalPasif', 'asOf'
        ));
    }

    // ══════════════════════════════════════════════════════════════
    // GELİR TABLOSU (Income Statement)
    // ══════════════════════════════════════════════════════════════

    public function incomeStatement(Request $request)
    {
        $startDate = $request->input('start', date('Y-01-01'));
        $endDate   = $request->input('end',   date('Y-m-d'));

        $lines = JournalEntryLine::select(
                'account_code',
                DB::raw('SUM(debit) as total_debit'),
                DB::raw('SUM(credit) as total_credit')
            )
            ->whereHas('journalEntry', fn($q) =>
                $q->where('is_posted', true)
                  ->whereBetween('date', [$startDate, $endDate])
            )
            ->groupBy('account_code')
            ->get()
            ->keyBy('account_code');

        $accounts = AccountPlan::where('is_active', true)
            ->whereIn('type', ['revenue', 'cost', 'expense'])
            ->orderBy('code')
            ->get();

        $revenues  = [];
        $costs     = [];
        $expenses  = [];

        foreach ($accounts as $acc) {
            $line   = $lines[$acc->code] ?? null;
            $debit  = (float) ($line?->total_debit  ?? 0);
            $credit = (float) ($line?->total_credit ?? 0);
            $bal    = $acc->normal_balance === 'debit' ? $debit - $credit : $credit - $debit;

            if (abs($bal) < 0.001) continue;

            $row = ['code' => $acc->code, 'name' => $acc->name, 'level' => $acc->level, 'amount' => $bal];

            match ($acc->type) {
                'revenue' => $revenues[] = $row,
                'cost'    => $costs[]    = $row,
                'expense' => $expenses[] = $row,
                default   => null,
            };
        }

        $grossRevenue = collect($revenues)->sum('amount');
        $totalCost    = collect($costs)->sum('amount');
        $grossProfit  = $grossRevenue - $totalCost;
        $totalExpense = collect($expenses)->sum('amount');
        $netProfit    = $grossProfit - $totalExpense;

        return view('accounting.income-statement', compact(
            'revenues', 'costs', 'expenses',
            'grossRevenue', 'totalCost', 'grossProfit',
            'totalExpense', 'netProfit',
            'startDate', 'endDate'
        ));
    }

    // ══════════════════════════════════════════════════════════════
    // HESAP PLANI
    // ══════════════════════════════════════════════════════════════

    public function accountPlan(Request $request)
    {
        $query = AccountPlan::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('code', 'like', "%{$s}%")
                  ->orWhere('name', 'like', "%{$s}%");
            });
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        $accounts = $query->orderBy('code')->paginate(50)->withQueryString();

        return view('accounting.account-plan', compact('accounts'));
    }

    public function accountPlanStore(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:20|unique:account_plan,code',
            'name' => 'required|string|max:255',
            'type' => 'required|in:asset,liability,equity,revenue,cost,expense',
            'normal_balance' => 'required|in:debit,credit',
            'level' => 'required|integer|min:1|max:5',
            'parent_code' => 'nullable|string|max:20',
        ]);

        AccountPlan::create($request->only([
            'code', 'name', 'type', 'normal_balance', 'level', 'parent_code',
        ]));

        return back()->with('success', 'Hesap eklendi: ' . $request->code . ' - ' . $request->name);
    }

    public function accountPlanUpdate(Request $request, AccountPlan $account)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $account->update($request->only(['name', 'is_active']));

        return back()->with('success', 'Hesap güncellendi.');
    }

    /**
     * Hesap planını seed eder (dashboard'dan tetiklenir).
     */
    public function setup(Request $request)
    {
        try {
            \Artisan::call('db:seed', ['--class' => 'AccountPlanSeeder', '--force' => true]);
            return redirect()->route('accounting.dashboard')
                ->with('success', 'Tekdüzen Hesap Planı başarıyla yüklendi!');
        } catch (\Exception $e) {
            return redirect()->route('accounting.dashboard')
                ->with('error', 'Hesap planı yüklenirken hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Örnek fişler oluştur.
     */
    public function sampleEntries(Request $request)
    {
        $created = AccountingService::createSampleEntries(
            auth()->user()->branch_id,
            auth()->id()
        );

        return redirect()->route('accounting.journal.index')
            ->with('success', count($created) . ' örnek fiş başarıyla oluşturuldu ve kesinleştirildi!');
    }

    /**
     * QR kod tarama sayfası (mobil).
     */
    public function scan()
    {
        return view('accounting.scan');
    }

    /**
     * QR koddan fiş bul ve yönlendir.
     */
    public function scanResult(Request $request)
    {
        $code = $request->input('code', '');

        // Fiş numarasıyla ara
        $entry = JournalEntry::where('entry_no', $code)->first();
        if ($entry) {
            return redirect()->route('accounting.journal.show', $entry);
        }

        // ID ile ara (QR içeriği yalnızca ID ise)
        if (is_numeric($code)) {
            $entry = JournalEntry::find($code);
            if ($entry) {
                return redirect()->route('accounting.journal.show', $entry);
            }
        }

        return redirect()->route('accounting.scan')
            ->with('error', 'Fiş bulunamadı: ' . $code);
    }
}
