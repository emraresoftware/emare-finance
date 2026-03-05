@extends('layouts.app')
@section('title', 'SMS Yönetimi')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">SMS Yönetimi</h1>
        <p class="text-sm text-gray-500 mt-1">SMS gönderim, şablonlar ve senaryoları yönetin</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('sms.compose') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
            <i class="fas fa-paper-plane mr-2"></i> Hızlı SMS Gönder
        </a>
        <a href="{{ route('sms.templates.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
            <i class="fas fa-file-alt mr-2"></i> Şablonlar
        </a>
        <a href="{{ route('sms.scenarios.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
            <i class="fas fa-cogs mr-2"></i> Senaryolar
        </a>
        <a href="{{ route('sms.settings') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
            <i class="fas fa-sliders-h mr-2"></i> Ayarlar
        </a>
    </div>
</div>

{{-- Durum Uyarısı --}}
@if(!$settings || !$settings->is_active)
    <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-xl p-4 flex items-start gap-3">
        <i class="fas fa-exclamation-triangle text-yellow-500 mt-0.5"></i>
        <div>
            <p class="text-sm font-medium text-yellow-800">SMS sağlayıcı yapılandırılmamış veya aktif değil.</p>
            <p class="text-sm text-yellow-600 mt-1">SMS gönderebilmek için lütfen <a href="{{ route('sms.settings') }}" class="underline font-medium">SMS Ayarları</a> sayfasından sağlayıcınızı yapılandırın.</p>
        </div>
    </div>
@endif

{{-- Başarı / Hata Mesajları --}}
@if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4 text-sm text-green-700">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">
        <i class="fas fa-times-circle mr-2"></i>{{ session('error') }}
    </div>
@endif

{{-- İstatistik Kartları --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    {{-- Gönderilen --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Toplam Gönderilen</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_sent']) }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-3 text-sm text-gray-500">Bugün: {{ number_format($stats['today_sent']) }}</div>
    </div>

    {{-- Başarısız --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Başarısız</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_failed']) }}</p>
            </div>
            <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-times-circle text-red-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-3 text-sm text-gray-500">Bekleyen: {{ number_format($stats['total_pending']) }}</div>
    </div>

    {{-- Maliyet --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Toplam Maliyet</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">₺{{ number_format($stats['total_cost'], 2, ',', '.') }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-turkish-lira-sign text-blue-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-3 text-sm text-gray-500">Bakiye: ₺{{ number_format($stats['balance'] ?? 0, 2, ',', '.') }}</div>
    </div>

    {{-- Senaryolar & Şablonlar --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Aktif Senaryo</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['active_scenarios']) }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-robot text-purple-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-3 text-sm text-gray-500">{{ number_format($stats['templates_count']) }} şablon</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Günlük Grafik --}}
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Son 30 Gün SMS İstatistikleri</h3>
        <canvas id="smsChart" height="120"></canvas>
    </div>

    {{-- Son SMS Logları --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Son Gönderimler</h3>
            <a href="{{ route('sms.logs.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">Tümünü Gör →</a>
        </div>
        <div class="space-y-3">
            @forelse($recentLogs as $log)
                <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 transition">
                    <div class="mt-0.5">
                        @switch($log->status)
                            @case('delivered')
                                <span class="w-8 h-8 flex items-center justify-center bg-green-100 rounded-full">
                                    <i class="fas fa-check text-green-600 text-xs"></i>
                                </span>
                                @break
                            @case('sent')
                                <span class="w-8 h-8 flex items-center justify-center bg-blue-100 rounded-full">
                                    <i class="fas fa-paper-plane text-blue-600 text-xs"></i>
                                </span>
                                @break
                            @case('failed')
                            @case('rejected')
                                <span class="w-8 h-8 flex items-center justify-center bg-red-100 rounded-full">
                                    <i class="fas fa-times text-red-600 text-xs"></i>
                                </span>
                                @break
                            @default
                                <span class="w-8 h-8 flex items-center justify-center bg-yellow-100 rounded-full">
                                    <i class="fas fa-clock text-yellow-600 text-xs"></i>
                                </span>
                        @endswitch
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $log->customer?->full_name ?? $log->phone }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ Str::limit($log->content, 50) }}</p>
                        <p class="text-xs text-gray-400 mt-1">
                            {{ $log->created_at->diffForHumans() }}
                            @if($log->scenario)
                                · <span class="text-indigo-500">{{ $log->scenario->name }}</span>
                            @endif
                        </p>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-400">
                    <i class="fas fa-sms text-3xl mb-2"></i>
                    <p class="text-sm">Henüz SMS gönderimi yok</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Grafik Script --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dailyStats = @json($dailyStats);
    const ctx = document.getElementById('smsChart')?.getContext('2d');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: dailyStats.map(s => s.date),
            datasets: [
                {
                    label: 'Gönderilen',
                    data: dailyStats.map(s => s.sent ?? 0),
                    backgroundColor: 'rgba(34, 197, 94, 0.7)',
                    borderRadius: 4,
                },
                {
                    label: 'Başarısız',
                    data: dailyStats.map(s => s.failed ?? 0),
                    backgroundColor: 'rgba(239, 68, 68, 0.7)',
                    borderRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } },
            scales: {
                x: { grid: { display: false } },
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });
});
</script>
@endpush
@endsection
