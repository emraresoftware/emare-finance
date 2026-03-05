@extends('layouts.app')
@section('title', 'Alış Faturaları')

@section('content')
{{-- İstatistikler --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Toplam Fatura</p>
        <p class="text-xl font-bold">{{ number_format($stats['total']) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Toplam Tutar</p>
        <p class="text-xl font-bold text-green-600">₺{{ number_format($stats['total_amount'], 2, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Ortalama</p>
        <p class="text-xl font-bold text-blue-600">₺{{ number_format($stats['avg_amount'], 2, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Bu Ay</p>
        <p class="text-xl font-bold text-orange-600">₺{{ number_format($stats['this_month'], 2, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Tedarikçi</p>
        <p class="text-xl font-bold text-purple-600">{{ $stats['firm_count'] }}</p>
    </div>
</div>

{{-- Filtreler --}}
<div class="bg-white rounded-xl shadow-sm border p-4 mb-6">
    <form method="GET" class="flex items-end gap-3 flex-wrap">
        <div class="flex-1 min-w-[180px]">
            <label class="block text-xs text-gray-500 mb-1">Arama</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Fatura no veya firma..."
                   class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Firma</label>
            <select name="firm_id" class="border rounded-lg px-3 py-2 text-sm">
                <option value="">Tüm Firmalar</option>
                @foreach($firms as $f)
                <option value="{{ $f->id }}" {{ request('firm_id') == $f->id ? 'selected' : '' }}>{{ $f->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Şube</label>
            <select name="branch_id" class="border rounded-lg px-3 py-2 text-sm">
                <option value="">Tüm Şubeler</option>
                @foreach($branches as $b)
                <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
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
        <div>
            <label class="block text-xs text-gray-500 mb-1">Sırala</label>
            <select name="sort" class="border rounded-lg px-3 py-2 text-sm">
                <option value="invoice_date" {{ request('sort') == 'invoice_date' ? 'selected' : '' }}>Tarih</option>
                <option value="total_amount" {{ request('sort') == 'total_amount' ? 'selected' : '' }}>Tutar</option>
                <option value="invoice_no" {{ request('sort') == 'invoice_no' ? 'selected' : '' }}>Fatura No</option>
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
        <a href="{{ route('invoices.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm hover:bg-gray-200">Temizle</a>
    </form>
</div>

{{-- Tablo --}}
<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fatura No</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Firma</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Şube</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ödeme</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Kalem</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Toplam</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($invoices as $inv)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <a href="{{ route('invoices.show', $inv) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">{{ $inv->invoice_no }}</a>
                    </td>
                    <td class="px-4 py-3">
                        @if($inv->firm)
                            <a href="{{ route('firms.show', $inv->firm) }}" class="text-indigo-600 hover:underline">{{ $inv->firm->name }}</a>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $inv->branch?->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $inv->payment_type ?? '-' }}</td>
                    <td class="px-4 py-3 text-center font-semibold">{{ $inv->items_count ?? $inv->total_items }}</td>
                    <td class="px-4 py-3 text-right font-bold text-green-600">₺{{ number_format($inv->total_amount, 2, ',', '.') }}</td>
                    <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ $inv->invoice_date?->format('d.m.Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                        <i class="fas fa-file-invoice text-3xl mb-2"></i><p>Fatura bulunamadı.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-t flex items-center justify-between">
        <span class="text-sm text-gray-500">Toplam {{ $invoices->total() }} kayıt, Sayfa {{ $invoices->currentPage() }}/{{ $invoices->lastPage() }}</span>
        <div>{{ $invoices->links() }}</div>
    </div>
</div>
@endsection
