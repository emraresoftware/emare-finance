@extends('layouts.app')
@section('title', 'Stok Hareketleri')

@section('content')
{{-- İstatistikler --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Toplam Hareket</p>
        <p class="text-xl font-bold">{{ number_format($stats['total_movements']) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Toplam Giriş</p>
        <p class="text-xl font-bold text-green-600">{{ number_format($stats['total_in']) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Toplam Çıkış</p>
        <p class="text-xl font-bold text-red-600">{{ number_format($stats['total_out']) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Toplam Değer</p>
        <p class="text-xl font-bold text-blue-600">₺{{ number_format($stats['total_value'], 2, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Fire</p>
        <p class="text-xl font-bold text-yellow-600">{{ number_format($stats['waste_count']) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Transfer</p>
        <p class="text-xl font-bold text-purple-600">{{ number_format($stats['transfer_count']) }}</p>
    </div>
</div>

{{-- Filtreler --}}
<div class="bg-white rounded-xl shadow-sm border p-4 mb-6">
    <form method="GET" class="flex items-end gap-3 flex-wrap">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs text-gray-500 mb-1">Arama</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Ürün adı, barkod..." class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Hareket Türü</label>
            <select name="type" class="border rounded-lg px-3 py-2 text-sm">
                <option value="">Tümü</option>
                <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Giriş</option>
                <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Çıkış</option>
                <option value="transfer" {{ request('type') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                <option value="waste" {{ request('type') == 'waste' ? 'selected' : '' }}>Fire</option>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Başlangıç</label>
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="border rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Bitiş</label>
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="border rounded-lg px-3 py-2 text-sm">
        </div>
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">
            <i class="fas fa-search mr-1"></i> Filtrele
        </button>
        <a href="{{ route('stock.movements') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm hover:bg-gray-200">Temizle</a>
    </form>
</div>

{{-- Tablo --}}
<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ürün</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tür</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Miktar</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Kalan</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Tutar</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Açıklama</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($movements as $i => $m)
                @php
                    $colors = ['in' => 'bg-green-100 text-green-800', 'out' => 'bg-red-100 text-red-800', 'transfer' => 'bg-blue-100 text-blue-800', 'waste' => 'bg-yellow-100 text-yellow-800'];
                    $labels = ['in' => 'Giriş', 'out' => 'Çıkış', 'transfer' => 'Transfer', 'waste' => 'Fire'];
                    $rowBg = $m->type === 'waste' ? 'bg-yellow-50' : ($m->type === 'out' ? 'bg-red-50' : '');
                @endphp
                <tr class="hover:bg-gray-50 {{ $rowBg }}">
                    <td class="px-4 py-3">{{ $movements->firstItem() + $i }}</td>
                    <td class="px-4 py-3 font-medium">
                        @if($m->product)
                            <a href="{{ route('products.show', $m->product) }}" class="text-indigo-600 hover:underline">{{ $m->product->name }}</a>
                        @else
                            {{ $m->product_name ?? '-' }}
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colors[$m->type] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $labels[$m->type] ?? $m->type }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right font-bold">{{ $m->quantity }}</td>
                    <td class="px-4 py-3 text-right">{{ $m->remaining ?? '-' }}</td>
                    <td class="px-4 py-3 text-right">₺{{ number_format($m->total, 2, ',', '.') }}</td>
                    <td class="px-4 py-3 text-gray-500 max-w-xs truncate">{{ $m->note ?? '-' }}</td>
                    <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ $m->movement_date?->format('d.m.Y H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-gray-400">
                        <i class="fas fa-boxes text-3xl mb-2"></i><p>Stok hareketi bulunamadı.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-t flex items-center justify-between">
        <span class="text-sm text-gray-500">Toplam {{ $movements->total() }} kayıt, Sayfa {{ $movements->currentPage() }}/{{ $movements->lastPage() }}</span>
        <div>{{ $movements->links() }}</div>
    </div>
</div>
@endsection
