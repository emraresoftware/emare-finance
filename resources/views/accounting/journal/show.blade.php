@extends('layouts.app')
@section('title', 'Fiş #' . $entry->entry_no)

@section('content')
<div class="max-w-4xl mx-auto space-y-5">

    {{-- Başlık --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('accounting.journal.index') }}" class="text-gray-400 hover:text-gray-600"><i class="fas fa-arrow-left"></i></a>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $entry->entry_no }}</h2>
                <p class="text-sm text-gray-500">{{ $entry->date->format('d.m.Y') }} · {{ $entry->getTypeLabel() }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200">
                <i class="fas fa-print mr-1"></i> Yazdır
            </button>
            @if(!$entry->is_posted)
            <form action="{{ route('accounting.journal.post', $entry) }}" method="POST" class="inline">
                @csrf
                <button type="submit"
                        onclick="return confirm('Bu fişi kesinleştirmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700">
                    <i class="fas fa-lock mr-1"></i> Kesinleştir
                </button>
            </form>
            <form action="{{ route('accounting.journal.destroy', $entry) }}" method="POST" class="inline">
                @csrf @method('DELETE')
                <button type="submit"
                        onclick="return confirm('Bu fişi silmek istediğinizden emin misiniz?')"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700">
                    <i class="fas fa-trash mr-1"></i> Sil
                </button>
            </form>
            @endif
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-sm text-green-700">
        <i class="fas fa-circle-check mr-2 text-green-500"></i>{{ session('success') }}
    </div>
    @endif

    {{-- Durum Banner --}}
    @if($entry->is_posted)
    <div class="bg-green-50 border border-green-200 rounded-xl px-5 py-3 flex items-center gap-3">
        <i class="fas fa-lock text-green-600"></i>
        <p class="text-sm text-green-800 font-medium">Bu fiş kesinleşmiştir — değiştirilemez ve silinemez.</p>
        @if($entry->is_posted)
        <p class="text-xs text-green-600 ml-auto">{{ $entry->creator?->name }} · {{ $entry->updated_at->format('d.m.Y H:i') }}</p>
        @endif
    </div>
    @else
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl px-5 py-3 flex items-center gap-2">
        <i class="fas fa-pencil text-yellow-600"></i>
        <p class="text-sm text-yellow-800 font-medium">Taslak — henüz kesinleştirilmedi.</p>
    </div>
    @endif

    {{-- Detaylar --}}
    <div class="bg-white rounded-xl border shadow-sm">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-0 divide-x divide-y border-b">
            <div class="px-5 py-4">
                <p class="text-xs text-gray-500 mb-1">Fiş No</p>
                <p class="font-mono font-bold text-gray-800">{{ $entry->entry_no }}</p>
            </div>
            <div class="px-5 py-4">
                <p class="text-xs text-gray-500 mb-1">Tarih</p>
                <p class="font-semibold text-gray-800">{{ $entry->date->format('d.m.Y') }}</p>
            </div>
            <div class="px-5 py-4">
                <p class="text-xs text-gray-500 mb-1">Tür</p>
                <p class="font-semibold text-gray-800">{{ $entry->getTypeLabel() }}</p>
            </div>
            <div class="px-5 py-4">
                <p class="text-xs text-gray-500 mb-1">Oluşturan</p>
                <p class="font-semibold text-gray-800">{{ $entry->creator?->name ?? '-' }}</p>
            </div>
        </div>
        <div class="px-5 py-4 border-b">
            <p class="text-xs text-gray-500 mb-1">Açıklama</p>
            <p class="text-gray-800">{{ $entry->description }}</p>
        </div>

        {{-- Satırlar --}}
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase w-8">#</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase w-28">Hesap Kodu</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Hesap Adı</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase w-36">Borç</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase w-36">Alacak</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($entry->lines as $line)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2.5 text-gray-400 text-xs">{{ $loop->iteration }}</td>
                    <td class="px-4 py-2.5 text-indigo-700 font-mono font-semibold text-xs">{{ $line->account_code }}</td>
                    <td class="px-4 py-2.5 text-gray-800">{{ $line->account?->name ?? '-' }}</td>
                    <td class="px-4 py-2.5 text-right font-mono text-xs tabular-nums whitespace-nowrap {{ $line->debit > 0 ? 'text-blue-700 font-semibold' : 'text-gray-300' }}">
                        {{ $line->debit > 0 ? number_format($line->debit, 2, ',', '.') : '—' }}
                    </td>
                    <td class="px-4 py-2.5 text-right font-mono text-xs tabular-nums whitespace-nowrap {{ $line->credit > 0 ? 'text-red-700 font-semibold' : 'text-gray-300' }}">
                        {{ $line->credit > 0 ? number_format($line->credit, 2, ',', '.') : '—' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50 border-t-2 border-gray-300">
                <tr>
                    <td colspan="3" class="px-4 py-3 font-bold text-gray-700 text-sm">TOPLAM</td>
                    <td class="px-4 py-3 text-right font-bold text-blue-700 font-mono tabular-nums whitespace-nowrap">
                        {{ number_format($entry->total_debit, 2, ',', '.') }} ₺
                    </td>
                    <td class="px-4 py-3 text-right font-bold text-red-700 font-mono tabular-nums whitespace-nowrap">
                        {{ number_format($entry->total_credit, 2, ',', '.') }} ₺
                    </td>
                </tr>
            </tfoot>
        </table>

        {{-- Denge Kontrolü --}}
        @php $diff = abs($entry->total_debit - $entry->total_credit); @endphp
        @if($diff < 0.01)
        <div class="px-5 py-3 border-t flex items-center gap-2 text-sm text-green-700">
            <i class="fas fa-circle-check text-green-500"></i> Muhasebe dengesi sağlanmış — Borç = Alacak
        </div>
        @else
        <div class="px-5 py-3 border-t flex items-center gap-2 text-sm text-red-600">
            <i class="fas fa-triangle-exclamation text-red-500"></i>
            DİKKAT: Borç/Alacak dengesi yok! Fark: {{ number_format($diff, 2, ',', '.') }} ₺
        </div>
        @endif
    </div>

    {{-- QR Kod Paneli --}}
    <div class="bg-white rounded-xl border shadow-sm p-5 flex items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div id="entry-qr" class="border-2 border-gray-200 rounded-xl p-2 bg-white"></div>
            <div>
                <p class="font-semibold text-gray-800">{{ $entry->entry_no }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Bu fişi telefonunuzla taramak için QR kodu okutun.</p>
                <a href="{{ route('accounting.scan') }}"
                   class="inline-flex items-center gap-1 mt-2 text-xs text-indigo-600 hover:underline">
                    <i class="fas fa-qrcode"></i> QR Tarama Sayfası
                </a>
            </div>
        </div>
        <button onclick="window.print()"
                class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50">
            <i class="fas fa-print mr-1"></i> Yazdır
        </button>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
new QRCode(document.getElementById("entry-qr"), {
    text: "{{ $entry->entry_no }}",
    width: 100, height: 100,
    colorDark: "#1e1b4b",
    colorLight: "#ffffff",
    correctLevel: QRCode.CorrectLevel.M
});
</script>
