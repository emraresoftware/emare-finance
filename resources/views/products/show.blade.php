@extends('layouts.app')
@section('title', $product->name)

@section('content')
<div class="mb-4 flex items-center justify-between">
    <a href="{{ route('products.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
        <i class="fas fa-arrow-left mr-1"></i> Ürünlere Dön
    </a>
    <div class="flex items-center gap-2">
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
            {{ $product->is_active ? 'Aktif' : 'Pasif' }}
        </span>
        @if($product->isLowStock())
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                <i class="fas fa-exclamation-triangle mr-1"></i> Düşük Stok
            </span>
        @endif
    </div>
</div>

{{-- Üst Bilgi Kartları --}}
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Toplam Satış</p>
        <p class="text-xl font-bold text-gray-900">{{ number_format($salesStats['total_quantity'], 0, ',', '.') }}</p>
        <p class="text-xs text-gray-400">{{ $salesStats['sale_count'] }} sipariş</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Toplam Gelir</p>
        <p class="text-xl font-bold text-green-600">₺{{ number_format($salesStats['total_revenue'], 2, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Ort. Satış Fiyatı</p>
        <p class="text-xl font-bold text-blue-600">₺{{ number_format($salesStats['avg_price'], 2, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Son 30 Gün Satış</p>
        <p class="text-xl font-bold text-indigo-600">{{ number_format($salesStats['last_30_days'], 0, ',', '.') }}</p>
        <p class="text-xs text-gray-400">adet</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Kâr Marjı</p>
        <p class="text-xl font-bold {{ $product->profit_margin > 0 ? 'text-green-600' : 'text-red-600' }}">%{{ $product->profit_margin }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Sol Panel: Ürün Bilgileri --}}
    <div class="lg:col-span-1 space-y-6">
        {{-- Temel Bilgiler --}}
        <div class="bg-white rounded-xl shadow-sm p-6 border">
            <h2 class="text-lg font-bold text-gray-900 mb-4">
                <i class="fas fa-box text-indigo-500 mr-2"></i>{{ $product->name }}
            </h2>
            @if($product->image_url)
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-48 object-contain bg-gray-50 rounded-lg mb-4">
            @endif
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Barkod</span><span class="font-mono bg-gray-100 px-2 py-0.5 rounded">{{ $product->barcode ?: '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Kategori</span>
                    <span>
                        @if($product->category)
                            <a href="{{ route('products.index', ['category_id' => $product->category_id]) }}" class="text-indigo-600 hover:underline">{{ $product->category->name }}</a>
                        @else - @endif
                    </span>
                </div>
                <div class="flex justify-between"><span class="text-gray-500">Birim</span><span>{{ $product->unit }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">KDV</span><span>%{{ $product->vat_rate }}</span></div>
                @if($product->variant_type)
                    <div class="flex justify-between"><span class="text-gray-500">Varyant Tipi</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">{{ $product->variant_type }}</span>
                    </div>
                @endif
                @if($product->parent)
                    <div class="flex justify-between"><span class="text-gray-500">Ana Ürün</span>
                        <a href="{{ route('products.show', $product->parent) }}" class="text-indigo-600 hover:underline">{{ $product->parent->name }}</a>
                    </div>
                @endif
                <hr>
                <div class="flex justify-between"><span class="text-gray-500">Alış Fiyatı</span><span class="font-bold">₺{{ number_format($product->purchase_price, 2, ',', '.') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Satış Fiyatı</span><span class="font-bold text-green-600">₺{{ number_format($product->sale_price, 2, ',', '.') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Birim Kâr</span>
                    <span class="font-bold {{ ($product->sale_price - $product->purchase_price) > 0 ? 'text-green-600' : 'text-red-600' }}">
                        ₺{{ number_format($product->sale_price - $product->purchase_price, 2, ',', '.') }}
                    </span>
                </div>
                <hr>
                <div class="flex justify-between"><span class="text-gray-500">Mevcut Stok</span>
                    <span class="font-bold {{ $product->isLowStock() ? 'text-red-600' : 'text-gray-900' }}">
                        {{ $product->stock_quantity }} {{ $product->unit }}
                    </span>
                </div>
                <div class="flex justify-between"><span class="text-gray-500">Kritik Stok</span><span>{{ $product->critical_stock }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Stok Değeri</span>
                    <span class="font-bold">₺{{ number_format($product->stock_quantity * $product->sale_price, 2, ',', '.') }}</span>
                </div>
                <div class="flex justify-between"><span class="text-gray-500">Maliyet Değeri</span>
                    <span>₺{{ number_format($product->stock_quantity * $product->purchase_price, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Şube Stokları --}}
        @if($branchStocks->count() > 0)
        <div class="bg-white rounded-xl shadow-sm p-6 border">
            <h3 class="font-semibold text-gray-800 mb-3"><i class="fas fa-store text-blue-500 mr-2"></i>Şube Stokları</h3>
            <div class="space-y-2">
                @foreach($branchStocks as $branch)
                <div class="flex justify-between items-center p-2 bg-gray-50 rounded-lg">
                    <span class="text-sm font-medium">{{ $branch->name }}</span>
                    <div class="text-right">
                        <span class="text-sm font-bold">{{ $branch->pivot->stock_quantity }}</span>
                        <span class="text-xs text-gray-400 block">₺{{ number_format($branch->pivot->sale_price, 2, ',', '.') }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Varyantlar --}}
        @if($product->variants->count() > 0)
        <div class="bg-white rounded-xl shadow-sm p-6 border">
            <h3 class="font-semibold text-gray-800 mb-3"><i class="fas fa-clone text-purple-500 mr-2"></i>Varyantlar ({{ $product->variants->count() }})</h3>
            <div class="space-y-2">
                @foreach($product->variants as $variant)
                <a href="{{ route('products.show', $variant) }}" class="flex justify-between items-center p-2 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div>
                        <span class="text-sm font-medium text-indigo-600">{{ $variant->name }}</span>
                        <span class="text-xs text-gray-400 block">{{ $variant->barcode ?: 'Barkod yok' }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-sm font-bold">₺{{ number_format($variant->sale_price, 2, ',', '.') }}</span>
                        <span class="text-xs text-gray-400 block">Stok: {{ $variant->stock_quantity }}</span>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Sağ Panel: Sekmeli İçerik --}}
    <div class="lg:col-span-2 space-y-6" x-data="{ activeTab: 'sales' }">

        {{-- Satış Trendi Grafiği --}}
        @if($monthlySales->count() > 0)
        <div class="bg-white rounded-xl shadow-sm p-6 border">
            <h3 class="font-semibold text-gray-800 mb-4"><i class="fas fa-chart-area text-green-500 mr-2"></i>Aylık Satış Trendi</h3>
            <canvas id="salesTrendChart" height="120"></canvas>
        </div>
        @endif

        {{-- Sekme Başlıkları --}}
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <div class="border-b">
                <nav class="flex -mb-px overflow-x-auto">
                    <button @click="activeTab = 'sales'" :class="activeTab === 'sales' ? 'border-indigo-500 text-indigo-600 bg-indigo-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="flex-shrink-0 px-4 py-3 text-sm font-medium border-b-2 transition">
                        <i class="fas fa-shopping-cart mr-1"></i> Son Satışlar
                        <span class="ml-1 bg-gray-100 text-gray-600 text-xs px-1.5 py-0.5 rounded-full">{{ $salesHistory->count() }}</span>
                    </button>
                    <button @click="activeTab = 'stock'" :class="activeTab === 'stock' ? 'border-indigo-500 text-indigo-600 bg-indigo-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="flex-shrink-0 px-4 py-3 text-sm font-medium border-b-2 transition">
                        <i class="fas fa-exchange-alt mr-1"></i> Stok Hareketleri
                        <span class="ml-1 bg-gray-100 text-gray-600 text-xs px-1.5 py-0.5 rounded-full">{{ $stockMovements->count() }}</span>
                    </button>
                    <button @click="activeTab = 'purchases'" :class="activeTab === 'purchases' ? 'border-indigo-500 text-indigo-600 bg-indigo-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="flex-shrink-0 px-4 py-3 text-sm font-medium border-b-2 transition">
                        <i class="fas fa-file-invoice mr-1"></i> Alış Faturaları
                        <span class="ml-1 bg-gray-100 text-gray-600 text-xs px-1.5 py-0.5 rounded-full">{{ $purchaseItems->count() }}</span>
                    </button>
                </nav>
            </div>

            {{-- Satışlar Sekmesi --}}
            <div x-show="activeTab === 'sales'" class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Tarih</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Fiş No</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Miktar</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Birim Fiyat</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">İndirim</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Toplam</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($salesHistory as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 text-gray-600">{{ $item->sale?->sold_at?->format('d.m.Y H:i') }}</td>
                                <td class="px-3 py-2">
                                    <a href="{{ route('sales.show', $item->sale_id) }}" class="text-indigo-600 hover:underline">{{ $item->sale?->receipt_no }}</a>
                                </td>
                                <td class="px-3 py-2 text-right">{{ $item->quantity }}</td>
                                <td class="px-3 py-2 text-right">₺{{ number_format($item->unit_price, 2, ',', '.') }}</td>
                                <td class="px-3 py-2 text-right text-red-600">{{ $item->discount > 0 ? '₺' . number_format($item->discount, 2, ',', '.') : '-' }}</td>
                                <td class="px-3 py-2 text-right font-medium">₺{{ number_format($item->total, 2, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-3 py-8 text-center text-gray-400">
                                <i class="fas fa-shopping-cart text-2xl mb-2"></i><p>Satış kaydı yok</p>
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Stok Hareketleri Sekmesi --}}
            <div x-show="activeTab === 'stock'" x-cloak class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Tarih</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Tür</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Açıklama</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Miktar</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Kalan</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Birim Fiyat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($stockMovements as $move)
                            @php
                                $typeColors = ['in' => 'bg-green-100 text-green-800', 'out' => 'bg-red-100 text-red-800', 'sale' => 'bg-blue-100 text-blue-800', 'refund' => 'bg-yellow-100 text-yellow-800', 'transfer' => 'bg-purple-100 text-purple-800', 'count' => 'bg-gray-100 text-gray-800'];
                                $typeLabels = ['in' => 'Giriş', 'out' => 'Çıkış', 'sale' => 'Satış', 'refund' => 'İade', 'transfer' => 'Transfer', 'count' => 'Sayım'];
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 text-gray-600 text-xs">{{ $move->movement_date?->format('d.m.Y H:i') ?? '-' }}</td>
                                <td class="px-3 py-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $typeColors[$move->type] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $typeLabels[$move->type] ?? $move->type }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 text-gray-600 text-xs max-w-[200px] truncate">{{ $move->note ?: ($move->firm_customer ?: '-') }}</td>
                                <td class="px-3 py-2 text-right font-medium {{ $move->quantity >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $move->quantity >= 0 ? '+' : '' }}{{ $move->quantity }}
                                </td>
                                <td class="px-3 py-2 text-right text-gray-600">{{ $move->remaining }}</td>
                                <td class="px-3 py-2 text-right text-gray-600">₺{{ number_format($move->unit_price, 2, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-3 py-8 text-center text-gray-400">
                                <i class="fas fa-exchange-alt text-2xl mb-2"></i><p>Stok hareketi bulunamadı</p>
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Alış Faturaları Sekmesi --}}
            <div x-show="activeTab === 'purchases'" x-cloak class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Tarih</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Fatura No</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Firma</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Miktar</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Birim Fiyat</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Toplam</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($purchaseItems as $pi)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 text-gray-600 text-xs">{{ $pi->purchaseInvoice?->invoice_date ? \Carbon\Carbon::parse($pi->purchaseInvoice->invoice_date)->format('d.m.Y') : '-' }}</td>
                                <td class="px-3 py-2">
                                    @if($pi->purchaseInvoice)
                                        <a href="{{ route('invoices.show', $pi->purchase_invoice_id) }}" class="text-indigo-600 hover:underline">{{ $pi->purchaseInvoice->invoice_no ?: '#' . $pi->purchase_invoice_id }}</a>
                                    @else - @endif
                                </td>
                                <td class="px-3 py-2 text-gray-600">{{ $pi->purchaseInvoice?->firm?->name ?? '-' }}</td>
                                <td class="px-3 py-2 text-right">{{ $pi->quantity }}</td>
                                <td class="px-3 py-2 text-right">₺{{ number_format($pi->unit_price, 2, ',', '.') }}</td>
                                <td class="px-3 py-2 text-right font-medium">₺{{ number_format($pi->total, 2, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-3 py-8 text-center text-gray-400">
                                <i class="fas fa-file-invoice text-2xl mb-2"></i><p>Alış faturası kaydı bulunamadı</p>
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@if($monthlySales->count() > 0)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('salesTrendChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($monthlySales->pluck('month')->map(function($m) { return \Carbon\Carbon::createFromFormat('Y-m', $m)->translatedFormat('M Y'); })) !!},
            datasets: [{
                label: 'Satış Miktarı',
                data: {!! json_encode($monthlySales->pluck('qty')) !!},
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                fill: true,
                tension: 0.4,
                yAxisID: 'y',
            }, {
                label: 'Gelir (₺)',
                data: {!! json_encode($monthlySales->pluck('revenue')) !!},
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                fill: true,
                tension: 0.4,
                yAxisID: 'y1',
            }]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: { legend: { position: 'top' } },
            scales: {
                y: { type: 'linear', display: true, position: 'left', title: { display: true, text: 'Adet' } },
                y1: { type: 'linear', display: true, position: 'right', grid: { drawOnChartArea: false }, title: { display: true, text: 'Gelir (₺)' } }
            }
        }
    });
});
</script>
@endif
@endsection
