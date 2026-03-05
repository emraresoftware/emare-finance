@extends('layouts.app')
@section('title', 'Otomasyon Kuyruğu')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Otomasyon Kuyruğu</h1>
        <p class="text-sm text-gray-500 mt-1">Otomatik SMS gönderim kuyruğunu görüntüleyin</p>
    </div>
    <a href="{{ route('sms.automations.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
        <i class="fas fa-arrow-left mr-2"></i> Otomasyonlar
    </a>
</div>

{{-- Filtreler --}}
<div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-medium text-gray-500 mb-1">Arama</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Telefon, isim veya içerik..." class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div class="w-40">
            <label class="block text-xs font-medium text-gray-500 mb-1">Durum</label>
            <select name="status" class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Tümü</option>
                @foreach($statusOptions as $val => $label)
                    <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-40">
            <label class="block text-xs font-medium text-gray-500 mb-1">Otomasyon Tipi</label>
            <select name="type" class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Tümü</option>
                <option value="birthday" {{ request('type') === 'birthday' ? 'selected' : '' }}>Doğum Günü</option>
                <option value="inactivity" {{ request('type') === 'inactivity' ? 'selected' : '' }}>Pasif Müşteri</option>
                <option value="payment_reminder" {{ request('type') === 'payment_reminder' ? 'selected' : '' }}>Ödeme Hatırlatma</option>
                <option value="welcome" {{ request('type') === 'welcome' ? 'selected' : '' }}>Hoş Geldin</option>
                <option value="after_sale" {{ request('type') === 'after_sale' ? 'selected' : '' }}>Satış Sonrası</option>
                <option value="cargo_shipped" {{ request('type') === 'cargo_shipped' ? 'selected' : '' }}>Kargo</option>
            </select>
        </div>
        <div>
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                <i class="fas fa-search mr-1.5"></i> Filtrele
            </button>
        </div>
    </form>
</div>

{{-- Tablo --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Müşteri</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telefon</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tip</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İçerik</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Zamanlanma</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($queue as $item)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 whitespace-nowrap">
                        <span class="text-sm font-medium text-gray-900">{{ $item->customer?->name ?? '-' }}</span>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                        {{ $item->phone }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        @php
                            $triggerLabels = [
                                'birthday' => ['Doğum Günü', 'bg-pink-100 text-pink-700'],
                                'inactivity' => ['Pasif Müşteri', 'bg-red-100 text-red-700'],
                                'payment_reminder' => ['Ödeme Hatırlatma', 'bg-yellow-100 text-yellow-700'],
                                'welcome' => ['Hoş Geldin', 'bg-green-100 text-green-700'],
                                'after_sale' => ['Satış Sonrası', 'bg-blue-100 text-blue-700'],
                                'cargo_shipped' => ['Kargo', 'bg-orange-100 text-orange-700'],
                                'cargo_delivered' => ['Teslim', 'bg-emerald-100 text-emerald-700'],
                            ];
                            $trigger = $triggerLabels[$item->trigger_event] ?? [$item->trigger_event, 'bg-gray-100 text-gray-700'];
                        @endphp
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $trigger[1] }}">
                            {{ $trigger[0] }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <p class="text-sm text-gray-600 max-w-xs truncate" title="{{ $item->content }}">{{ $item->content }}</p>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                        {{ $item->scheduled_at?->format('d.m.Y H:i') ?? '-' }}
                        @if($item->sent_at)
                            <br><span class="text-xs text-green-600">Gönderildi: {{ $item->sent_at->format('H:i') }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        @php
                            $statusColors = [
                                'pending'   => 'bg-yellow-100 text-yellow-700',
                                'sent'      => 'bg-green-100 text-green-700',
                                'failed'    => 'bg-red-100 text-red-700',
                                'cancelled' => 'bg-gray-100 text-gray-700',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$item->status] ?? 'bg-gray-100 text-gray-700' }}">
                            {{ $statusOptions[$item->status] ?? $item->status }}
                        </span>
                        @if($item->error_message)
                            <p class="text-xs text-red-500 mt-1 max-w-[200px] truncate" title="{{ $item->error_message }}">{{ $item->error_message }}</p>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-3">
                                <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                            </div>
                            <p class="text-sm font-medium text-gray-500">Kuyrukta mesaj bulunmuyor</p>
                            <p class="text-xs text-gray-400 mt-1">Otomasyonlar çalıştığında mesajlar burada görünecek</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($queue->hasPages())
    <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
        {{ $queue->links() }}
    </div>
    @endif
</div>
@endsection
