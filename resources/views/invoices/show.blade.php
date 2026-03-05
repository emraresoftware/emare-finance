@extends('layouts.app')
@section('title', 'Fatura #' . $invoice->invoice_no)

@section('content')
<div class="mb-4 flex items-center justify-between">
    <a href="{{ route('invoices.index') }}" class="text-sm text-indigo-600"><i class="fas fa-arrow-left mr-1"></i> Faturalara Dön</a>
    <button onclick="window.print()" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm hover:bg-gray-200">
        <i class="fas fa-print mr-1"></i> Yazdır
    </button>
</div>

{{-- İstatistik Kartları --}}
<div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Kalem Sayısı</p>
        <p class="text-xl font-bold">{{ $invoiceStats['item_count'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Toplam Adet</p>
        <p class="text-xl font-bold text-blue-600">{{ number_format($invoiceStats['total_quantity']) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Genel Toplam</p>
        <p class="text-xl font-bold text-green-600">₺{{ number_format($invoiceStats['total_amount'], 2, ',', '.') }}</p>
    </div>
</div>

{{-- Fatura Bilgileri --}}
<div class="bg-white rounded-xl shadow-sm border p-6 mb-6">
    <h2 class="text-xl font-bold text-gray-800 mb-4"><i class="fas fa-file-invoice mr-1 text-indigo-500"></i> Fatura #{{ $invoice->invoice_no }}</h2>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <span class="text-sm text-gray-500">Firma:</span>
            @if($invoice->firm)
                <p class="font-medium"><a href="{{ route('firms.show', $invoice->firm) }}" class="text-indigo-600 hover:underline">{{ $invoice->firm->name }}</a></p>
            @else
                <p class="font-medium">-</p>
            @endif
        </div>
        <div><span class="text-sm text-gray-500">Şube:</span><p class="font-medium">{{ $invoice->branch?->name ?? '-' }}</p></div>
        <div><span class="text-sm text-gray-500">Fatura Tarihi:</span><p class="font-medium">{{ $invoice->invoice_date?->format('d.m.Y') }}</p></div>
        <div><span class="text-sm text-gray-500">Durum:</span>
            <p class="font-medium">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    {{ ucfirst($invoice->invoice_type ?? 'purchase') }}
                </span>
            </p>
        </div>
    </div>
    <div class="mt-4 flex gap-8">
        <div><span class="text-sm text-gray-500">Ödeme Tipi:</span><p class="text-lg font-bold">{{ $invoice->payment_type ?? 'Nakit' }}</p></div>
        <div><span class="text-sm text-gray-500">Toplam Kalem:</span><p class="text-lg font-bold text-blue-600">{{ $invoice->items->count() }}</p></div>
        <div><span class="text-sm text-gray-500">Genel Toplam:</span><p class="text-lg font-bold text-green-600">₺{{ number_format($invoice->total_amount, 2, ',', '.') }}</p></div>
    </div>
    @if($invoice->notes)
    <div class="mt-4 p-3 bg-yellow-50 rounded-lg text-sm text-yellow-800">
        <i class="fas fa-sticky-note mr-1"></i> {{ $invoice->notes }}
    </div>
    @endif
</div>

{{-- Fatura Kalemleri --}}
<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <div class="px-6 py-4 border-b"><h3 class="font-semibold text-gray-800"><i class="fas fa-list mr-1 text-indigo-500"></i> Fatura Kalemleri</h3></div>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ürün</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barkod</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Miktar</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Birim Fiyat</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Toplam</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($invoice->items as $i => $item)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">{{ $i + 1 }}</td>
                    <td class="px-4 py-3 font-medium">
                        @if($item->product)
                            <a href="{{ route('products.show', $item->product) }}" class="text-indigo-600 hover:underline">{{ $item->product->name }}</a>
                        @else
                            {{ $item->product_name }}
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-500 font-mono text-xs">{{ $item->barcode ?? '-' }}</td>
                    <td class="px-4 py-3 text-right">{{ $item->quantity }}</td>
                    <td class="px-4 py-3 text-right">₺{{ number_format($item->unit_price, 2, ',', '.') }}</td>
                    <td class="px-4 py-3 text-right font-bold">₺{{ number_format($item->total, 2, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-400">Kalem bulunamadı.</td>
                </tr>
                @endforelse
            </tbody>
            @if($invoice->items->count())
            <tfoot class="bg-gray-50">
                <tr>
                    <td colspan="3" class="px-4 py-3 font-bold">Toplam</td>
                    <td class="px-4 py-3 text-right font-bold">{{ $invoice->items->sum('quantity') }}</td>
                    <td class="px-4 py-3"></td>
                    <td class="px-4 py-3 text-right font-bold text-green-600">₺{{ number_format($invoice->items->sum('total'), 2, ',', '.') }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection
