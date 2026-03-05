@extends('layouts.app')
@section('title', 'Stok Hareket Raporu')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <form method="GET" class="flex items-end gap-4 flex-wrap">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Başlangıç</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bitiş</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tür</label>
                <select name="type" class="rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                    <option value="">Tümü</option>
                    <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Stok Girişi</option>
                    <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Stok Çıkışı</option>
                    <option value="sale" {{ request('type') == 'sale' ? 'selected' : '' }}>Satış</option>
                </select>
            </div>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">
                <i class="fas fa-search mr-1"></i> Listele
            </button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sıra</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tür</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barkod</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ürün</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">İşlem Kodu</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Firma/Müşteri</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Miktar</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Kalan</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Birim Fiyat</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Tutar</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($movements as $i => $m)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm">{{ $movements->firstItem() + $i }}</td>
                        <td class="px-4 py-3 text-sm">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $m->type == 'in' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $m->type == 'in' ? 'Giriş' : ($m->type == 'out' ? 'Çıkış' : $m->type) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm font-mono">{{ $m->barcode }}</td>
                        <td class="px-4 py-3 text-sm">{{ $m->product_name }}</td>
                        <td class="px-4 py-3 text-sm font-mono">{{ $m->transaction_code }}</td>
                        <td class="px-4 py-3 text-sm">{{ $m->firm_customer }}</td>
                        <td class="px-4 py-3 text-sm text-right">{{ number_format($m->quantity, 2) }}</td>
                        <td class="px-4 py-3 text-sm text-right">{{ number_format($m->remaining, 2) }}</td>
                        <td class="px-4 py-3 text-sm text-right">₺{{ number_format($m->unit_price, 2, ',', '.') }}</td>
                        <td class="px-4 py-3 text-sm text-right font-medium">₺{{ number_format($m->total, 2, ',', '.') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $m->movement_date?->format('d.m.Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="px-4 py-8 text-center text-gray-400">
                            <i class="fas fa-inbox text-3xl mb-2"></i><p>Kayıt bulunamadı.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t">{{ $movements->links() }}</div>
    </div>
</div>
@endsection
