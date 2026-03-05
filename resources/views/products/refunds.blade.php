@extends('layouts.app')
@section('title', 'İade İşlemleri')

@section('content')
{{-- İstatistik Kartları --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">İade Edilen Satış</p>
        <p class="text-xl font-bold text-gray-900">{{ number_format($stats['total_refunded_sales']) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Toplam İade Tutarı</p>
        <p class="text-xl font-bold text-red-600">₺{{ number_format($stats['total_refunded_amount'], 2, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">İade Edilen Ürün</p>
        <p class="text-xl font-bold text-orange-600">{{ number_format($stats['total_refunded_items']) }}</p>
        <p class="text-xs text-gray-400">adet</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Bu Ay İade</p>
        <p class="text-xl font-bold text-purple-600">₺{{ number_format($stats['this_month'], 2, ',', '.') }}</p>
    </div>
</div>

{{-- Filtreler --}}
<div class="bg-white rounded-xl shadow-sm border p-4 mb-6">
    <form method="GET" class="flex items-end gap-4 flex-wrap">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Başlangıç</label>
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Bitiş</label>
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
        </div>
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs text-gray-500 mb-1">Arama</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Ürün adı, barkod, fiş no..."
                   class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border focus:ring-2 focus:ring-indigo-500">
        </div>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 transition">
            <i class="fas fa-search mr-1"></i> Filtrele
        </button>
        <a href="{{ route('products.refunds') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm hover:bg-gray-200 transition">Temizle</a>
    </form>
</div>

{{-- İade Edilen Ürünler Tablosu --}}
<div class="bg-white rounded-xl shadow-sm border overflow-hidden mb-6">
    <div class="px-6 py-4 border-b">
        <h3 class="font-semibold text-gray-800"><i class="fas fa-undo text-red-500 mr-2"></i>İade Edilen Ürünler</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fiş No</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ürün</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barkod</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Miktar</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Birim Fiyat</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Toplam</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($refunds as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $item->sale?->sold_at?->format('d.m.Y H:i') ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm">
                            @if($item->sale)
                                <a href="{{ route('sales.show', $item->sale_id) }}" class="text-indigo-600 hover:underline">{{ $item->sale->receipt_no }}</a>
                            @else - @endif
                        </td>
                        <td class="px-4 py-3 text-sm font-medium">
                            @if($item->product)
                                <a href="{{ route('products.show', $item->product) }}" class="text-indigo-600 hover:underline">{{ $item->product_name }}</a>
                            @else
                                {{ $item->product_name }}
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500 font-mono text-xs">{{ $item->barcode ?: '-' }}</td>
                        <td class="px-4 py-3 text-sm text-right text-red-600 font-medium">{{ $item->quantity }}</td>
                        <td class="px-4 py-3 text-sm text-right">₺{{ number_format($item->unit_price, 2, ',', '.') }}</td>
                        <td class="px-4 py-3 text-sm text-right font-medium text-red-600">₺{{ number_format($item->total, 2, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                            <i class="fas fa-undo-alt text-4xl mb-3"></i>
                            <p class="text-lg">Henüz iade kaydı bulunmuyor.</p>
                            <p class="text-sm mt-1">İade işlemleri yapıldığında burada listelenecektir.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($refunds->hasPages())
    <div class="px-4 py-3 border-t">
        {{ $refunds->links() }}
    </div>
    @endif
</div>

{{-- İade Stok Hareketleri --}}
@if($refundMovements->count() > 0)
<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <div class="px-6 py-4 border-b">
        <h3 class="font-semibold text-gray-800"><i class="fas fa-exchange-alt text-yellow-500 mr-2"></i>İade Stok Hareketleri</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ürün</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Açıklama</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Miktar</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Toplam</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($refundMovements as $move)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $move->movement_date?->format('d.m.Y H:i') ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm font-medium">{{ $move->product_name ?: ($move->product?->name ?? '-') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $move->note ?: '-' }}</td>
                        <td class="px-4 py-3 text-sm text-right text-green-600 font-medium">+{{ $move->quantity }}</td>
                        <td class="px-4 py-3 text-sm text-right">₺{{ number_format($move->total, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
