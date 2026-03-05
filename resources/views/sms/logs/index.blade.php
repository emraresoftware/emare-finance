@extends('layouts.app')
@section('title', 'SMS Logları')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">SMS Logları</h1>
        <p class="text-sm text-gray-500 mt-1">Tüm SMS gönderim kayıtlarını görüntüleyin</p>
    </div>
    <a href="{{ route('sms.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
        <i class="fas fa-arrow-left mr-2"></i> SMS Paneli
    </a>
</div>

{{-- Filtreler --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
    <form action="{{ route('sms.logs.index') }}" method="GET" class="p-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            {{-- Arama --}}
            <div>
                <label for="search" class="block text-xs font-medium text-gray-500 mb-1">Arama</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                       placeholder="Telefon, müşteri adı...">
            </div>

            {{-- Durum --}}
            <div>
                <label for="status" class="block text-xs font-medium text-gray-500 mb-1">Durum</label>
                <select name="status" id="status"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Tüm Durumlar</option>
                    @foreach($statusOptions as $statusKey => $statusLabel)
                        <option value="{{ $statusKey }}" {{ request('status') == $statusKey ? 'selected' : '' }}>{{ $statusLabel }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Başlangıç Tarihi --}}
            <div>
                <label for="date_from" class="block text-xs font-medium text-gray-500 mb-1">Başlangıç Tarihi</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            {{-- Bitiş Tarihi --}}
            <div>
                <label for="date_to" class="block text-xs font-medium text-gray-500 mb-1">Bitiş Tarihi</label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            {{-- Butonlar --}}
            <div class="flex items-end gap-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                    <i class="fas fa-search mr-2"></i> Filtrele
                </button>
                <a href="{{ route('sms.logs.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                    <i class="fas fa-times mr-1"></i> Temizle
                </a>
            </div>
        </div>
    </form>
</div>

{{-- Tablo --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telefon</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Müşteri</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İçerik</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tetikleyici</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Maliyet</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mesaj ID</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($logs as $log)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="text-sm text-gray-900">{{ $log->created_at->format('d.m.Y') }}</p>
                            <p class="text-xs text-gray-400">{{ $log->created_at->format('H:i:s') }}</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-mono text-gray-900">{{ $log->phone }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($log->customer)
                                <p class="text-sm text-gray-900">{{ $log->customer->full_name }}</p>
                            @else
                                <span class="text-xs text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-600 truncate max-w-xs" title="{{ $log->content }}">
                                {{ Str::limit($log->content, 50) }}
                            </p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($log->scenario)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    <i class="fas fa-robot text-[10px] mr-1"></i> {{ $log->scenario->name }}
                                </span>
                            @elseif($log->template)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    <i class="fas fa-file-alt text-[10px] mr-1"></i> {{ $log->template->name }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                    Manuel
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            @switch($log->status)
                                @case('pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock text-[8px] mr-1"></i> Bekliyor
                                    </span>
                                @break
                                @case('sent')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-paper-plane text-[8px] mr-1"></i> Gönderildi
                                    </span>
                                @break
                                @case('delivered')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-double text-[8px] mr-1"></i> İletildi
                                    </span>
                                @break
                                @case('failed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-times text-[8px] mr-1"></i> Başarısız
                                    </span>
                                @break
                                @case('rejected')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <i class="fas fa-ban text-[8px] mr-1"></i> Reddedildi
                                    </span>
                                @break
                                @default
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        {{ $log->status }}
                                    </span>
                            @endswitch
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <span class="text-sm text-gray-900">₺{{ number_format($log->cost ?? 0, 4, ',', '.') }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($log->provider_message_id)
                                <code class="text-xs bg-gray-100 px-2 py-1 rounded text-gray-500">{{ Str::limit($log->provider_message_id, 20) }}</code>
                            @else
                                <span class="text-xs text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="text-gray-400">
                                <i class="fas fa-inbox text-4xl mb-3"></i>
                                <p class="text-sm">Henüz SMS gönderim kaydı bulunamadı</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $logs->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
