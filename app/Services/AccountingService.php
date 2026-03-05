<?php

namespace App\Services;

use App\Models\AccountPlan;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Sale;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;

/**
 * AccountingService — Muhasebe Otomasyon Servisi
 *
 * Satış, gider, alım gibi işlemlerden otomatik yevmiye fişi üretir.
 * Hesap planında ilgili hesaplar yoksa sessizce atlar (sistemi kırmaz).
 */
class AccountingService
{
    // ══════════════════════════════════════════════════════════════
    // SATIŞ FİŞİ
    // ══════════════════════════════════════════════════════════════

    /**
     * Satıştan otomatik yevmiye fişi oluştur.
     *
     * Borç  : 102-BANKALAR veya 100-KASA (ödeme yöntemine göre)
     *       + 100-KASA (nakit kısım varsa)
     * Alacak: 600-YURTİÇİ SATIŞLAR (KDV hariç tutar)
     *       + 391-HESAPLANAN KDV (KDV tutarı)
     */
    public static function fromSale(Sale $sale): ?JournalEntry
    {
        // Hesap planı yüklü değilse atla
        if (!AccountPlan::exists()) return null;

        // Daha önce bu satış için fiş oluşturulmuş mu?
        $existing = JournalEntry::where('reference_type', 'sale')
            ->where('reference_id', $sale->id)
            ->first();
        if ($existing) return $existing;

        $grandTotal  = (float) $sale->grand_total;
        $vatTotal    = (float) $sale->vat_total;
        $netTotal    = $grandTotal - $vatTotal;
        $cashAmount  = (float) $sale->cash_amount;
        $cardAmount  = (float) $sale->card_amount;

        if ($grandTotal <= 0) return null;

        // Ödeme yöntemi yoksa tüm tutar kasa
        if ($cashAmount + $cardAmount == 0) {
            $cashAmount = $grandTotal;
        }

        $lines = [];

        // Borç: Nakit → 100-KASA, Kart → 102-BANKALAR
        if ($cashAmount > 0) {
            $lines[] = ['code' => '100', 'debit' => $cashAmount, 'credit' => 0, 'desc' => 'Kasa tahsilatı'];
        }
        if ($cardAmount > 0) {
            $lines[] = ['code' => '102', 'debit' => $cardAmount, 'credit' => 0, 'desc' => 'Banka/POS tahsilatı'];
        }

        // Alacak: 600-Satışlar
        if ($netTotal > 0) {
            $lines[] = ['code' => '600', 'debit' => 0, 'credit' => $netTotal, 'desc' => 'Satış geliri'];
        }

        // Alacak: 391-Hesaplanan KDV (veya 368)
        if ($vatTotal > 0) {
            $kdvCode = AccountPlan::where('code', '391')->exists() ? '391' : (AccountPlan::where('code', '368')->exists() ? '368' : null);
            if ($kdvCode) {
                $lines[] = ['code' => $kdvCode, 'debit' => 0, 'credit' => $vatTotal, 'desc' => 'Hesaplanan KDV'];
            } else {
                // KDV hesabı yoksa 600'e ekle
                $lines[count($lines)-1]['credit'] += $vatTotal;
            }
        }

        return self::createEntry([
            'type'           => 'sale',
            'date'           => $sale->sold_at?->toDateString() ?? now()->toDateString(),
            'description'    => 'Satış Fişi #' . ($sale->receipt_no ?? $sale->id),
            'reference_type' => 'sale',
            'reference_id'   => $sale->id,
            'branch_id'      => $sale->branch_id,
            'created_by'     => $sale->user_id,
            'is_posted'      => true,
        ], $lines);
    }

    // ══════════════════════════════════════════════════════════════
    // GİDER FİŞİ
    // ══════════════════════════════════════════════════════════════

    /**
     * Giderden otomatik yevmiye fişi oluştur.
     *
     * Borç  : 770-GENEL YÖNETİM VEYA İLGİLİ GİDER HESABI
     * Alacak: 100-KASA veya 102-BANKALAR (ödeme yöntemine göre)
     */
    public static function fromExpense(Expense $expense): ?JournalEntry
    {
        if (!AccountPlan::exists()) return null;

        $existing = JournalEntry::where('reference_type', 'expense')
            ->where('reference_id', $expense->id)
            ->first();
        if ($existing) return $existing;

        $amount = (float) $expense->amount;
        if ($amount <= 0) return null;

        // Gider hesabı seç (tip adından eşleştir)
        $expenseCode = self::matchExpenseAccount($expense->type_name ?? '');

        // Ödeme yöntemi
        $payCode = ($expense->payment_type === 'card' || $expense->payment_type === 'bank')
            ? '102' : '100';

        $lines = [
            ['code' => $expenseCode, 'debit' => $amount, 'credit' => 0, 'desc' => $expense->type_name ?? 'Gider'],
            ['code' => $payCode,     'debit' => 0, 'credit' => $amount, 'desc' => 'Ödeme'],
        ];

        return self::createEntry([
            'type'           => 'expense',
            'date'           => $expense->date?->toDateString() ?? now()->toDateString(),
            'description'    => 'Gider Fişi: ' . ($expense->type_name ?? 'Gider') . ($expense->note ? ' - ' . $expense->note : ''),
            'reference_type' => 'expense',
            'reference_id'   => $expense->id,
            'branch_id'      => null,
            'created_by'     => auth()->id(),
            'is_posted'      => true,
        ], $lines);
    }

    // ══════════════════════════════════════════════════════════════
    // MANUEL / ÖRNEK FİŞLER
    // ══════════════════════════════════════════════════════════════

    /**
     * Gösterim amaçlı örnek fişler oluştur.
     */
    public static function createSampleEntries(?int $branchId = null, ?int $userId = null): array
    {
        $created = [];
        $today = now();

        $samples = [
            // Açılış fişi
            [
                'type' => 'opening',
                'date' => $today->copy()->subDays(30)->toDateString(),
                'desc' => 'Dönem Açılış Fişi',
                'lines' => [
                    ['code' => '100', 'debit' => 50000, 'credit' => 0],
                    ['code' => '102', 'debit' => 150000, 'credit' => 0],
                    ['code' => '153', 'debit' => 80000, 'credit' => 0],
                    ['code' => '500', 'debit' => 0, 'credit' => 280000],
                ],
            ],
            // Satış fişi 1
            [
                'type' => 'sale',
                'date' => $today->copy()->subDays(25)->toDateString(),
                'desc' => 'Peşin Satış — Fiş #100',
                'lines' => [
                    ['code' => '100', 'debit' => 11800, 'credit' => 0],
                    ['code' => '600', 'debit' => 0, 'credit' => 10000],
                    ['code' => '391', 'debit' => 0, 'credit' => 1800],
                ],
            ],
            // Satış fişi 2 – kart
            [
                'type' => 'sale',
                'date' => $today->copy()->subDays(20)->toDateString(),
                'desc' => 'Kredi Kartı Satışı — Fiş #101',
                'lines' => [
                    ['code' => '102', 'debit' => 23600, 'credit' => 0],
                    ['code' => '600', 'debit' => 0, 'credit' => 20000],
                    ['code' => '391', 'debit' => 0, 'credit' => 3600],
                ],
            ],
            // Satın alma fişi
            [
                'type' => 'purchase',
                'date' => $today->copy()->subDays(18)->toDateString(),
                'desc' => 'Mal Alımı — Tedarikçi: ABC Ltd.',
                'lines' => [
                    ['code' => '153', 'debit' => 30000, 'credit' => 0],
                    ['code' => '191', 'debit' => 5400,  'credit' => 0],
                    ['code' => '320', 'debit' => 0, 'credit' => 35400],
                ],
            ],
            // Kira gideri
            [
                'type' => 'expense',
                'date' => $today->copy()->subDays(15)->toDateString(),
                'desc' => 'Aylık Kira Gideri',
                'lines' => [
                    ['code' => '770', 'debit' => 15000, 'credit' => 0],
                    ['code' => '100', 'debit' => 0, 'credit' => 15000],
                ],
            ],
            // Personel maaş fişi
            [
                'type' => 'payroll',
                'date' => $today->copy()->subDays(10)->toDateString(),
                'desc' => 'Personel Maaş Ödemesi — ' . $today->copy()->subDays(10)->format('F Y'),
                'lines' => [
                    ['code' => '720', 'debit' => 25000, 'credit' => 0],
                    ['code' => '360', 'debit' => 0, 'credit' => 4525],
                    ['code' => '100', 'debit' => 0, 'credit' => 20475],
                ],
            ],
            // Satış fişi 3
            [
                'type' => 'sale',
                'date' => $today->copy()->subDays(5)->toDateString(),
                'desc' => 'Karma Ödeme Satışı — Fiş #102',
                'lines' => [
                    ['code' => '100', 'debit' => 5900, 'credit' => 0],
                    ['code' => '102', 'debit' => 11800, 'credit' => 0],
                    ['code' => '600', 'debit' => 0, 'credit' => 15000],
                    ['code' => '391', 'debit' => 0, 'credit' => 2700],
                ],
            ],
            // Elektrik faturası
            [
                'type' => 'expense',
                'date' => $today->copy()->subDays(3)->toDateString(),
                'desc' => 'Elektrik Faturası',
                'lines' => [
                    ['code' => '770', 'debit' => 3200, 'credit' => 0],
                    ['code' => '191', 'debit' => 576,  'credit' => 0],
                    ['code' => '320', 'debit' => 0, 'credit' => 3776],
                ],
            ],
        ];

        foreach ($samples as $s) {
            // Hesaplar mevcut mu kontrol et
            $codes = array_column($s['lines'], 'code');
            $foundAccounts = AccountPlan::whereIn('code', $codes)->pluck('code')->toArray();
            $missing = array_diff($codes, $foundAccounts);

            // Eksik hesapları atla, satırları filtrele
            $lines = array_filter($s['lines'], fn($l) => !in_array($l['code'], $missing));
            if (empty($lines)) continue;

            // Dengeyi kontrol et
            $totalD = array_sum(array_column(array_values($lines), 'debit'));
            $totalC = array_sum(array_column(array_values($lines), 'credit'));
            if (abs($totalD - $totalC) > 0.01) continue;

            $entry = self::createEntry([
                'type'        => $s['type'],
                'date'        => $s['date'],
                'description' => $s['desc'],
                'branch_id'   => $branchId,
                'created_by'  => $userId,
                'is_posted'   => true,
            ], array_values($lines));

            if ($entry) $created[] = $entry;
        }

        return $created;
    }

    // ══════════════════════════════════════════════════════════════
    // YARDIMCI METODLAR
    // ══════════════════════════════════════════════════════════════

    /**
     * Gider türü adından muhasebe hesabını eşleştir.
     */
    private static function matchExpenseAccount(string $typeName): string
    {
        $typeName = mb_strtolower($typeName, 'UTF-8');

        $map = [
            'kira'      => '770',
            'elektrik'  => '770',
            'su'        => '770',
            'internet'  => '770',
            'telefon'   => '770',
            'maaş'      => '720',
            'prim'      => '720',
            'yakıt'     => '770',
            'fatura'    => '770',
            'vergi'     => '360',
            'amort'     => '257',
        ];

        foreach ($map as $keyword => $code) {
            if (str_contains($typeName, $keyword)) {
                if (AccountPlan::where('code', $code)->exists()) {
                    return $code;
                }
            }
        }

        // Varsayılan: Genel Yönetim Giderleri
        return AccountPlan::whereIn('code', ['770', '632', '630'])->first()?->code ?? '770';
    }

    /**
     * Yevmiye fişi ve satırlarını tek transaction içinde yarat.
     */
    private static function createEntry(array $header, array $lines): ?JournalEntry
    {
        try {
            return DB::transaction(function () use ($header, $lines) {
                $entry = JournalEntry::create([
                    'entry_no'       => JournalEntry::nextEntryNo(),
                    'date'           => $header['date'],
                    'description'    => $header['description'],
                    'type'           => $header['type'],
                    'reference_type' => $header['reference_type'] ?? null,
                    'reference_id'   => $header['reference_id'] ?? null,
                    'is_posted'      => $header['is_posted'] ?? false,
                    'branch_id'      => $header['branch_id'] ?? null,
                    'created_by'     => $header['created_by'] ?? null,
                ]);

                foreach ($lines as $i => $line) {
                    JournalEntryLine::create([
                        'journal_entry_id' => $entry->id,
                        'account_code'     => $line['code'],
                        'description'      => $line['desc'] ?? null,
                        'debit'            => (float)($line['debit']  ?? 0),
                        'credit'           => (float)($line['credit'] ?? 0),
                        'line_order'       => $i,
                    ]);
                }

                return $entry;
            });
        } catch (\Throwable $e) {
            \Log::warning('AccountingService: Fiş oluşturulamadı — ' . $e->getMessage());
            return null;
        }
    }
}
