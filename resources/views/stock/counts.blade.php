@extends('layouts.app')
@section('title', 'Stok Sayımları')

@section('content')
{{-- İstatistikler --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Toplam Sayım</p>
        <p class="text-xl font-bold">{{ number_format($countStats['total']) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Tamamlanan</p>
        <p class="text-xl font-bold text-green-600">{{ $countStats['completed'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Devam Eden</p>
        <p class="text-xl font-bold text-yellow-600">{{ $countStats['in_progress'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Toplam Kalem</p>
        <p class="text-xl font-bold text-blue-600">{{ number_format($countStats['total_items']) }}</p>
    </div>
</div>

{{-- Filtreler --}}
<div class="bg-white rounded-xl shadow-sm p-4 mb-6 border">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Durum</label>
            <select name="status" class="border rounded-lg px-3 py-2 text-sm">
                <option value="">Tümü</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Tamamlandı</option>
                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Devam Ediyor</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">
            <i class="fas fa-search mr-1"></i> Filtrele
        </button>
        <a href="{{ route('stock.counts') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm hover:bg-gray-200">Temizle</a>
    </form>
</div>

{{-- Tablo --}}
<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sayım</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Şube</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Durum</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Ürün Sayısı</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">İşlem</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($counts as $i => $count)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">{{ $counts->firstItem() + $i }}</td>
                    <td class="px-4 py-3 font-medium">Sayım #{{ $count->id }}</td>
                    <td class="px-4 py-3">{{ $count->branch?->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $count->status === 'completed' ? 'bg-green-100 text-green-800' : ($count->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ $count->status === 'completed' ? 'Tamamlandı' : ($count->status === 'in_progress' ? 'Devam Ediyor' : ucfirst($count->status ?? '-')) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center font-semibold">{{ $count->items_count ?? 0 }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $count->counted_at ? \Carbon\Carbon::parse($count->counted_at)->format('d.m.Y') : '-' }}</td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('stock.count_show', $count) }}" class="text-indigo-600 hover:underline text-sm">
                            <i class="fas fa-eye mr-1"></i> Detay
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                        <i class="fas fa-clipboard-check text-3xl mb-2"></i><p>Stok sayımı bulunamadı.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-t flex items-center justify-between">
        <span class="text-sm text-gray-500">Toplam {{ $counts->total() }} kayıt, Sayfa {{ $counts->currentPage() }}/{{ $counts->lastPage() }}</span>
        <div>{{ $counts->links() }}</div>
    </div>
</div>
@endsection
