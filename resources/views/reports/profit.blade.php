@extends('layouts.app')
@section('title', 'Kâr Analizi')

@section('content')
<div class="bg-white rounded-xl shadow-sm p-4 mb-6 border">
    <form method="GET" class="flex flex-wrap items-end gap-4">
        <div><label class="block text-xs text-gray-500 mb-1">Başlangıç</label>
            <input type="date" name="start_date" value="{{ $startDate }}" class="border rounded-lg px-3 py-2 text-sm"></div>
        <div><label class="block text-xs text-gray-500 mb-1">Bitiş</label>
            <input type="date" name="end_date" value="{{ $endDate }}" class="border rounded-lg px-3 py-2 text-sm"></div>
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm">Hesapla</button>
    </form>
</div>

@if($profitData)
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-sm p-8 border text-center">
        <div class="w-16 h-16 bg-blue-100 rounded-2xl mx-auto flex items-center justify-center mb-4">
            <i class="fas fa-hand-holding-dollar text-blue-600 text-2xl"></i>
        </div>
        <p class="text-sm text-gray-500">Toplam Gelir</p>
        <p class="text-3xl font-bold text-gray-900 mt-2">₺{{ number_format($profitData->total_revenue ?? 0, 2, ',', '.') }}</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-8 border text-center">
        <div class="w-16 h-16 bg-red-100 rounded-2xl mx-auto flex items-center justify-center mb-4">
            <i class="fas fa-money-bill-transfer text-red-600 text-2xl"></i>
        </div>
        <p class="text-sm text-gray-500">Toplam Maliyet</p>
        <p class="text-3xl font-bold text-red-600 mt-2">₺{{ number_format($profitData->total_cost ?? 0, 2, ',', '.') }}</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-8 border text-center">
        <div class="w-16 h-16 bg-green-100 rounded-2xl mx-auto flex items-center justify-center mb-4">
            <i class="fas fa-piggy-bank text-green-600 text-2xl"></i>
        </div>
        <p class="text-sm text-gray-500">Brüt Kâr</p>
        <p class="text-3xl font-bold text-green-600 mt-2">₺{{ number_format($profitData->gross_profit ?? 0, 2, ',', '.') }}</p>
        @if(($profitData->total_revenue ?? 0) > 0)
            <p class="text-sm text-gray-400 mt-1">
                Kâr Marjı: %{{ number_format(($profitData->gross_profit / $profitData->total_revenue) * 100, 1) }}
            </p>
        @endif
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm p-6 border">
    <canvas id="profitChart" height="100"></canvas>
</div>
@else
<div class="bg-white rounded-xl shadow-sm p-12 border text-center">
    <i class="fas fa-calculator text-gray-300 text-5xl mb-4"></i>
    <p class="text-gray-400">Henüz kâr verisi yok. Verileri senkronize edin.</p>
</div>
@endif
@endsection

@push('scripts')
@if($profitData)
<script>
new Chart(document.getElementById('profitChart'), {
    type: 'bar',
    data: {
        labels: ['Gelir', 'Maliyet', 'Brüt Kâr'],
        datasets: [{
            data: [{{ $profitData->total_revenue ?? 0 }}, {{ $profitData->total_cost ?? 0 }}, {{ $profitData->gross_profit ?? 0 }}],
            backgroundColor: ['#3B82F6', '#EF4444', '#10B981'],
            borderRadius: 8,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { callback: v => '₺' + v.toLocaleString('tr-TR') } } }
    }
});
</script>
@endif
@endpush
