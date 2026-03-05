@extends('layouts.app')
@section('title', 'Mesaj - ' . $message->title)

@section('content')
<div class="space-y-6">
    {{-- Başlık --}}
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('marketing.messages.index') }}" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>
                <div>
                    <h2 class="text-lg font-bold text-gray-800">{{ $message->title }}</h2>
                    <div class="flex items-center gap-2 mt-1">
                        @php
                            $channelIcon = match($message->channel) {
                                'sms' => 'sms',
                                'email' => 'envelope',
                                'push' => 'bell',
                                default => 'comment'
                            };
                            $channelColor = match($message->channel) {
                                'sms' => 'green',
                                'email' => 'blue',
                                'push' => 'orange',
                                default => 'gray'
                            };
                        @endphp
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $channelColor }}-100 text-{{ $channelColor }}-700">
                            <i class="fas fa-{{ $channelIcon }}"></i> {{ $message->channel_label }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $message->status_color ?? 'gray' }}-100 text-{{ $message->status_color ?? 'gray' }}-800">
                            {{ $message->status_label }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="flex gap-2">
                @if($message->status === 'draft')
                    <form method="POST" action="{{ route('marketing.messages.send', $message) }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 font-medium" onclick="return confirm('Mesajı göndermek istediğinize emin misiniz?')">
                            <i class="fas fa-paper-plane mr-1"></i> Gönder
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    {{-- İstatistik Kartları --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-white rounded-lg shadow-sm p-4 border">
            <p class="text-xs text-gray-500">Toplam Alıcı</p>
            <p class="text-xl font-bold text-gray-800">{{ number_format($message->total_recipients) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border">
            <p class="text-xs text-gray-500">Gönderildi</p>
            <p class="text-xl font-bold text-blue-600">{{ number_format($message->sent_count) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border">
            <p class="text-xs text-gray-500">Teslim Edildi</p>
            <p class="text-xl font-bold text-green-600">{{ number_format($message->delivered_count) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border">
            <p class="text-xs text-gray-500">Açıldı</p>
            <p class="text-xl font-bold text-purple-600">{{ number_format($message->opened_count) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border">
            <p class="text-xs text-gray-500">Açılma Oranı</p>
            <p class="text-xl font-bold text-indigo-600">%{{ number_format($message->open_rate, 1) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border">
            <p class="text-xs text-gray-500">Tıklama Oranı</p>
            <p class="text-xl font-bold text-orange-600">%{{ number_format($message->click_rate, 1) }}</p>
        </div>
    </div>

    {{-- Performans Barları --}}
    @if($message->total_recipients > 0)
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="text-md font-semibold text-gray-800 mb-4"><i class="fas fa-chart-bar text-indigo-500 mr-2"></i>Performans</h3>
            <div class="space-y-4">
                <div>
                    <div class="flex items-center justify-between text-sm mb-1">
                        <span class="text-gray-600">Gönderilme</span>
                        <span class="font-medium">{{ number_format($message->sent_count) }} / {{ number_format($message->total_recipients) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $message->total_recipients > 0 ? ($message->sent_count / $message->total_recipients) * 100 : 0 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between text-sm mb-1">
                        <span class="text-gray-600">Teslim</span>
                        <span class="font-medium">{{ number_format($message->delivered_count) }} / {{ number_format($message->sent_count) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ $message->sent_count > 0 ? ($message->delivered_count / $message->sent_count) * 100 : 0 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between text-sm mb-1">
                        <span class="text-gray-600">Açılma</span>
                        <span class="font-medium">{{ number_format($message->opened_count) }} / {{ number_format($message->delivered_count) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $message->open_rate }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between text-sm mb-1">
                        <span class="text-gray-600">Tıklama</span>
                        <span class="font-medium">{{ number_format($message->clicked_count) }} / {{ number_format($message->opened_count) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-orange-600 h-2 rounded-full" style="width: {{ $message->click_rate }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Sol: İçerik & Loglar --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Mesaj İçeriği --}}
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="text-md font-semibold text-gray-800 mb-4"><i class="fas fa-pen text-indigo-500 mr-2"></i>Mesaj İçeriği</h3>
                <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-700 whitespace-pre-line">{{ $message->content }}</div>
            </div>

            {{-- Gönderim Logları --}}
            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                <div class="p-6 pb-0">
                    <h3 class="text-md font-semibold text-gray-800 mb-4"><i class="fas fa-list text-indigo-500 mr-2"></i>Gönderim Logları</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alıcı</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Durum</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Gönderim</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Teslim</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Açılma</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($message->logs as $log)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <span class="font-medium text-gray-800">{{ $log->recipient_name ?? $log->recipient ?? '-' }}</span>
                                        @if($log->recipient_address)
                                            <div class="text-xs text-gray-400">{{ $log->recipient_address }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($log->status === 'delivered')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                                <i class="fas fa-check mr-1"></i>Teslim
                                            </span>
                                        @elseif($log->status === 'sent')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                                <i class="fas fa-paper-plane mr-1"></i>Gönderildi
                                            </span>
                                        @elseif($log->status === 'failed')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                                <i class="fas fa-times mr-1"></i>Başarısız
                                            </span>
                                        @elseif($log->status === 'opened')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                                                <i class="fas fa-envelope-open mr-1"></i>Açıldı
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                {{ $log->status ?? 'Bekliyor' }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center text-xs text-gray-500">{{ $log->sent_at?->format('d.m H:i') ?? '-' }}</td>
                                    <td class="px-4 py-3 text-center text-xs text-gray-500">{{ $log->delivered_at?->format('d.m H:i') ?? '-' }}</td>
                                    <td class="px-4 py-3 text-center text-xs text-gray-500">{{ $log->opened_at?->format('d.m H:i') ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">
                                    <i class="fas fa-clipboard-list text-3xl mb-2"></i><p>Henüz gönderim logu yok</p>
                                </td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Sağ Panel --}}
        <div class="space-y-6">
            {{-- Mesaj Detayları --}}
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="text-md font-semibold text-gray-800 mb-4">Mesaj Detayları</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Kanal</span>
                        <span class="font-medium">{{ $message->channel_label }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Durum</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $message->status_color ?? 'gray' }}-100 text-{{ $message->status_color ?? 'gray' }}-800">
                            {{ $message->status_label }}
                        </span>
                    </div>
                    @if($message->segment)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Segment</span>
                            <a href="{{ route('marketing.segments.show', $message->segment) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">{{ $message->segment->name }}</a>
                        </div>
                    @endif
                    @if($message->campaign)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Kampanya</span>
                            <a href="{{ route('marketing.campaigns.show', $message->campaign) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">{{ $message->campaign->name }}</a>
                        </div>
                    @endif
                    @if($message->scheduled_at)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Planlanan</span>
                            <span>{{ $message->scheduled_at->format('d.m.Y H:i') }}</span>
                        </div>
                    @endif
                    @if($message->sent_at)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Gönderilme</span>
                            <span>{{ $message->sent_at->format('d.m.Y H:i') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-500">Oluşturulma</span>
                        <span>{{ $message->created_at->format('d.m.Y H:i') }}</span>
                    </div>
                </div>
            </div>

            {{-- Huni Grafiği --}}
            @if($message->total_recipients > 0)
                <div class="bg-white rounded-xl shadow-sm border p-6">
                    <h3 class="text-md font-semibold text-gray-800 mb-4">Dönüşüm Hunisi</h3>
                    <div class="space-y-2">
                        @php
                            $maxWidth = $message->total_recipients;
                            $stages = [
                                ['label' => 'Alıcı', 'count' => $message->total_recipients, 'color' => 'bg-gray-400'],
                                ['label' => 'Gönderildi', 'count' => $message->sent_count, 'color' => 'bg-blue-500'],
                                ['label' => 'Teslim', 'count' => $message->delivered_count, 'color' => 'bg-green-500'],
                                ['label' => 'Açıldı', 'count' => $message->opened_count, 'color' => 'bg-purple-500'],
                                ['label' => 'Tıklandı', 'count' => $message->clicked_count, 'color' => 'bg-orange-500'],
                            ];
                        @endphp
                        @foreach($stages as $stage)
                            <div class="flex items-center gap-3">
                                <span class="text-xs text-gray-500 w-16 text-right">{{ $stage['label'] }}</span>
                                <div class="flex-1">
                                    <div class="{{ $stage['color'] }} h-6 rounded flex items-center justify-end pr-2"
                                         style="width: {{ $maxWidth > 0 ? max(5, ($stage['count'] / $maxWidth) * 100) : 0 }}%">
                                        <span class="text-white text-xs font-medium">{{ number_format($stage['count']) }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
