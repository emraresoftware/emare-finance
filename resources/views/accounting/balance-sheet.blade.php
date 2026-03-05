@extends('layouts.app')
@section('title', 'Bilanço')

@section('content')
<div class="space-y-5">

    {{-- Başlık + Filtre --}}
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('accounting.dashboard') }}" class="text-gray-400 hover:text-gray-600"><i class="fas fa-arrow-left"></i></a>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Bilanço</h2>
                <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($asOf)->format('d.m.Y') }} tarihi itibarıyla</p>
            </div>
        </div>
        <form method="GET" class="flex items-end gap-2">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Tarih İtibarıyla</label>
                <input type="date" name="date" value="{{ $asOf }}"
                       class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">Göster</button>
            <a href="?date={{ $asOf }}&print=1" onclick="window.print();return false;"
               class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-200">
               <i class="fas fa-print mr-1"></i> Yazdır
            </a>
        </form>
    </div>

    {{-- Bilanço Tablosu --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- AKTİF --}}
        <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
            <div class="bg-blue-600 px-5 py-3">
                <h3 class="font-bold text-white text-sm tracking-wide">AKTİF (VARLIKLAR)</h3>
            </div>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-gray-100">
                    @php $prevLevel = null; @endphp
                    @foreach(($data['asset'] ?? []) as $row)
                        <tr class="{{ $row['level'] == 1 ? 'bg-blue-50 font-semibold' : ($row['level'] == 2 ? 'bg-gray-50 font-medium' : '') }} hover:bg-gray-50">
                            <td class="px-4 py-2 text-gray-600 font-mono text-xs w-20">{{ $row['code'] }}</td>
                            <td class="px-2 py-2 text-gray-800" style="padding-left: {{ ($row['level'] - 1) * 16 + 8 }}px">
                                {{ $row['name'] }}
                            </td>
                            <td class="px-4 py-2 text-right text-gray-900 font-medium whitespace-nowrap">
                                {{ number_format($row['balance'], 2, ',', '.') }} ₺
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-blue-600">
                        <td colspan="2" class="px-4 py-3 font-bold text-white text-sm">TOPLAM AKTİF</td>
                        <td class="px-4 py-3 text-right font-bold text-white text-sm whitespace-nowrap">
                            {{ number_format($totalAssets, 2, ',', '.') }} ₺
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- PASİF --}}
        <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
            <div class="bg-red-600 px-5 py-3">
                <h3 class="font-bold text-white text-sm tracking-wide">PASİF (KAYNAKLAR)</h3>
            </div>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-gray-100">
                    {{-- Yükümlülükler --}}
                    @if(!empty($data['liability']))
                    <tr class="bg-red-50">
                        <td colspan="3" class="px-4 py-2 text-xs font-bold text-red-700 uppercase tracking-wider">Yabancı Kaynaklar</td>
                    </tr>
                    @foreach(($data['liability'] ?? []) as $row)
                        <tr class="{{ $row['level'] == 1 ? 'bg-red-50 font-semibold' : ($row['level'] == 2 ? 'bg-gray-50 font-medium' : '') }} hover:bg-gray-50">
                            <td class="px-4 py-2 text-gray-600 font-mono text-xs w-20">{{ $row['code'] }}</td>
                            <td class="px-2 py-2 text-gray-800" style="padding-left: {{ ($row['level'] - 1) * 16 + 8 }}px">
                                {{ $row['name'] }}
                            </td>
                            <td class="px-4 py-2 text-right text-gray-900 font-medium whitespace-nowrap">
                                {{ number_format($row['balance'], 2, ',', '.') }} ₺
                            </td>
                        </tr>
                    @endforeach
                    @endif

                    {{-- Öz Kaynaklar --}}
                    @if(!empty($data['equity']))
                    <tr class="bg-green-50">
                        <td colspan="3" class="px-4 py-2 text-xs font-bold text-green-700 uppercase tracking-wider">Öz Kaynaklar</td>
                    </tr>
                    @foreach(($data['equity'] ?? []) as $row)
                        <tr class="{{ $row['level'] == 1 ? 'bg-green-50 font-semibold' : ($row['level'] == 2 ? 'bg-gray-50 font-medium' : '') }} hover:bg-gray-50">
                            <td class="px-4 py-2 text-gray-600 font-mono text-xs w-20">{{ $row['code'] }}</td>
                            <td class="px-2 py-2 text-gray-800" style="padding-left: {{ ($row['level'] - 1) * 16 + 8 }}px">
                                {{ $row['name'] }}
                            </td>
                            <td class="px-4 py-2 text-right text-gray-900 font-medium whitespace-nowrap">
                                {{ number_format($row['balance'], 2, ',', '.') }} ₺
                            </td>
                        </tr>
                    @endforeach
                    @endif
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-gray-300">
                        <td colspan="2" class="px-4 py-2 text-sm font-semibold text-gray-600">Toplam Yabancı Kaynaklar</td>
                        <td class="px-4 py-2 text-right font-semibold text-gray-700 text-sm whitespace-nowrap">{{ number_format($totalLiabilities, 2, ',', '.') }} ₺</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="px-4 py-2 text-sm font-semibold text-gray-600">Toplam Öz Kaynaklar</td>
                        <td class="px-4 py-2 text-right font-semibold text-gray-700 text-sm whitespace-nowrap">{{ number_format($totalEquity, 2, ',', '.') }} ₺</td>
                    </tr>
                    <tr class="bg-red-600">
                        <td colspan="2" class="px-4 py-3 font-bold text-white text-sm">TOPLAM PASİF</td>
                        <td class="px-4 py-3 text-right font-bold text-white text-sm whitespace-nowrap">
                            {{ number_format($totalPasif, 2, ',', '.') }} ₺
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>

    {{-- Denge Kontrolü --}}
    @php $diff = abs($totalAssets - $totalPasif); @endphp
    @if($diff > 0.01)
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-center gap-3">
        <i class="fas fa-triangle-exclamation text-red-500"></i>
        <p class="text-sm text-red-700">
            <strong>Bilanço dengede değil!</strong> Aktif ve Pasif arasında
            <strong>{{ number_format($diff, 2, ',', '.') }} ₺</strong> fark var.
            Yevmiye fişlerini kontrol edin.
        </p>
    </div>
    @else
    <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-3">
        <i class="fas fa-circle-check text-green-500"></i>
        <p class="text-sm text-green-700"><strong>Bilanço dengede.</strong> Aktif = Pasif = ₺{{ number_format($totalAssets, 2, ',', '.') }}</p>
    </div>
    @endif

</div>
@endsection
