@extends('layouts.app')
@section('title', 'Ürün Raporu - En Çok Satanlar')

@section('content')
<div class="bg-white rounded-xl shadow-sm p-4 mb-6 border">
    <form method="GET" class="flex flex-wrap items-end gap-4">
        <div><label class="block text-xs text-gray-500 mb-1">Başlangıç</label>
            <input type="date" name="start_date" value="{{ $startDate }}" class="border rounded-lg px-3 py-2 text-sm"></div>
        <div><label class="block text-xs text-gray-500 mb-1">Bitiş</label>
            <input type="date" name="end_date" value="{{ $endDate }}" class="border rounded-lg px-3 py-2 text-sm"></div>
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm">Raporla</button>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50"><tr>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ürün</th>
            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Satış Adedi</th>
            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Satış Sayısı</th>
            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Toplam Gelir</th>
        </tr></thead>
        <tbody class="divide-y">
            @foreach($topProducts as $i => $product)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-400">{{ $i + 1 }}</td>
                    <td class="px-4 py-3 font-medium">{{ $product->product_name }}</td>
                    <td class="px-4 py-3 text-right">{{ number_format($product->total_quantity) }}</td>
                    <td class="px-4 py-3 text-right">{{ number_format($product->sale_count) }}</td>
                    <td class="px-4 py-3 text-right font-semibold text-green-600">₺{{ number_format($product->total_revenue, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
