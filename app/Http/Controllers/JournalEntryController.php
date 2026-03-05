<?php

namespace App\Http\Controllers;

use App\Models\AccountPlan;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JournalEntryController extends Controller
{
    public function index(Request $request)
    {
        $query = JournalEntry::with(['creator'])->latest('date');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('is_posted', $request->status === 'posted');
        }
        if ($request->filled('start')) {
            $query->where('date', '>=', $request->start);
        }
        if ($request->filled('end')) {
            $query->where('date', '<=', $request->end);
        }
        if ($request->filled('q')) {
            $s = $request->q;
            $query->where(function ($q) use ($s) {
                $q->where('entry_no', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%");
            });
        }

        $entries = $query->paginate(25)->withQueryString();

        return view('accounting.journal.index', compact('entries'));
    }

    public function create()
    {
        $accounts = AccountPlan::where('is_active', true)
            ->where('level', '>=', 2)
            ->orderBy('code')
            ->get();

        $nextNo = JournalEntry::nextEntryNo();

        return view('accounting.journal.create', compact('accounts', 'nextNo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'        => 'required|date',
            'description' => 'required|string|max:500',
            'type'        => 'required|in:opening,sale,purchase,expense,income,payroll,adjustment,closing,manual',
            'lines'       => 'required|array|min:2',
            'lines.*.account_code' => 'required|exists:account_plan,code',
            'lines.*.debit'        => 'nullable|numeric|min:0',
            'lines.*.credit'       => 'nullable|numeric|min:0',
            'lines.*.description'  => 'nullable|string|max:255',
        ]);

        $lines = collect($request->lines)->filter(fn($l) =>
            (float)($l['debit'] ?? 0) > 0 || (float)($l['credit'] ?? 0) > 0
        );

        $totalDebit  = $lines->sum(fn($l) => (float)($l['debit']  ?? 0));
        $totalCredit = $lines->sum(fn($l) => (float)($l['credit'] ?? 0));

        if (abs($totalDebit - $totalCredit) > 0.01) {
            return back()
                ->withErrors(['lines' => "Borç ({$totalDebit}) ve Alacak ({$totalCredit}) toplamları eşit değil."])
                ->withInput();
        }

        DB::transaction(function () use ($request, $lines) {
            $entry = JournalEntry::create([
                'entry_no'    => JournalEntry::nextEntryNo(),
                'date'        => $request->date,
                'description' => $request->description,
                'type'        => $request->type,
                'is_posted'   => $request->input('post_entry') === '1',
                'branch_id'   => auth()->user()->branch_id,
                'created_by'  => auth()->id(),
            ]);

            foreach ($lines as $i => $line) {
                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_code'     => $line['account_code'],
                    'description'      => $line['description'] ?? null,
                    'debit'            => (float)($line['debit']  ?? 0),
                    'credit'           => (float)($line['credit'] ?? 0),
                    'line_order'       => $i,
                ]);
            }
        });

        return redirect()->route('accounting.journal.index')
            ->with('success', 'Yevmiye fişi oluşturuldu.');
    }

    public function show(JournalEntry $entry)
    {
        $entry->load('lines.account', 'creator', 'branch');
        return view('accounting.journal.show', compact('entry'));
    }

    public function post(JournalEntry $entry)
    {
        if ($entry->is_posted) {
            return back()->withErrors(['Fiş zaten kesinleştirilmiş.']);
        }

        if (!$entry->isBalanced()) {
            return back()->withErrors(['Fiş dengeli değil — kesinleştirilemez.']);
        }

        $entry->update(['is_posted' => true]);

        return back()->with('success', 'Fiş kesinleştirildi: ' . $entry->entry_no);
    }

    public function destroy(JournalEntry $entry)
    {
        if ($entry->is_posted) {
            return back()->withErrors(['Kesinleştirilmiş fiş silinemez.']);
        }

        $entry->delete();

        return redirect()->route('accounting.journal.index')
            ->with('success', 'Fiş silindi.');
    }

    /**
     * Kamera ile fiş okutma sayfası.
     */
    public function camera()
    {
        $accounts = AccountPlan::where('is_active', true)
            ->where('level', '>=', 2)
            ->orderBy('code')
            ->get();

        return view('accounting.journal.camera', compact('accounts'));
    }

    /**
     * Kamera ile fotoğraflanan fişten yevmiye oluştur.
     */
    public function cameraStore(Request $request)
    {
        $request->validate([
            'date'           => 'required|date',
            'description'    => 'required|string|max:500',
            'type'           => 'required|in:opening,sale,purchase,expense,income,payroll,adjustment,closing,manual',
            'lines'          => 'required|array|min:2',
            'lines.*.account_code' => 'required|exists:account_plan,code',
            'lines.*.debit'        => 'nullable|numeric|min:0',
            'lines.*.credit'       => 'nullable|numeric|min:0',
            'receipt_photo'        => 'nullable|image|max:5120',
        ]);

        $lines = collect($request->lines)->filter(fn($l) =>
            (float)($l['debit'] ?? 0) > 0 || (float)($l['credit'] ?? 0) > 0
        );

        $totalDebit  = $lines->sum(fn($l) => (float)($l['debit']  ?? 0));
        $totalCredit = $lines->sum(fn($l) => (float)($l['credit'] ?? 0));

        if (abs($totalDebit - $totalCredit) > 0.01) {
            return back()
                ->withErrors(['lines' => "Borç ({$totalDebit}) ve Alacak ({$totalCredit}) eşit değil."])
                ->withInput();
        }

        // Fotoğrafı kaydet
        $photoPath = null;
        if ($request->hasFile('receipt_photo')) {
            $photoPath = $request->file('receipt_photo')->store('receipts', 'public');
        }

        DB::transaction(function () use ($request, $lines, $photoPath) {
            $entry = JournalEntry::create([
                'entry_no'    => JournalEntry::nextEntryNo(),
                'date'        => $request->date,
                'description' => $request->description,
                'type'        => $request->type,
                'is_posted'   => $request->input('post_entry') === '1',
                'branch_id'   => auth()->user()->branch_id,
                'created_by'  => auth()->id(),
            ]);

            foreach ($lines as $i => $line) {
                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_code'     => $line['account_code'],
                    'description'      => $line['description'] ?? null,
                    'debit'            => (float)($line['debit']  ?? 0),
                    'credit'           => (float)($line['credit'] ?? 0),
                    'line_order'       => $i,
                ]);
            }

            // Fotoğraf referansını açıklamaya ekle
            if ($photoPath) {
                $entry->update(['description' => $entry->description . ' [Fotoğraf: ' . $photoPath . ']']);
            }
        });

        return redirect()->route('accounting.journal.index')
            ->with('success', 'Fiş kamera ile oluşturuldu!');
    }
}
