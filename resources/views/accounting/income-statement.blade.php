@extends('layouts.app')
@section('title', 'Gelir Tablosu')

@section('content')
<div class="space-y-5 max-w-4xl mx-auto">

    {{-- Başlık + Filtre --}}
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('accounting.dashboard') }}" class="text-gray-400 hover:text-gray-600"><i class="fas fa-arrow-left"></i></a>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Gelir Tablosu</h2>
                <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($startDate)->format('d.m.Y') }} – {{ \Carbon\Carbon::parse($endDate)->format('d.m.Y') }}</p>
            </div>
        </div>
        <form method="GET" class="flex items-end gap-2">
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

    {{-- Özet Kartlar --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-center">
            <div class="text-lg font-bold text-green-700">₺{{ number_format($grossRevenue, 2, ',', '.') }}</div>
            <div class="text-xs text-green-600 mt-1">Brüt Satışlar</div>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-center">
            <div class="text-lg font-bold text-red-700">₺{{ number_format($totalCost, 2, ',', '.') }}</div>
            <div class="text-xs text-red-600 mt-1">Satış Maliyeti</div>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-center">
            <div class="text-lg font-bold text-blue-700">₺{{ number_format($grossProfit, 2, ',', '.') }}</div>
            <div class="text-xs text-blue-600 mt-1">Brüt Kâr</div>
        </div>
        <div class="{{ $netProfit >= 0 ? 'bg-indigo-50 border-indigo-200' : 'bg-orange-50 border-orange-200' }} border rounded-xl p-4 text-center">
            <div class="text-lg font-bold {{ $netProfit >= 0 ? 'text-indigo-700' : 'text-orange-700' }}">
                ₺{{ number_format(abs($netProfit), 2, ',', '.') }}
            </div>
            <div class="text-xs {{ $netProfit >= 0 ? 'text-indigo-600' : 'text-orange-600' }} mt-1">
                {{ $netProfit >= 0 ? 'Net Kâr' : 'Net Zarar' }}
            </div>
        </div>
    </div>

    {{-- Tablo --}}
    <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase w-24">Kod</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Hesap Adı</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Tutar</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">

                {{-- Brüt Satışlar --}}
                <tr class="bg-green-50">
                    <td colspan="3" class="px-4 py-2 text-xs font-bold text-green-700 uppercase tracking-wider">
                        <i class="fas fa-arrow-trend-up mr-2"></i>Brüt Satışlar
                    </td>
                </tr>
                @foreach($revenues as $row)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 text-gray-500 font-mono text-xs">{{ $row['code'] }}</td>
                    <td class="px-4 py-2 text-gray-800" style="padding-left: {{ ($row['level'] - 1) * 16 + 16 }}px">{{ $row['name'] }}</td>
                    <td class="px-4 py-2 text-right text-green-700 font-medium whitespace-nowrap">{{ number_format($row['amount'], 2, ',', '.') }} ₺</td>
                </tr>
                @endforeach
                <tr class="bg-green-100 font-semibold">
                    <td colspan="2" class="px-4 py-2.5 text-green-800">Brüt Satışlar Toplamı</td>
                    <td class="px-4 py-2.5 text-right text-green-800 whitespace-nowrap">{{ number_format($grossRevenue, 2, ',', '.') }} ₺</td>
                </tr>

                {{-- Satış Maliyeti --}}
                <tr class="bg-red-50">
                    <td colspan="3" class="px-4 py-2 text-xs font-bold text-red-700 uppercase tracking-wider">
                        <i class="fas fa-boxes-stacked mr-2"></i>Satışların Maliyeti
                    </td>
                </tr>
                @foreach($costs as $row)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 text-gray-500 font-mono text-xs">{{ $row['code'] }}</td>
                    <td class="px-4 py-2 text-gray-800" style="padding-left: {{ ($row['level'] - 1) * 16 + 16 }}px">{{ $row['name'] }}</td>
                    <td class="px-4 py-2 text-right text-red-700 font-medium whitespace-nowrap">{{ number_format($row['amount'], 2, ',', '.') }} ₺</td>
                </tr>
                @endforeach
                <tr class="bg-red-100 font-semibold">
                    <td colspan="2" class="px-4 py-2.5 text-red-800">Satış Maliyeti Toplamı</td>
                    <td class="px-4 py-2.5 text-right text-red-800 whitespace-nowrap">{{ number_format($totalCost, 2, ',', '.') }} ₺</td>
                </tr>

                {{-- Brüt Kâr --}}
                <tr class="bg-blue-100 font-bold border-t-2 border-blue-300">
                    <td colspan="2" class="px-4 py-3 text-blue-900 text-sm">BRÜT KÂR</td>
                    <td class="px-4 py-3 text-right text-blue-900 text-sm whitespace-nowrap">{{ number_format($grossProfit, 2, ',', '.') }} ₺</td>
                </tr>

                {{-- Faaliyet Giderleri --}}
                <tr class="bg-orange-50">
                    <td colspan="3" class="px-4 py-2 text-xs font-bold text-orange-700 uppercase tracking-wider">
                        <i class="fas fa-minus mr-2"></i>Faaliyet Giderleri
                    </td>
                </tr>
                @foreach($expenses as $row)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 text-gray-500 font-mono text-xs">{{ $row['code'] }}</td>
                    <td class="px-4 py-2 text-gray-800" style="padding-left: {{ ($row['level'] - 1) * 16 + 16 }}px">{{ $row['name'] }}</td>
                    <td class="px-4 py-2 text-right text-orange-700 font-medium whitespace-nowrap">{{ number_format($row['amount'], 2, ',', '.') }} ₺</td>
                </tr>
                @endforeach
                <tr class="bg-orange-100 font-semibold">
                    <td colspan="2" class="px-4 py-2.5 text-orange-800">Faaliyet Giderleri Toplamı</td>
                    <td class="px-4 py-2.5 text-right text-orange-800 whitespace-nowrap">{{ number_format($totalExpense, 2, ',', '.') }} ₺</td>
                </tr>

                {{-- Net Kâr/Zarar --}}
                <tr class="{{ $netProfit >= 0 ? 'bg-indigo-600' : 'bg-red-700' }} font-bold border-t-2">
                    <td colspan="2" class="px-4 py-4 text-white text-sm">
                        {{ $netProfit >= 0 ? 'DÖNEM NET KÂRI' : 'DÖNEM NET ZARARI' }}
                    </td>
                    <td class="px-4 py-4 text-right text-white text-sm whitespace-nowrap">
                        {{ number_format(abs($netProfit), 2, ',', '.') }} ₺
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</div>
@endsection
