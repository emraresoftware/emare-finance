@extends('layouts.app')
@section('title', 'Stok Sayım Detayı')

@section('content')
<div class="mb-4">
    <a href="{{ route('stock.counts') }}" class="text-sm text-indigo-600"><i class="fas fa-arrow-left mr-1"></i> Sayımlara Dön</a>
</div>

{{-- Fark Özeti --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Toplam Ürün</p>
        <p class="text-xl font-bold">{{ $diffStats['total_items'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Eşleşen</p>
        <p class="text-xl font-bold text-green-600">{{ $diffStats['matched'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Fazla</p>
        <p class="text-xl font-bold text-blue-600">{{ $diffStats['surplus'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Eksik</p>
        <p class="text-xl font-bold text-red-600">{{ $diffStats['deficit'] }}</p>
    </div>
</div>

{{-- Sayım Bilgileri --}}
<div class="bg-white rounded-xl shadow-sm border p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-bold text-gray-800"><i class="fas fa-clipboard-check mr-1 text-indigo-500"></i> Stok Sayımı #{{ $stockCount->id }}</h2>
        <button onclick="window.print()" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm hover:bg-gray-200">
            <i class="fas fa-print mr-1"></i> Yazdır
        </button>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div><span class="text-sm text-gray-500">Şube:</span><p class="font-medium">{{ $stockCount->branch?->name ?? '-' }}</p></div>
        <div><span class="text-sm text-gray-500">Durum:</span>
            <p><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $stockCount->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                {{ $stockCount->status === 'completed' ? 'Tamamlandı' : 'Devam Ediyor' }}
            </span></p>
        </div>
        <div><span class="text-sm text-gray-500">Sayım Tarihi:</span><p class="font-medium">{{ $stockCount->counted_at ? \Carbon\Carbon::parse($stockCount->counted_at)->format('d.m.Y') : '-' }}</p></div>
        <div><span class="text-sm text-gray-500">Toplam Ürün:</span><p class="font-medium">{{ $stockCount->items->count() }}</p></div>
    </div>
    @if($stockCount->notes)
    <div class="mt-4 p-3 bg-yellow-50 rounded-lg text-sm text-yellow-800">
        <i class="fas fa-sticky-note mr-1"></i> {{ $stockCount->notes }}
    </div>
    @endif
</div>

{{-- Sayım Kalemleri --}}
<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <div class="px-6 py-4 border-b"><h3 class="font-semibold text-gray-800"><i class="fas fa-list mr-1 text-indigo-500"></i> Sayım Kalemleri</h3></div>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ürün</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barkod</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Beklenen</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Sayılan</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Fark</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($stockCount->items as $i => $item)
                @php $diff = ($item->counted_quantity ?? 0) - ($item->system_quantity ?? 0); @endphp
                <tr class="hover:bg-gray-50 {{ $diff < 0 ? 'bg-red-50' : ($diff > 0 ? 'bg-green-50' : '') }}">
                    <td class="px-4 py-3">{{ $i + 1 }}</td>
                    <td class="px-4 py-3 font-medium">
                        @if($item->product)
                            <a href="{{ route('products.show', $item->product) }}" class="text-indigo-600 hover:underline">{{ $item->product->name }}</a>
                        @else
                            {{ $item->product?->name ?? '-' }}
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-500 font-mono text-xs">{{ $item->product?->barcode ?? '-' }}</td>
                    <td class="px-4 py-3 text-right">{{ $item->system_quantity ?? 0 }}</td>
                    <td class="px-4 py-3 text-right font-bold">{{ $item->counted_quantity ?? 0 }}</td>
                    <td class="px-4 py-3 text-right font-bold {{ $diff > 0 ? 'text-green-600' : ($diff < 0 ? 'text-red-600' : 'text-gray-500') }}">
                        {{ $diff > 0 ? '+' : '' }}{{ $diff }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-400">Sayım kalemi bulunamadı.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
