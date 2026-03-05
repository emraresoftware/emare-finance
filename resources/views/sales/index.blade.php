@extends('layouts.app')
@section('title', 'Satışlar')

@section('content')
{{-- İstatistikler --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Toplam Satış</p>
        <p class="text-xl font-bold">{{ number_format($stats['total_sales']) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Toplam Gelir</p>
        <p class="text-xl font-bold text-green-600">₺{{ number_format($stats['total_revenue'], 2, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Ort. Satış</p>
        <p class="text-xl font-bold text-blue-600">₺{{ number_format($stats['avg_sale'], 2, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Bugün Satış</p>
        <p class="text-xl font-bold">{{ $stats['today_count'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Bugün Gelir</p>
        <p class="text-xl font-bold text-green-600">₺{{ number_format($stats['today_revenue'], 2, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">İptal</p>
        <p class="text-xl font-bold text-red-600">{{ $stats['cancelled'] }}</p>
    </div>
</div>

{{-- Filtreler --}}
<div class="bg-white rounded-xl shadow-sm p-4 mb-6 border">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[180px]">
            <label class="block text-xs text-gray-500 mb-1">Ara</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Fiş no, müşteri veya personel..."
                   class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Başlangıç</label>
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="border rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Bitiş</label>
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="border rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Şube</label>
            <select name="branch_id" class="border rounded-lg px-3 py-2 text-sm">
                <option value="">Tümü</option>
                @foreach($branches as $branch)
                <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Ödeme</label>
            <select name="payment_method" class="border rounded-lg px-3 py-2 text-sm">
                <option value="">Tümü</option>
                <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Nakit</option>
                <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>Kart</option>
                <option value="mixed" {{ request('payment_method') == 'mixed' ? 'selected' : '' }}>Karışık</option>
                <option value="credit" {{ request('payment_method') == 'credit' ? 'selected' : '' }}>Veresiye</option>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Durum</label>
            <select name="status" class="border rounded-lg px-3 py-2 text-sm">
                <option value="">Tümü</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Tamamlandı</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>İptal</option>
                <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>İade</option>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Sırala</label>
            <select name="sort" class="border rounded-lg px-3 py-2 text-sm">
                <option value="sold_at" {{ request('sort') == 'sold_at' ? 'selected' : '' }}>Tarih</option>
                <option value="grand_total" {{ request('sort') == 'grand_total' ? 'selected' : '' }}>Tutar</option>
                <option value="receipt_no" {{ request('sort') == 'receipt_no' ? 'selected' : '' }}>Fiş No</option>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Yön</label>
            <select name="dir" class="border rounded-lg px-3 py-2 text-sm">
                <option value="desc" {{ request('dir') == 'desc' ? 'selected' : '' }}>↓ Azalan</option>
                <option value="asc" {{ request('dir') == 'asc' ? 'selected' : '' }}>↑ Artan</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">
            <i class="fas fa-search mr-1"></i> Filtrele
        </button>
        <a href="{{ route('sales.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm hover:bg-gray-200">Temizle</a>
        <a href="{{ route('sales.export', request()->query()) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700">
            <i class="fas fa-file-csv mr-1"></i> CSV
        </a>
    </form>
</div>

{{-- Tablo --}}
<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fiş No</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Müşteri</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Personel</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Şube</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Ödeme</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Kalem</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Tutar</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Durum</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($sales as $sale)
                <tr class="hover:bg-gray-50 {{ $sale->status === 'cancelled' ? 'bg-red-50' : ($sale->status === 'refunded' ? 'bg-yellow-50' : '') }}">
                    <td class="px-4 py-3">
                        <a href="{{ route('sales.show', $sale) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                            {{ $sale->receipt_no ?: '#' . $sale->id }}
                        </a>
                    </td>
                    <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $sale->sold_at?->format('d.m.Y H:i') }}</td>
                    <td class="px-4 py-3 text-gray-600">
                        @if($sale->customer)
                            <a href="{{ route('customers.show', $sale->customer) }}" class="text-indigo-600 hover:underline">{{ $sale->customer->name }}</a>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $sale->staff_name ?? '-' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $sale->branch?->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $sale->payment_method === 'cash' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $sale->payment_method === 'card' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $sale->payment_method === 'credit' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $sale->payment_method === 'mixed' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                            {{ $sale->payment_method_label }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">{{ $sale->items->count() }}</td>
                    <td class="px-4 py-3 text-right font-semibold text-green-600">₺{{ number_format($sale->grand_total, 2, ',', '.') }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $sale->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $sale->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $sale->status === 'refunded' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                            {{ $sale->status_label }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr><td colspan="9" class="px-4 py-8 text-center text-gray-400">
                    <i class="fas fa-receipt text-3xl mb-2"></i><p>Satış bulunamadı</p>
                </td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
    <div class="px-4 py-3 border-t flex items-center justify-between">
        <span class="text-sm text-gray-500">Toplam {{ $sales->total() }} kayıt, Sayfa {{ $sales->currentPage() }}/{{ $sales->lastPage() }}</span>
        <div>{{ $sales->links() }}</div>
    </div>
</div>
@endsection
