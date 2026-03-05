@extends('layouts.app')
@section('title', 'Kampanya - ' . $campaign->name)

@section('content')
<div class="space-y-6">
    {{-- Başlık --}}
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('marketing.campaigns.index') }}" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>
                <div>
                    <h2 class="text-lg font-bold text-gray-800">{{ $campaign->name }}</h2>
                    <p class="text-sm text-gray-500">{{ $campaign->description }}</p>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $campaign->status_color }}-100 text-{{ $campaign->status_color }}-800">
                    {{ $campaign->status_label }}
                </span>
            </div>
            <div class="flex gap-2">
                <form method="POST" action="{{ route('marketing.campaigns.toggle', $campaign) }}" class="inline">
                    @csrf
                    <button type="submit" class="{{ $campaign->status === 'active' ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white px-4 py-2 rounded-lg text-sm font-medium">
                        @if($campaign->status === 'active')
                            <i class="fas fa-pause mr-1"></i> Durdur
                        @else
                            <i class="fas fa-play mr-1"></i> Aktifleştir
                        @endif
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- İstatistik Kartları --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">İndirim</p>
                    <p class="text-2xl font-bold text-indigo-600">
                        @if($campaign->discount_type === 'percentage')
                            %{{ $campaign->discount_value }}
                        @else
                            ₺{{ number_format($campaign->discount_value, 2, ',', '.') }}
                        @endif
                    </p>
                </div>
                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-percent text-indigo-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Kullanım</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $campaign->usages_count ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-bar text-blue-600"></i>
                </div>
            </div>
            @if($campaign->usage_limit)
                <div class="mt-2">
                    <div class="w-full bg-gray-200 rounded-full h-1.5">
                        <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ min(100, (($campaign->usages_count ?? 0) / $campaign->usage_limit) * 100) }}%"></div>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">{{ $campaign->usages_count ?? 0 }} / {{ $campaign->usage_limit }}</p>
                </div>
            @endif
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Başlangıç</p>
                    <p class="text-lg font-bold text-gray-800">{{ $campaign->starts_at?->format('d.m.Y') ?? 'Süresiz' }}</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-green-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Bitiş</p>
                    <p class="text-lg font-bold {{ $campaign->ends_at && $campaign->ends_at->isPast() ? 'text-red-600' : 'text-gray-800' }}">
                        {{ $campaign->ends_at?->format('d.m.Y') ?? 'Süresiz' }}
                    </p>
                </div>
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-check text-red-600"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Sol: Kullanım Grafiği & Tablo --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Günlük Kullanım Grafiği --}}
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="text-md font-semibold text-gray-800 mb-4"><i class="fas fa-chart-line text-green-500 mr-2"></i>Günlük Kullanım</h3>
                <canvas id="usageChart" height="100"></canvas>
            </div>

            {{-- Son Kullanımlar --}}
            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                <div class="p-6 pb-0">
                    <h3 class="text-md font-semibold text-gray-800 mb-4"><i class="fas fa-list text-green-500 mr-2"></i>Son Kullanımlar</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Müşteri</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">İndirim Tutarı</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Tarih</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($campaign->usages as $usage)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <span class="font-medium text-gray-800">{{ $usage->customer->name ?? 'Bilinmeyen' }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-right text-red-600 font-medium">
                                        -₺{{ number_format($usage->discount_amount ?? 0, 2, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-gray-500">{{ $usage->created_at->format('d.m.Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-4 py-8 text-center text-gray-400">
                                    <i class="fas fa-chart-bar text-3xl mb-2"></i><p>Henüz kullanım yok</p>
                                </td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Sağ Panel --}}
        <div class="space-y-6">
            {{-- Kampanya Detayları --}}
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="text-md font-semibold text-gray-800 mb-4">Kampanya Detayları</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Tür</span>
                        <span class="font-medium">{{ $campaign->type_label }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">İndirim</span>
                        <span class="font-medium text-indigo-600">
                            @if($campaign->discount_type === 'percentage')
                                %{{ $campaign->discount_value }}
                            @else
                                ₺{{ number_format($campaign->discount_value, 2, ',', '.') }}
                            @endif
                        </span>
                    </div>
                    @if($campaign->coupon_code)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Kupon Kodu</span>
                            <code class="bg-gray-100 px-2 py-0.5 rounded font-mono text-xs">{{ $campaign->coupon_code }}</code>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-500">Kullanım Sayısı</span>
                        <span>{{ $campaign->usages_count ?? 0 }}{{ $campaign->usage_limit ? ' / ' . $campaign->usage_limit : '' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Oluşturulma</span>
                        <span>{{ $campaign->created_at->format('d.m.Y H:i') }}</span>
                    </div>
                </div>
            </div>

            {{-- İlişkili Mesajlar --}}
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="text-md font-semibold text-gray-800 mb-4">Kampanya Mesajları</h3>
                <div class="space-y-3">
                    @forelse($campaign->messages as $message)
                        <a href="{{ route('marketing.messages.show', $message) }}" class="block p-3 rounded-lg border hover:bg-gray-50 transition">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-800">{{ $message->title }}</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $message->status_color ?? 'gray' }}-100 text-{{ $message->status_color ?? 'gray' }}-800">
                                    {{ $message->status_label }}
                                </span>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-users mr-1"></i>{{ $message->total_recipients }} alıcı
                                @if($message->sent_at)
                                    · {{ $message->sent_at->format('d.m.Y') }}
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-4 text-gray-400 text-sm">
                            <p>Henüz mesaj yok</p>
                        </div>
                    @endforelse
                    <a href="{{ route('marketing.messages.create', ['campaign_id' => $campaign->id]) }}" class="block text-center py-2 text-sm text-indigo-600 hover:text-indigo-800 border border-dashed rounded-lg hover:bg-indigo-50 transition">
                        <i class="fas fa-plus mr-1"></i> Kampanya Mesajı Oluştur
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dailyUsages = @json($dailyUsages ?? []);
    if (dailyUsages.length === 0) return;

    const ctx = document.getElementById('usageChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: dailyUsages.map(d => d.date),
            datasets: [{
                label: 'Kullanım',
                data: dailyUsages.map(d => d.count),
                backgroundColor: 'rgba(34, 197, 94, 0.5)',
                borderColor: 'rgb(34, 197, 94)',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });
});
</script>
@endpush
@endsection
