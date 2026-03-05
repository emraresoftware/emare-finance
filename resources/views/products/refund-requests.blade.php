@extends('layouts.app')
@section('title', 'İade Talepleri')

@section('content')
{{-- İstatistik Kartları --}}
<div class="grid grid-cols-2 md:grid-cols-2 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">İptal Edilen Satış</p>
        <p class="text-xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Toplam İptal Tutarı</p>
        <p class="text-xl font-bold text-red-600">₺{{ number_format($stats['total_amount'], 2, ',', '.') }}</p>
    </div>
</div>

{{-- Filtreler --}}
<div class="bg-white rounded-xl shadow-sm border p-4 mb-6">
    <form method="GET" class="flex items-end gap-4 flex-wrap">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Durum</label>
            <select name="status" class="rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                <option value="">Tümü</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Bekleyen</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Onaylanan</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Reddedilen</option>
            </select>
        </div>
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs text-gray-500 mb-1">Arama</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Fiş no, müşteri..."
                   class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border focus:ring-2 focus:ring-indigo-500">
        </div>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 transition">
            <i class="fas fa-search mr-1"></i> Filtrele
        </button>
        <a href="{{ route('products.refund_requests') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm hover:bg-gray-200 transition">Temizle</a>
    </form>
</div>

{{-- İade Talepleri / İptal Edilen Satışlar --}}
<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <div class="px-6 py-4 border-b">
        <h3 class="font-semibold text-gray-800"><i class="fas fa-exchange-alt text-orange-500 mr-2"></i>İptal Edilen Satışlar / İade Talepleri</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fiş No</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Şube</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Müşteri</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Tutar</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ödeme</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Not</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">İşlem</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($requests as $sale)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $sale->sold_at?->format('d.m.Y H:i') ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm font-mono">{{ $sale->receipt_no ?: '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $sale->branch?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm">
                            @if($sale->customer)
                                <a href="{{ route('customers.show', $sale->customer) }}" class="text-indigo-600 hover:underline">{{ $sale->customer->name }}</a>
                            @else - @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-right font-medium text-red-600">₺{{ number_format($sale->grand_total, 2, ',', '.') }}</td>
                        <td class="px-4 py-3 text-sm">
                            @php
                                $payLabels = ['cash' => 'Nakit', 'card' => 'Kart', 'mixed' => 'Karışık', 'credit' => 'Veresiye'];
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $payLabels[$sale->payment_method] ?? $sale->payment_method }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500 max-w-[150px] truncate">{{ $sale->notes ?: ($sale->note ?: '-') }}</td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('sales.show', $sale) }}" class="text-indigo-600 hover:text-indigo-800 text-sm">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-gray-400">
                            <i class="fas fa-exchange-alt text-4xl mb-3"></i>
                            <p class="text-lg">Henüz iptal edilen satış / iade talebi bulunmuyor.</p>
                            <p class="text-sm mt-1">İade talepleri oluşturulduğunda burada listelenecektir.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($requests->hasPages())
    <div class="px-4 py-3 border-t">
        {{ $requests->links() }}
    </div>
    @endif
</div>
@endsection
