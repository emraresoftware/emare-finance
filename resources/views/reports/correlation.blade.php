@extends('layouts.app')
@section('title', 'Ürün Korelasyon Raporu')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <p class="text-sm text-gray-600 mb-4">Diğer ürünlerle ilişkisini öğrenmek istediğiniz ürünün barkodunu girerek formu gönderiniz.</p>
        <form method="GET" class="flex items-end gap-4 flex-wrap">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ürün Barkodu</label>
                <input type="text" name="barcode" value="{{ $barcode }}" placeholder="Barkod girin..." class="rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Başlangıç</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bitiş</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border focus:ring-indigo-500 focus:border-indigo-500">
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
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ürün Barkodu</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ürün Adı</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Satış Adeti</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Toplam Tutar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($correlationData as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-mono">{{ $item->barcode }}</td>
                        <td class="px-4 py-3 text-sm">{{ $item->product_name }}</td>
                        <td class="px-4 py-3 text-sm text-right">{{ number_format($item->total_quantity, 0) }}</td>
                        <td class="px-4 py-3 text-sm text-right">₺{{ number_format($item->total_amount, 2, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-400">
                            <i class="fas fa-inbox text-3xl mb-2"></i>
                            <p>{{ $barcode ? 'Korelasyon verisi bulunamadı.' : 'Barkod girerek arama yapın.' }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
