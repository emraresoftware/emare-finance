@extends('layouts.app')
@section('title', 'Firmalar')

@section('content')
{{-- İstatistikler --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Toplam Firma</p>
        <p class="text-xl font-bold">{{ number_format($stats['total']) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Aktif Firma</p>
        <p class="text-xl font-bold text-green-600">{{ $stats['active'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Faturalı Firma</p>
        <p class="text-xl font-bold text-blue-600">{{ $stats['with_invoices'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Toplam Bakiye</p>
        <p class="text-xl font-bold text-red-600">₺{{ number_format($stats['total_balance'], 2, ',', '.') }}</p>
    </div>
</div>

{{-- Filtreler --}}
<div class="bg-white rounded-xl shadow-sm border p-4 mb-6">
    <form method="GET" class="flex items-end gap-3 flex-wrap">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs text-gray-500 mb-1">Arama</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Firma adı, vergi no..."
                   class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Sırala</label>
            <select name="sort" class="border rounded-lg px-3 py-2 text-sm">
                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Ad</option>
                <option value="balance" {{ request('sort') == 'balance' ? 'selected' : '' }}>Bakiye</option>
                <option value="purchase_invoices_count" {{ request('sort') == 'purchase_invoices_count' ? 'selected' : '' }}>Fatura Sayısı</option>
                <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Kayıt Tarihi</option>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Yön</label>
            <select name="dir" class="border rounded-lg px-3 py-2 text-sm">
                <option value="asc" {{ request('dir') == 'asc' ? 'selected' : '' }}>↑ Artan</option>
                <option value="desc" {{ request('dir') == 'desc' ? 'selected' : '' }}>↓ Azalan</option>
            </select>
        </div>
        <label class="flex items-center text-sm gap-1">
            <input type="checkbox" name="has_invoices" value="1" {{ request('has_invoices') ? 'checked' : '' }}> Faturalı
        </label>
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">
            <i class="fas fa-search mr-1"></i> Filtrele
        </button>
        <a href="{{ route('firms.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm hover:bg-gray-200">Temizle</a>
    </form>
</div>

{{-- Tablo --}}
<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Firma Adı</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telefon</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vergi No</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Fatura</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Alış Toplamı</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Bakiye</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($firms as $firm)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <a href="{{ route('firms.show', $firm) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">{{ $firm->name }}</a>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $firm->phone ?? '-' }}</td>
                    <td class="px-4 py-3 text-gray-600 font-mono text-xs">{{ $firm->tax_number ?? '-' }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $firm->purchase_invoices_count }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right text-gray-600">₺{{ number_format($firm->purchase_invoices_sum_total_amount ?? 0, 2, ',', '.') }}</td>
                    <td class="px-4 py-3 text-right font-semibold {{ ($firm->balance ?? 0) < 0 ? 'text-red-600' : 'text-green-600' }}">
                        ₺{{ number_format(abs($firm->balance ?? 0), 2, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                        <i class="fas fa-building text-3xl mb-2"></i><p>Firma bulunamadı.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-t flex items-center justify-between">
        <span class="text-sm text-gray-500">Toplam {{ $firms->total() }} kayıt, Sayfa {{ $firms->currentPage() }}/{{ $firms->lastPage() }}</span>
        <div>{{ $firms->links() }}</div>
    </div>
</div>
@endsection
