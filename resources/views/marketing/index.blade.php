@extends('layouts.app')
@section('title', 'Pazarlama')

@section('content')
{{-- Başlık --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Pazarlama Paneli</h1>
        <p class="text-sm text-gray-500 mt-1">Kampanyalar, teklifler, segmentler ve mesajlar</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('marketing.quotes.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 font-medium">
            <i class="fas fa-plus mr-1"></i> Yeni Teklif
        </a>
        <a href="{{ route('marketing.campaigns.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 font-medium">
            <i class="fas fa-bullhorn mr-1"></i> Yeni Kampanya
        </a>
    </div>
</div>

{{-- İstatistik Kartları --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Aktif Kampanyalar</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['active_campaigns']) }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-bullhorn text-green-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-3 text-sm">
            <span class="text-green-600 font-medium">₺{{ number_format($stats['campaign_savings'], 2, ',', '.') }}</span>
            <span class="text-gray-500 ml-1">toplam tasarruf</span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Toplam Teklifler</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_quotes']) }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-file-invoice text-blue-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-3 flex items-center gap-3 text-sm">
            <span class="text-yellow-600">{{ $stats['pending_quotes'] }} bekleyen</span>
            <span class="text-green-600">{{ $stats['accepted_quotes'] }} kabul</span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Teklif Geliri</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">₺{{ number_format($stats['quote_revenue'], 2, ',', '.') }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-turkish-lira-sign text-purple-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-3 text-sm text-gray-500">Kabul edilen tekliflerden</div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Gönderilen Mesajlar</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['messages_sent']) }}</p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-envelope text-orange-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-3 text-sm text-gray-500">{{ $stats['total_segments'] }} müşteri segmenti</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Son Teklifler --}}
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Son Teklifler</h3>
            <a href="{{ route('marketing.quotes.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">Tümünü Gör <i class="fas fa-arrow-right ml-1"></i></a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teklif No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Müşteri</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Durum</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Tutar</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Tarih</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($recentQuotes as $quote)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <a href="{{ route('marketing.quotes.show', $quote) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">{{ $quote->quote_number }}</a>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $quote->customer_name }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $quote->status_color }}-100 text-{{ $quote->status_color }}-800">
                                    {{ $quote->status_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right font-semibold">₺{{ number_format($quote->grand_total, 2, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-gray-500">{{ $quote->issue_date?->format('d.m.Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">
                            <i class="fas fa-file-invoice text-3xl mb-2"></i><p>Henüz teklif yok</p>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Aktif Kampanyalar --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Aktif Kampanyalar</h3>
            <a href="{{ route('marketing.campaigns.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">Tümü <i class="fas fa-arrow-right ml-1"></i></a>
        </div>
        <div class="space-y-3">
            @forelse($activeCampaigns as $campaign)
                <a href="{{ route('marketing.campaigns.show', $campaign) }}" class="block p-4 rounded-lg border hover:bg-gray-50 transition">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold text-gray-800">{{ $campaign->name }}</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $campaign->status_color }}-100 text-{{ $campaign->status_color }}-800">
                            {{ $campaign->status_label }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-xs text-gray-500">
                        <span><i class="fas fa-tag mr-1"></i>{{ $campaign->type_label }}</span>
                        <span>
                            @if($campaign->discount_type === 'percentage')
                                %{{ $campaign->discount_value }} indirim
                            @else
                                ₺{{ number_format($campaign->discount_value, 2, ',', '.') }} indirim
                            @endif
                        </span>
                    </div>
                    @if($campaign->ends_at)
                        <div class="mt-2 text-xs text-gray-400">
                            <i class="fas fa-clock mr-1"></i>{{ $campaign->ends_at->format('d.m.Y') }}'e kadar
                        </div>
                    @endif
                </a>
            @empty
                <div class="text-center py-6 text-gray-400">
                    <i class="fas fa-bullhorn text-3xl mb-2"></i>
                    <p class="text-sm">Aktif kampanya yok</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Son Mesajlar --}}
<div class="mt-6 bg-white rounded-xl shadow-sm p-6 border border-gray-100">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-800">Son Mesajlar</h3>
        <a href="{{ route('marketing.messages.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">Tümünü Gör <i class="fas fa-arrow-right ml-1"></i></a>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($recentMessages as $message)
            <a href="{{ route('marketing.messages.show', $message) }}" class="block p-4 rounded-lg border hover:bg-gray-50 transition">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-semibold text-gray-800">{{ $message->title }}</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $message->status_color ?? 'gray' }}-100 text-{{ $message->status_color ?? 'gray' }}-800">
                        {{ $message->status_label }}
                    </span>
                </div>
                <div class="flex items-center gap-3 text-xs text-gray-500 mt-1">
                    <span><i class="fas fa-{{ $message->channel === 'sms' ? 'sms' : ($message->channel === 'email' ? 'envelope' : 'bell') }} mr-1"></i>{{ $message->channel_label }}</span>
                    <span><i class="fas fa-users mr-1"></i>{{ $message->total_recipients }} alıcı</span>
                </div>
                @if($message->sent_at)
                    <div class="mt-2 text-xs text-gray-400">
                        <i class="fas fa-paper-plane mr-1"></i>{{ $message->sent_at->format('d.m.Y H:i') }}
                    </div>
                @endif
            </a>
        @empty
            <div class="col-span-3 text-center py-6 text-gray-400">
                <i class="fas fa-envelope text-3xl mb-2"></i>
                <p class="text-sm">Henüz mesaj gönderilmemiş</p>
            </div>
        @endforelse
    </div>
</div>

{{-- Hızlı Erişim --}}
<div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
    <a href="{{ route('marketing.quotes.index') }}" class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:border-indigo-300 hover:shadow-md transition group">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center group-hover:bg-indigo-200 transition">
                <i class="fas fa-file-invoice text-indigo-600 text-xl"></i>
            </div>
            <div>
                <p class="font-semibold text-gray-800">Teklifler</p>
                <p class="text-sm text-gray-500">Tüm teklifleri yönet</p>
            </div>
        </div>
    </a>
    <a href="{{ route('marketing.campaigns.index') }}" class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:border-green-300 hover:shadow-md transition group">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center group-hover:bg-green-200 transition">
                <i class="fas fa-bullhorn text-green-600 text-xl"></i>
            </div>
            <div>
                <p class="font-semibold text-gray-800">Kampanyalar</p>
                <p class="text-sm text-gray-500">Kampanyaları yönet</p>
            </div>
        </div>
    </a>
    <a href="{{ route('marketing.segments.index') }}" class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:border-purple-300 hover:shadow-md transition group">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center group-hover:bg-purple-200 transition">
                <i class="fas fa-users text-purple-600 text-xl"></i>
            </div>
            <div>
                <p class="font-semibold text-gray-800">Segmentler</p>
                <p class="text-sm text-gray-500">Müşteri segmentleri</p>
            </div>
        </div>
    </a>
    <a href="{{ route('marketing.loyalty.index') }}" class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:border-orange-300 hover:shadow-md transition group">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center group-hover:bg-orange-200 transition">
                <i class="fas fa-star text-orange-600 text-xl"></i>
            </div>
            <div>
                <p class="font-semibold text-gray-800">Sadakat Programı</p>
                <p class="text-sm text-gray-500">Puan sistemi yönetimi</p>
            </div>
        </div>
    </a>
</div>
@endsection
