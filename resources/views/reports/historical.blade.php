@extends('layouts.app')
@section('title', 'Tarihsel Rapor')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <form method="GET" class="flex items-end gap-4 flex-wrap">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Başlangıç Tarihi</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bitiş Tarihi</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">
                <i class="fas fa-search mr-1"></i> Listele
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl shadow-sm border p-5">
            <div class="text-sm text-gray-500">Toplam Satış</div>
            <div class="text-2xl font-bold text-gray-900 mt-1">{{ $summary['total_sales'] }} adet</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-5">
            <div class="text-sm text-gray-500">Toplam Ciro</div>
            <div class="text-2xl font-bold text-green-600 mt-1">₺{{ number_format($summary['total_revenue'], 2, ',', '.') }}</div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Şube</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Satış Kodu</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Müşteri</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Toplam Tutar</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">İskonto</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ödeme Tipi</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($sales as $sale)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm">{{ $sale->branch?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm font-mono">{{ $sale->receipt_no }}</td>
                        <td class="px-4 py-3 text-sm">{{ $sale->customer?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-right font-medium">₺{{ number_format($sale->grand_total, 2, ',', '.') }}</td>
                        <td class="px-4 py-3 text-sm">₺{{ number_format($sale->discount ?? 0, 2, ',', '.') }}</td>
                        <td class="px-4 py-3 text-sm">{{ $sale->payment_method }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $sale->sold_at?->format('d.m.Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                            <i class="fas fa-inbox text-3xl mb-2"></i><p>Kayıt bulunamadı.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t">{{ $sales->links() }}</div>
    </div>
</div>
@endsection
