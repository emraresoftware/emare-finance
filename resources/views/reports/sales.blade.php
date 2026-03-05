@extends('layouts.app')
@section('title', 'Satış Raporu')

@section('content')
<div class="bg-white rounded-xl shadow-sm p-4 mb-6 border">
    <form method="GET" class="flex flex-wrap items-end gap-4">
        <div><label class="block text-xs text-gray-500 mb-1">Başlangıç</label>
            <input type="date" name="start_date" value="{{ $startDate }}" class="border rounded-lg px-3 py-2 text-sm"></div>
        <div><label class="block text-xs text-gray-500 mb-1">Bitiş</label>
            <input type="date" name="end_date" value="{{ $endDate }}" class="border rounded-lg px-3 py-2 text-sm"></div>
        <div><label class="block text-xs text-gray-500 mb-1">Gruplama</label>
            <select name="group_by" class="border rounded-lg px-3 py-2 text-sm">
                <option value="day" {{ $groupBy == 'day' ? 'selected' : '' }}>Günlük</option>
                <option value="week" {{ $groupBy == 'week' ? 'selected' : '' }}>Haftalık</option>
                <option value="month" {{ $groupBy == 'month' ? 'selected' : '' }}>Aylık</option>
            </select></div>
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm">Raporla</button>
    </form>
</div>

{{-- Özet --}}
<div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
    <div class="bg-white rounded-lg p-4 border"><p class="text-xs text-gray-500">Toplam Gelir</p><p class="text-xl font-bold text-green-600">₺{{ number_format($summary['total_revenue'], 2, ',', '.') }}</p></div>
    <div class="bg-white rounded-lg p-4 border"><p class="text-xs text-gray-500">Toplam Satış</p><p class="text-xl font-bold">{{ number_format($summary['total_sales']) }}</p></div>
    <div class="bg-white rounded-lg p-4 border"><p class="text-xs text-gray-500">Ort. Günlük</p><p class="text-xl font-bold text-blue-600">₺{{ number_format($summary['avg_daily'], 2, ',', '.') }}</p></div>
    <div class="bg-white rounded-lg p-4 border"><p class="text-xs text-gray-500">Toplam İndirim</p><p class="text-xl font-bold text-red-600">₺{{ number_format($summary['total_discounts'], 2, ',', '.') }}</p></div>
    <div class="bg-white rounded-lg p-4 border"><p class="text-xs text-gray-500">Toplam KDV</p><p class="text-xl font-bold">₺{{ number_format($summary['total_vat'], 2, ',', '.') }}</p></div>
</div>

{{-- Grafik --}}
<div class="bg-white rounded-xl shadow-sm p-6 border mb-6">
    <canvas id="reportChart" height="100"></canvas>
</div>

{{-- Tablo --}}
<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50"><tr>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dönem</th>
            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Satış Sayısı</th>
            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Gelir</th>
            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">İndirim</th>
            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">KDV</th>
            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ort. Satış</th>
        </tr></thead>
        <tbody class="divide-y">
            @foreach($salesData as $row)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium">{{ $row->period }}</td>
                    <td class="px-4 py-3 text-right">{{ $row->sale_count }}</td>
                    <td class="px-4 py-3 text-right font-semibold text-green-600">₺{{ number_format($row->revenue, 2, ',', '.') }}</td>
                    <td class="px-4 py-3 text-right text-red-500">₺{{ number_format($row->discounts, 2, ',', '.') }}</td>
                    <td class="px-4 py-3 text-right">₺{{ number_format($row->vat, 2, ',', '.') }}</td>
                    <td class="px-4 py-3 text-right">₺{{ number_format($row->avg_sale, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
new Chart(document.getElementById('reportChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($salesData->pluck('period')) !!},
        datasets: [{
            label: 'Gelir (₺)',
            data: {!! json_encode($salesData->pluck('revenue')) !!},
            borderColor: '#4F46E5', backgroundColor: 'rgba(79,70,229,0.1)',
            fill: true, tension: 0.3,
        }]
    },
    options: {
        responsive: true,
        scales: { y: { beginAtZero: true, ticks: { callback: v => '₺' + v.toLocaleString('tr-TR') } } }
    }
});
</script>
@endpush
