@extends('layouts.app')
@section('title', 'Mizan')

@section('content')
<div class="space-y-5">

    {{-- Başlık + Filtre --}}
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('accounting.dashboard') }}" class="text-gray-400 hover:text-gray-600"><i class="fas fa-arrow-left"></i></a>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Mizan</h2>
                <p class="text-sm text-gray-500">Hesap bakiyeleri özeti</p>
            </div>
        </div>
        <form method="GET" class="flex items-end flex-wrap gap-2">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Başlangıç</label>
                <input type="date" name="start" value="{{ $startDate }}"
                       class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Bitiş</label>
                <input type="date" name="end" value="{{ $endDate }}"
                       class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">Göster</button>
            <a href="javascript:window.print()" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-200">
               <i class="fas fa-print mr-1"></i> Yazdır
            </a>
        </form>
    </div>

    {{-- Kontrol --}}
    @php
        $balanced = abs($totalDebit - $totalCredit) < 0.01;
    @endphp
    @if($balanced)
    <div class="bg-green-50 border border-green-200 rounded-xl p-3 flex items-center gap-2 text-sm text-green-700">
        <i class="fas fa-circle-check text-green-500"></i>
        Mizan dengede — Toplam Borç = Toplam Alacak = ₺{{ number_format($totalDebit, 2, ',', '.') }}
    </div>
    @else
    <div class="bg-red-50 border border-red-200 rounded-xl p-3 flex items-center gap-2 text-sm text-red-700">
        <i class="fas fa-triangle-exclamation text-red-500"></i>
        Mizan dengede değil! Fark: ₺{{ number_format(abs($totalDebit - $totalCredit), 2, ',', '.') }}
    </div>
    @endif

    {{-- Tablo --}}
    <div class="bg-white rounded-xl border shadow-sm overflow-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase w-24">Hesap No</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Hesap Adı</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Borç (Dr)</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Alacak (Cr)</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Bakiye</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Tür</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($accounts as $row)
                @php
                    $typeColors = [
                        'asset'     => 'bg-blue-50 text-blue-700',
                        'liability' => 'bg-red-50 text-red-700',
                        'equity'    => 'bg-green-50 text-green-700',
                        'revenue'   => 'bg-emerald-50 text-emerald-700',
                        'cost'      => 'bg-orange-50 text-orange-700',
                        'expense'   => 'bg-yellow-50 text-yellow-700',
                    ];
                    $typeLabels = [
                        'asset'     => 'Varlık',
                        'liability' => 'Yükümlülük',
                        'equity'    => 'Özkaynak',
                        'revenue'   => 'Gelir',
                        'cost'      => 'Maliyet',
                        'expense'   => 'Gider',
                    ];
                    $typeColor = $typeColors[$row['type']] ?? 'bg-gray-50 text-gray-600';
                    $typeLabel = $typeLabels[$row['type']] ?? $row['type'];
                    $balance = $row['debit'] - $row['credit'];
                @endphp
                <tr class="hover:bg-gray-50 {{ $row['level'] == 1 ? 'font-semibold' : '' }}">
                    <td class="px-4 py-2 text-gray-600 font-mono text-xs">{{ $row['code'] }}</td>
                    <td class="px-4 py-2 text-gray-800" style="padding-left: {{ ($row['level'] - 1) * 12 + 16 }}px">
                        {{ $row['name'] }}
                    </td>
                    <td class="px-4 py-2 text-right text-gray-700 font-mono text-xs tabular-nums whitespace-nowrap">
                        {{ $row['debit'] > 0 ? number_format($row['debit'], 2, ',', '.') : '—' }}
                    </td>
                    <td class="px-4 py-2 text-right text-gray-700 font-mono text-xs tabular-nums whitespace-nowrap">
                        {{ $row['credit'] > 0 ? number_format($row['credit'], 2, ',', '.') : '—' }}
                    </td>
                    <td class="px-4 py-2 text-right font-mono text-xs tabular-nums whitespace-nowrap {{ $balance >= 0 ? 'text-indigo-700' : 'text-red-600' }}">
                        {{ number_format($balance, 2, ',', '.') }}
                    </td>
                    <td class="px-4 py-2 text-center">
                        <span class="inline-block px-2 py-0.5 rounded text-xs font-medium {{ $typeColor }}">{{ $typeLabel }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-10 text-center text-gray-400">
                        <i class="fas fa-inbox text-3xl block mb-2"></i>Bu tarih aralığında kayıt yok.
                    </td>
                </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-800 border-t-2 border-gray-700">
                <tr>
                    <td colspan="2" class="px-4 py-3 font-bold text-white text-sm">GENEL TOPLAM</td>
                    <td class="px-4 py-3 text-right font-bold text-white text-sm font-mono tabular-nums whitespace-nowrap">
                        {{ number_format($totalDebit, 2, ',', '.') }} ₺
                    </td>
                    <td class="px-4 py-3 text-right font-bold text-white text-sm font-mono tabular-nums whitespace-nowrap">
                        {{ number_format($totalCredit, 2, ',', '.') }} ₺
                    </td>
                    <td class="px-4 py-3 text-right font-bold {{ $balanced ? 'text-green-400' : 'text-red-400' }} text-sm font-mono tabular-nums whitespace-nowrap">
                        {{ number_format($totalDebit - $totalCredit, 2, ',', '.') }} ₺
                    </td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>

</div>
@endsection
