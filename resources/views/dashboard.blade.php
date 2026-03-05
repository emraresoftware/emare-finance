@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
{{-- İstatistik Kartları --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Toplam Gelir</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">₺{{ number_format($stats['total_revenue'], 2, ',', '.') }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-turkish-lira-sign text-green-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-3 flex items-center text-sm">
            <span class="text-gray-500">Bugün: ₺{{ number_format($stats['today_revenue'], 2, ',', '.') }}</span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Aylık Gelir</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">₺{{ number_format($stats['month_revenue'], 2, ',', '.') }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-chart-line text-blue-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-3 text-sm text-gray-500">{{ now()->locale('tr')->translatedFormat('F Y') }}</div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Toplam Ürün</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_products']) }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-boxes-stacked text-purple-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-3 text-sm">
            @if($stats['low_stock_count'] > 0)
                <span class="text-red-500"><i class="fas fa-exclamation-triangle mr-1"></i>{{ $stats['low_stock_count'] }} düşük stok</span>
            @else
                <span class="text-green-500"><i class="fas fa-check mr-1"></i>Stoklar normal</span>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Toplam Satış</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_sales']) }}</p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-receipt text-orange-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-3 text-sm text-gray-500">{{ $stats['total_customers'] }} müşteri</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Satış Grafiği --}}
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Son 7 Gün Satışlar</h3>
        <canvas id="salesChart" height="120"></canvas>
    </div>

    {{-- Son Satışlar --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Son Satışlar</h3>
        <div class="space-y-3">
            @forelse($recentSales as $sale)
                <a href="{{ route('sales.show', $sale) }}" class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition">
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $sale->receipt_no ?? '#' . $sale->id }}</p>
                        <p class="text-xs text-gray-500">{{ $sale->sold_at?->format('d.m.Y H:i') }}</p>
                    </div>
                    <span class="text-sm font-semibold text-green-600">₺{{ number_format($sale->grand_total, 2, ',', '.') }}</span>
                </a>
            @empty
                <p class="text-sm text-gray-400 text-center py-4">Henüz satış verisi yok.</p>
            @endforelse
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
    {{-- Düşük Stok Uyarıları --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>Düşük Stok Uyarıları
        </h3>
        @if($lowStockProducts->count() > 0)
            <div class="space-y-2">
                @foreach($lowStockProducts as $product)
                    <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $product->name }}</p>
                            <p class="text-xs text-gray-500">{{ $product->barcode }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-red-600">{{ $product->stock_quantity }} {{ $product->unit }}</p>
                            <p class="text-xs text-gray-400">Min: {{ $product->critical_stock }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-400 text-center py-4">Düşük stoklu ürün yok.</p>
        @endif
    </div>

    {{-- Kategori Bazlı Satışlar --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Kategori Bazlı Satışlar</h3>
        @if($categorySales->count() > 0)
            <canvas id="categoryChart" height="200"></canvas>
        @else
            <p class="text-sm text-gray-400 text-center py-4">Henüz veri yok.</p>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
// Satış Grafiği
const salesCtx = document.getElementById('salesChart');
if (salesCtx) {
    new Chart(salesCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($dailySales->pluck('date')) !!},
            datasets: [{
                label: 'Gelir (₺)',
                data: {!! json_encode($dailySales->pluck('total')) !!},
                backgroundColor: 'rgba(79, 70, 229, 0.8)',
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: value => '₺' + value.toLocaleString('tr-TR')
                    }
                }
            }
        }
    });
}

// Kategori Grafiği
const catCtx = document.getElementById('categoryChart');
if (catCtx) {
    new Chart(catCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($categorySales->pluck('name')) !!},
            datasets: [{
                data: {!! json_encode($categorySales->pluck('total')) !!},
                backgroundColor: [
                    '#4F46E5', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6',
                    '#EC4899', '#06B6D4', '#84CC16', '#F97316', '#6366F1',
                ],
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom', labels: { font: { size: 11 } } }
            }
        }
    });
}
</script>
@endpush
