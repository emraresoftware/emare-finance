@extends('layouts.app')
@section('title', 'Yeni Yevmiye Fişi')

@section('content')
<div class="max-w-5xl mx-auto space-y-5" x-data="journalForm()">

    {{-- Başlık --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('accounting.journal.index') }}" class="text-gray-400 hover:text-gray-600"><i class="fas fa-arrow-left"></i></a>
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Yeni Yevmiye Fişi</h2>
            <p class="text-sm text-gray-500">Borç ve alacak eşit olmalıdır</p>
        </div>
    </div>

    {{-- Hatalar --}}
    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-xl p-4">
        <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('accounting.journal.store') }}" method="POST" id="journalForm">
        @csrf

        {{-- Üst Bilgiler --}}
        <div class="bg-white rounded-xl border shadow-sm p-5 grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Tarih *</label>
                <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Fiş Türü</label>
                <select name="type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    @foreach(['manual'=>'Manuel','opening'=>'Açılış','sale'=>'Satış','purchase'=>'Alım','expense'=>'Gider','income'=>'Gelir','payroll'=>'Maaş','adjustment'=>'Düzeltme','closing'=>'Kapanış'] as $v => $l)
                    <option value="{{ $v }}" {{ old('type', 'manual') == $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">Açıklama *</label>
                <input type="text" name="description" value="{{ old('description') }}" required
                       placeholder="Fiş açıklaması..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        {{-- Satırlar --}}
        <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">Fiş Satırları</h3>
                <div class="flex items-center gap-4 text-sm">
                    <span class="text-gray-600">
                        Borç: <strong class="text-blue-700" x-text="'₺' + formatNum(totalDebit)"></strong>
                    </span>
                    <span class="text-gray-600">
                        Alacak: <strong class="text-red-700" x-text="'₺' + formatNum(totalCredit)"></strong>
                    </span>
                    <span :class="isBalanced ? 'text-green-700 bg-green-50' : 'text-red-700 bg-red-50'"
                          class="px-3 py-1 rounded-full text-xs font-medium">
                        <span x-show="isBalanced"><i class="fas fa-circle-check mr-1"></i> Dengede</span>
                        <span x-show="!isBalanced">
                            <i class="fas fa-triangle-exclamation mr-1"></i>
                            Fark: ₺<span x-text="formatNum(Math.abs(totalDebit - totalCredit))"></span>
                        </span>
                    </span>
                </div>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs text-gray-500 w-8">#</th>
                        <th class="px-3 py-2 text-left text-xs text-gray-500 w-36">Hesap Kodu</th>
                        <th class="px-3 py-2 text-left text-xs text-gray-500">Hesap Adı</th>
                        <th class="px-3 py-2 text-right text-xs text-gray-500 w-36">Borç (₺)</th>
                        <th class="px-3 py-2 text-right text-xs text-gray-500 w-36">Alacak (₺)</th>
                        <th class="px-3 py-2 w-10"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(line, idx) in lines" :key="idx">
                        <tr class="border-b hover:bg-gray-50/50">
                            <td class="px-3 py-2 text-gray-400 text-xs" x-text="idx + 1"></td>
                            <td class="px-2 py-1.5">
                                <input type="text"
                                       :name="'lines[' + idx + '][account_code]'"
                                       x-model="line.account_code"
                                       @input.debounce.300ms="lookupAccount(idx)"
                                       @blur="lookupAccount(idx)"
                                       :placeholder="'120, 100...'"
                                       class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm font-mono focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400">
                            </td>
                            <td class="px-2 py-1.5">
                                <input type="text"
                                       :name="'lines[' + idx + '][account_name]'"
                                       x-model="line.account_name"
                                       readonly
                                       class="w-full bg-gray-50 border border-gray-200 rounded px-2 py-1.5 text-sm text-gray-600 cursor-default">
                            </td>
                            <td class="px-2 py-1.5">
                                <input type="number" step="0.01" min="0"
                                       :name="'lines[' + idx + '][debit]'"
                                       x-model="line.debit"
                                       @input="updateTotals"
                                       @focus="if(line.debit == '0') line.debit = ''"
                                       @blur="if(line.debit == '') line.debit = '0'"
                                       class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm text-right tabular-nums focus:ring-2 focus:ring-indigo-400">
                            </td>
                            <td class="px-2 py-1.5">
                                <input type="number" step="0.01" min="0"
                                       :name="'lines[' + idx + '][credit]'"
                                       x-model="line.credit"
                                       @input="updateTotals"
                                       @focus="if(line.credit == '0') line.credit = ''"
                                       @blur="if(line.credit == '') line.credit = '0'"
                                       class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm text-right tabular-nums focus:ring-2 focus:ring-indigo-400">
                            </td>
                            <td class="px-2 py-1.5 text-center">
                                <button type="button" @click="removeLine(idx)"
                                        class="text-gray-300 hover:text-red-500 transition"
                                        x-show="lines.length > 2">
                                    <i class="fas fa-times"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
            <div class="px-5 py-3 border-t bg-gray-50 flex items-center justify-between">
                <button type="button" @click="addLine"
                        class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                    <i class="fas fa-plus mr-1"></i> Satır Ekle
                </button>
                <span class="text-xs text-gray-400" x-text="lines.length + ' satır'"></span>
            </div>
        </div>

        {{-- Kaydet Butonları --}}
        <div class="flex items-center gap-3 justify-end">
            <a href="{{ route('accounting.journal.index') }}"
               class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50">
                İptal
            </a>
            <button type="submit" name="post_entry" value="0"
                    class="px-5 py-2.5 bg-yellow-500 text-white rounded-lg text-sm font-medium hover:bg-yellow-600 disabled:opacity-50">
                <i class="fas fa-floppy-disk mr-1"></i> Taslak Kaydet
            </button>
            <button type="submit" name="post_entry" value="1"
                    :disabled="!isBalanced"
                    class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fas fa-lock mr-1"></i> Kaydet ve Kesinleştir
            </button>
        </div>

    </form>
</div>

<script>
// Hesap listesi (arama için)
const accountList = @json($accounts->map(fn($a) => ['code' => $a->code, 'name' => $a->name]));

function journalForm() {
    return {
        lines: [
            { account_code: '', account_name: '', debit: '0', credit: '0' },
            { account_code: '', account_name: '', debit: '0', credit: '0' },
        ],
        totalDebit: 0,
        totalCredit: 0,
        get isBalanced() {
            return this.totalDebit > 0 && Math.abs(this.totalDebit - this.totalCredit) < 0.01;
        },
        addLine() {
            this.lines.push({ account_code: '', account_name: '', debit: '0', credit: '0' });
        },
        removeLine(idx) {
            if (this.lines.length > 2) {
                this.lines.splice(idx, 1);
                this.updateTotals();
            }
        },
        lookupAccount(idx) {
            const code = this.lines[idx].account_code.trim();
            if (!code) return;
            const found = accountList.find(a => a.code === code);
            this.lines[idx].account_name = found ? found.name : '⚠ Hesap bulunamadı';
        },
        updateTotals() {
            this.totalDebit  = this.lines.reduce((s, l) => s + (parseFloat(l.debit)  || 0), 0);
            this.totalCredit = this.lines.reduce((s, l) => s + (parseFloat(l.credit) || 0), 0);
        },
        formatNum(n) {
            return n.toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
    }
}
</script>
@endsection
