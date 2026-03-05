@extends('layouts.app')
@section('title', 'Mesajlar')

@section('content')
{{-- Başlık --}}
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('marketing.index') }}" class="text-gray-400 hover:text-gray-600">
            <i class="fas fa-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Pazarlama Mesajları</h1>
            <p class="text-sm text-gray-500 mt-1">SMS, e-posta ve bildirim mesajlarını yönetin</p>
        </div>
    </div>
    <a href="{{ route('marketing.messages.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 font-medium">
        <i class="fas fa-plus mr-1"></i> Yeni Mesaj
    </a>
</div>

{{-- Filtreler --}}
<div class="bg-white rounded-xl shadow-sm p-4 mb-6 border">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs text-gray-500 mb-1">Ara</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Mesaj başlığı..."
                   class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Kanal</label>
            <select name="channel" class="border rounded-lg px-3 py-2 text-sm">
                <option value="">Tümü</option>
                <option value="sms" {{ request('channel') == 'sms' ? 'selected' : '' }}>SMS</option>
                <option value="email" {{ request('channel') == 'email' ? 'selected' : '' }}>E-posta</option>
                <option value="push" {{ request('channel') == 'push' ? 'selected' : '' }}>Push Bildirim</option>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Durum</label>
            <select name="status" class="border rounded-lg px-3 py-2 text-sm">
                <option value="">Tümü</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Taslak</option>
                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Planlanmış</option>
                <option value="sending" {{ request('status') == 'sending' ? 'selected' : '' }}>Gönderiliyor</option>
                <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Gönderildi</option>
                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Başarısız</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">
            <i class="fas fa-search mr-1"></i> Filtrele
        </button>
        <a href="{{ route('marketing.messages.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm hover:bg-gray-200">Temizle</a>
    </form>
</div>

{{-- Tablo --}}
<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Başlık</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Kanal</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Durum</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Alıcı</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Gönderildi</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Açılma</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tıklama</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tarih</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">İşlem</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($messages as $message)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <a href="{{ route('marketing.messages.show', $message) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">{{ $message->title }}</a>
                        </td>
                        <td class="px-4 py-3 text-center">
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
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $message->status_color ?? 'gray' }}-100 text-{{ $message->status_color ?? 'gray' }}-800">
                                {{ $message->status_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ number_format($message->total_recipients) }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-gray-800 font-medium">{{ number_format($message->sent_count) }}</span>
                            <span class="text-gray-400">/{{ number_format($message->delivered_count) }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($message->open_rate > 0)
                                <span class="font-medium text-green-600">%{{ number_format($message->open_rate, 1) }}</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($message->click_rate > 0)
                                <span class="font-medium text-blue-600">%{{ number_format($message->click_rate, 1) }}</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center text-gray-500 text-xs">
                            @if($message->sent_at)
                                {{ $message->sent_at->format('d.m.Y H:i') }}
                            @elseif($message->scheduled_at)
                                <span class="text-yellow-600"><i class="fas fa-clock mr-1"></i>{{ $message->scheduled_at->format('d.m.Y H:i') }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('marketing.messages.show', $message) }}" class="p-1.5 text-gray-400 hover:text-indigo-600 rounded" title="Detay">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($message->status === 'draft')
                                    <form method="POST" action="{{ route('marketing.messages.send', $message) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="p-1.5 text-gray-400 hover:text-green-600 rounded" title="Gönder" onclick="return confirm('Mesajı göndermek istediğinize emin misiniz?')">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="px-4 py-8 text-center text-gray-400">
                        <i class="fas fa-envelope text-3xl mb-2"></i><p>Mesaj bulunamadı</p>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($messages->hasPages())
        <div class="px-4 py-3 border-t flex items-center justify-between">
            <span class="text-sm text-gray-500">Toplam {{ $messages->total() }} mesaj</span>
            <div>{{ $messages->withQueryString()->links() }}</div>
        </div>
    @endif
</div>
@endsection
