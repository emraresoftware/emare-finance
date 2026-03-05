@extends('layouts.app')
@section('title', 'Giderler')

@section('content')
{{-- İstatistikler --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Toplam Kayıt</p>
        <p class="text-xl font-bold">{{ number_format($stats['total']) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Toplam Gider</p>
        <p class="text-xl font-bold text-red-600">₺{{ number_format($stats['total_amount'], 2, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Ortalama</p>
        <p class="text-xl font-bold text-blue-600">₺{{ number_format($stats['avg_amount'], 2, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Bu Ay</p>
        <p class="text-xl font-bold text-orange-600">₺{{ number_format($stats['this_month'], 2, ',', '.') }}</p>
    </div>
</div>

{{-- Filtreler --}}
<div class="bg-white rounded-xl shadow-sm border p-4 mb-6">
    <form method="GET" class="flex items-end gap-3 flex-wrap">
        <div class="flex-1 min-w-[180px]">
            <label class="block text-xs text-gray-500 mb-1">Arama</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Açıklama ara..."
                   class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Tür</label>
            <select name="type_id" class="border rounded-lg px-3 py-2 text-sm">
                <option value="">Tümü</option>
                @foreach($types as $type)
                <option value="{{ $type->id }}" {{ request('type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                @endforeach
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
        <a href="{{ route('income_expense.expenses') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm hover:bg-gray-200">Temizle</a>
    </form>
</div>

{{-- Tablo --}}
<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tür</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Açıklama</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Tutar</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($expenses as $i => $expense)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">{{ $expenses->firstItem() + $i }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            {{ $expense->type?->name ?? '-' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-600 max-w-md truncate">{{ $expense->note ?? '-' }}</td>
                    <td class="px-4 py-3 text-right font-bold text-red-600">₺{{ number_format($expense->amount, 2, ',', '.') }}</td>
                    <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ $expense->date ? \Carbon\Carbon::parse($expense->date)->format('d.m.Y') : '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-400">
                        <i class="fas fa-arrow-up text-3xl mb-2"></i><p>Gider kaydı bulunamadı.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-t flex items-center justify-between">
        <span class="text-sm text-gray-500">Toplam {{ $expenses->total() }} kayıt, Sayfa {{ $expenses->currentPage() }}/{{ $expenses->lastPage() }}</span>
        <div>{{ $expenses->links() }}</div>
    </div>
</div>
@endsection
