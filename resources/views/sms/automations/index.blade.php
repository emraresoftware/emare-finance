@extends('layouts.app')
@section('title', 'SMS Otomasyonları')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">SMS Otomasyonları</h1>
        <p class="text-sm text-gray-500 mt-1">Otomatik SMS gönderimlerini yapılandırın ve yönetin</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('sms.automations.queue') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
            <i class="fas fa-list-ol mr-2"></i> Gönderim Kuyruğu
        </a>
        <a href="{{ route('sms.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
            <i class="fas fa-arrow-left mr-2"></i> SMS Dashboard
        </a>
    </div>
</div>

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

{{-- Kuyruk İstatistikleri --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Bugün Gönderilen</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($queueStats['today']) }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center">
                <i class="fas fa-paper-plane text-green-600 text-lg"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Toplam Gönderilen</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($queueStats['sent']) }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                <i class="fas fa-check-double text-blue-600 text-lg"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Bekleyen</p>
                <p class="text-2xl font-bold text-yellow-600 mt-1">{{ number_format($queueStats['pending']) }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-yellow-100 flex items-center justify-center">
                <i class="fas fa-clock text-yellow-600 text-lg"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Başarısız</p>
                <p class="text-2xl font-bold text-red-600 mt-1">{{ number_format($queueStats['failed']) }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-red-600 text-lg"></i>
            </div>
        </div>
    </div>
</div>

{{-- Aktif Otomasyon Sayacı --}}
@php
    $activeCount = $configs->where('is_active', true)->count();
    $totalCount = count($automationTypes);
@endphp
<div class="mb-6 bg-indigo-50 border border-indigo-200 rounded-xl p-4 flex items-center gap-3">
    <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
        <i class="fas fa-robot text-indigo-600"></i>
    </div>
    <div>
        <p class="text-sm font-medium text-indigo-800">{{ $activeCount }} / {{ $totalCount }} otomasyon aktif</p>
        <p class="text-xs text-indigo-600 mt-0.5">Otomasyonlar <code class="bg-indigo-100 px-1.5 py-0.5 rounded text-xs">sms:process-automations</code> artisan komutuyla veya cron ile çalıştırılır.</p>
    </div>
</div>

{{-- Otomasyon Kartları --}}
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
    @foreach($automationTypes as $type => $typeConfig)
        @php
            $config = $configs[$type] ?? null;
            $isActive = $config?->is_active ?? false;
            $color = $typeConfig['color'];

            $colorMap = [
                'pink'    => ['bg' => 'bg-pink-100', 'text' => 'text-pink-600', 'border' => 'border-pink-200', 'ring' => 'ring-pink-500'],
                'green'   => ['bg' => 'bg-green-100', 'text' => 'text-green-600', 'border' => 'border-green-200', 'ring' => 'ring-green-500'],
                'blue'    => ['bg' => 'bg-blue-100', 'text' => 'text-blue-600', 'border' => 'border-blue-200', 'ring' => 'ring-blue-500'],
                'orange'  => ['bg' => 'bg-orange-100', 'text' => 'text-orange-600', 'border' => 'border-orange-200', 'ring' => 'ring-orange-500'],
                'emerald' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'border' => 'border-emerald-200', 'ring' => 'ring-emerald-500'],
                'yellow'  => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-600', 'border' => 'border-yellow-200', 'ring' => 'ring-yellow-500'],
                'teal'    => ['bg' => 'bg-teal-100', 'text' => 'text-teal-600', 'border' => 'border-teal-200', 'ring' => 'ring-teal-500'],
                'red'     => ['bg' => 'bg-red-100', 'text' => 'text-red-600', 'border' => 'border-red-200', 'ring' => 'ring-red-500'],
                'amber'   => ['bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'border' => 'border-amber-200', 'ring' => 'ring-amber-500'],
                'purple'  => ['bg' => 'bg-purple-100', 'text' => 'text-purple-600', 'border' => 'border-purple-200', 'ring' => 'ring-purple-500'],
                'indigo'  => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-600', 'border' => 'border-indigo-200', 'ring' => 'ring-indigo-500'],
                'violet'  => ['bg' => 'bg-violet-100', 'text' => 'text-violet-600', 'border' => 'border-violet-200', 'ring' => 'ring-violet-500'],
                'slate'   => ['bg' => 'bg-slate-100', 'text' => 'text-slate-600', 'border' => 'border-slate-200', 'ring' => 'ring-slate-500'],
                'cyan'    => ['bg' => 'bg-cyan-100', 'text' => 'text-cyan-600', 'border' => 'border-cyan-200', 'ring' => 'ring-cyan-500'],
            ];

            $colors = $colorMap[$color] ?? $colorMap['blue'];
        @endphp

        <div x-data="{ expanded: false }" class="bg-white rounded-xl border {{ $isActive ? $colors['border'] : 'border-gray-200' }} overflow-hidden transition-all duration-200 {{ $isActive ? 'ring-1 ' . $colors['ring'] . '/30' : '' }}">
            {{-- Card Header --}}
            <div class="p-5">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl {{ $colors['bg'] }} flex items-center justify-center">
                            <i class="{{ $typeConfig['icon'] }} {{ $colors['text'] }} text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 text-sm">{{ $typeConfig['name'] }}</h3>
                            <span class="text-xs {{ $isActive ? 'text-green-600' : 'text-gray-400' }}">
                                <i class="fas fa-circle text-[6px] mr-1"></i>
                                {{ $isActive ? 'Aktif' : 'Pasif' }}
                            </span>
                        </div>
                    </div>

                    {{-- Toggle Switch --}}
                    <form method="POST" action="{{ route('sms.automations.toggle', $type) }}">
                        @csrf
                        <button type="submit" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors {{ $isActive ? 'bg-indigo-600' : 'bg-gray-300' }}" title="{{ $isActive ? 'Pasif Yap' : 'Aktif Yap' }}">
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform {{ $isActive ? 'translate-x-6' : 'translate-x-1' }}"></span>
                        </button>
                    </form>
                </div>

                <p class="text-xs text-gray-500 leading-relaxed mb-3">{{ $typeConfig['description'] }}</p>

                {{-- Stats --}}
                @if($config)
                <div class="flex items-center gap-4 text-xs text-gray-400">
                    <span><i class="fas fa-paper-plane mr-1"></i> {{ number_format($config->sent_count ?? 0) }} gönderim</span>
                    @if($config->last_run_at)
                        <span><i class="fas fa-clock mr-1"></i> {{ $config->last_run_at->diffForHumans() }}</span>
                    @endif
                    @if($config->send_time)
                        <span><i class="fas fa-bell mr-1"></i> {{ \Carbon\Carbon::parse($config->send_time)->format('H:i') }}</span>
                    @endif
                </div>
                @endif
            </div>

            {{-- Expand Button --}}
            <div class="border-t border-gray-100 px-5 py-2">
                <button @click="expanded = !expanded" class="w-full flex items-center justify-between text-xs text-gray-500 hover:text-gray-700 transition py-1">
                    <span x-text="expanded ? 'Ayarları Gizle' : 'Ayarları Düzenle'"></span>
                    <i :class="expanded ? 'fa-chevron-up' : 'fa-chevron-down'" class="fas text-[10px]"></i>
                </button>
            </div>

            {{-- Settings Panel --}}
            <div x-show="expanded" x-cloak x-collapse class="border-t border-gray-100 bg-gray-50 p-5">
                <form method="POST" action="{{ route('sms.automations.update', $type) }}">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        {{-- Şablon Seçimi --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">SMS Şablonu</label>
                            <select name="template_id" class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                @foreach($templates as $template)
                                    <option value="{{ $template->id }}" {{ ($config?->template_id == $template->id) ? 'selected' : '' }}>
                                        {{ $template->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Gönderim Saati --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Gönderim Saati</label>
                            <input type="time" name="send_time" value="{{ $config ? \Carbon\Carbon::parse($config->send_time)->format('H:i') : '10:00' }}" class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        {{-- Tip'e Göre Özel Alanlar --}}
                        @if(in_array($type, ['birthday', 'appointment_reminder']))
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Kaç Gün Önce Gönderilsin</label>
                            <input type="number" name="days_before" value="{{ $config?->days_before ?? 0 }}" min="0" max="30" class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <p class="text-xs text-gray-400 mt-1">0 = aynı gün gönderilir</p>
                        </div>
                        @endif

                        @if($type === 'inactivity')
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Pasiflik Süresi (Gün)</label>
                            <input type="number" name="inactive_days" value="{{ $config?->inactive_days ?? 60 }}" min="1" max="365" class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <p class="text-xs text-gray-400 mt-1">Bu süre boyunca alışveriş yapmayan müşterilere gönderilir</p>
                        </div>
                        @endif

                        @if($type === 'payment_reminder')
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Ödeme Vadesi (Gün Sonra)</label>
                            <input type="number" name="days_after" value="{{ $config?->days_after ?? 7 }}" min="0" max="30" class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        @endif

                        {{-- Şablon Önizleme --}}
                        @if($config?->template)
                        <div class="bg-white rounded-lg border border-gray-200 p-3">
                            <p class="text-xs font-medium text-gray-500 mb-1">Şablon Önizleme</p>
                            <p class="text-sm text-gray-700">{{ $config->template->content }}</p>
                        </div>
                        @endif
                    </div>

                    <div class="mt-4 flex items-center justify-between">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-xs font-medium rounded-lg hover:bg-indigo-700 transition">
                            <i class="fas fa-save mr-1.5"></i> Kaydet
                        </button>

                        @if($isActive)
                        <form method="POST" action="{{ route('sms.automations.run', $type) }}" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-xs font-medium rounded-lg hover:bg-green-700 transition" onclick="return confirm('Bu otomasyonu şimdi çalıştırmak istediğinize emin misiniz?')">
                                <i class="fas fa-play mr-1.5"></i> Şimdi Çalıştır
                            </button>
                        </form>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    @endforeach
</div>

{{-- Son 7 Gün Grafiği --}}
@if($dailyAutomationStats->count() > 0)
<div class="mt-8 bg-white rounded-xl border border-gray-200 p-6">
    <h3 class="text-sm font-semibold text-gray-900 mb-4">
        <i class="fas fa-chart-line text-indigo-500 mr-2"></i>Son 7 Gün Otomasyon Gönderimi
    </h3>
    <canvas id="automationChart" height="80"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const data = @json($dailyAutomationStats);
    const labels = data.map(d => {
        const date = new Date(d.date);
        return date.toLocaleDateString('tr-TR', { day: '2-digit', month: 'short' });
    });
    const values = data.map(d => d.count);

    new Chart(document.getElementById('automationChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Gönderilen SMS',
                data: values,
                backgroundColor: 'rgba(99, 102, 241, 0.2)',
                borderColor: 'rgba(99, 102, 241, 1)',
                borderWidth: 2,
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, font: { size: 11 } },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: {
                    ticks: { font: { size: 11 } },
                    grid: { display: false }
                }
            }
        }
    });
});
</script>
@endif
@endsection
