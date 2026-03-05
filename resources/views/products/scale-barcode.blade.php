@extends('layouts.app')
@section('title', 'Barkodlu Terazi Çıktısı')

@section('content')
<div class="space-y-6">
    {{-- İstatistik Kartları --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Toplam Ürün</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_products']) }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-boxes-stacked text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Tartılabilir Ürün</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['weighable']) }}</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-weight-scale text-green-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Barkodlu Ürün</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['with_barcode']) }}</p>
                </div>
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-barcode text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtre --}}
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <form method="GET" class="flex items-end gap-4 flex-wrap">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Ürün Ara</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ürün adı veya barkod..."
                    class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <select name="category_id" class="rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                    <option value="">Tümü</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-2">
                <label class="flex items-center gap-1.5 text-sm text-gray-600">
                    <input type="checkbox" name="weighable" value="1" {{ request('weighable') ? 'checked' : '' }}
                        class="rounded border-gray-300 text-indigo-600">
                    Sadece Tartılabilir
                </label>
            </div>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">
                <i class="fas fa-search mr-1"></i> Filtrele
            </button>
            @if(request()->hasAny(['search', 'category_id', 'weighable']))
                <a href="{{ route('products.scale_barcode') }}" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-200">
                    <i class="fas fa-times mr-1"></i> Temizle
                </a>
            @endif
            <button type="button" onclick="window.print()" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700">
                <i class="fas fa-print mr-1"></i> Yazdır
            </button>
            <button type="button" onclick="connectScale()" class="bg-amber-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-amber-700">
                <i class="fas fa-weight-scale mr-1"></i> Terazi Bağla
            </button>
        </form>
    </div>

    {{-- Tablo --}}
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="px-6 py-4 border-b flex items-center justify-between">
            <h3 class="font-semibold text-gray-800"><i class="fas fa-weight-scale mr-2"></i>Barkodlu Terazi Çıktısı</h3>
            <span class="text-sm text-gray-500">{{ $products->total() }} kayıt</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-3 w-12">#</th>
                        <th class="px-4 py-3">Ürün Adı</th>
                        <th class="px-4 py-3">Barkod</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3">Birim</th>
                        <th class="px-4 py-3 text-right">Satış Fiyatı</th>
                        <th class="px-4 py-3 text-right">Stok</th>
                        <th class="px-4 py-3 text-center">Terazi Barkodu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($products as $product)
                    @php
                        $isWeighable = in_array(strtolower($product->unit), ['kg', 'gram', 'gr']);
                    @endphp
                    <tr class="hover:bg-gray-50 {{ $isWeighable ? 'bg-green-50/30' : '' }}">
                        <td class="px-4 py-3 text-gray-400 text-xs">{{ $loop->iteration + ($products->currentPage() - 1) * $products->perPage() }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('products.show', $product) }}" class="text-indigo-600 hover:underline font-medium">
                                {{ $product->name }}
                            </a>
                            @if($isWeighable)
                                <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                    <i class="fas fa-weight-scale mr-0.5"></i> Tartılır
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $product->barcode ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $product->category?->name ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $isWeighable ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                {{ $product->unit }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-medium">₺{{ number_format($product->sale_price, 2, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right {{ $product->stock_quantity <= 0 ? 'text-red-600' : 'text-gray-700' }}">
                            {{ number_format($product->stock_quantity, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($product->barcode)
                                <div class="inline-flex flex-col items-center bg-white border rounded-lg p-2">
                                    <svg class="barcode-svg" data-barcode="{{ $product->barcode }}"></svg>
                                    @if($isWeighable)
                                        <div class="text-[8px] text-green-600 font-medium mt-0.5">
                                            ₺{{ number_format($product->sale_price, 2, ',', '.') }}/{{ $product->unit }}
                                        </div>
                                    @endif
                                </div>
                            @else
                                <span class="text-gray-400 text-xs">Barkod yok</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-400">
                            <i class="fas fa-weight-scale text-3xl mb-2"></i>
                            <p>Ürün bulunamadı.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($products->hasPages())
        <div class="px-6 py-3 border-t bg-gray-50">
            {{ $products->links() }}
        </div>
        @endif
        <div class="px-6 py-2 border-t text-xs text-gray-400">
            Sayfa {{ $products->currentPage() }} / {{ $products->lastPage() }} — Toplam {{ $products->total() }} kayıt
        </div>
    </div>
</div>

<style>
@media print {
    .space-y-6 > :nth-child(-n+2) { display: none !important; }
    table { font-size: 11px !important; }
}
</style>

@push('scripts')
<script>
// Barkodları oluştur
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.barcode-svg').forEach(function(svg) {
        const barcode = svg.dataset.barcode;
        if (barcode && typeof JsBarcode !== 'undefined') {
            try {
                JsBarcode(svg, barcode, {
                    format: barcode.length === 13 ? 'EAN13' : (barcode.length === 8 ? 'EAN8' : 'CODE128'),
                    width: 1, height: 30, fontSize: 9, margin: 2, displayValue: true,
                });
            } catch(e) {
                JsBarcode(svg, barcode, { format: 'CODE128', width: 1, height: 25, fontSize: 8, margin: 1, displayValue: true });
            }
        }
    });
});

// Terazi bağlantısı
async function connectScale() {
    try {
        const connId = await window.hw.connectSerial({ baudRate: 9600 });
        // Sürekli okuma başlat
        window.hw.startScalePolling(function(reading) {
            const display = document.getElementById('scale-display');
            if (display) {
                display.textContent = reading.weight.toFixed(3) + ' ' + reading.unit;
                display.classList.toggle('text-green-600', reading.stable);
                display.classList.toggle('text-yellow-600', !reading.stable);
            }
        }, 500, 'cas', connId);

        // Terazi okuma ekranı göster
        const header = document.querySelector('.font-semibold.text-gray-800');
        if (header) {
            const scaleDiv = document.createElement('div');
            scaleDiv.className = 'inline-flex items-center gap-3 ml-4 px-4 py-2 bg-green-50 border border-green-200 rounded-lg';
            scaleDiv.innerHTML = '<i class="fas fa-weight-scale text-green-600"></i> <span id="scale-display" class="text-lg font-bold text-green-600">0.000 kg</span>';
            header.parentNode.appendChild(scaleDiv);
        }
    } catch(e) {
        if (e.name !== 'NotFoundError') {
            alert('Terazi bağlantı hatası: ' + e.message);
        }
    }
}
</script>
@endpush
@endsection
