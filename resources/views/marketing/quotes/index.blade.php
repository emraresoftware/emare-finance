@extends('layouts.app')
@section('title', 'Teklifler')

@section('content')
{{-- Başlık --}}
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('marketing.index') }}" class="text-gray-400 hover:text-gray-600">
            <i class="fas fa-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Teklifler</h1>
            <p class="text-sm text-gray-500 mt-1">Müşteri tekliflerini yönetin</p>
        </div>
    </div>
    <a href="{{ route('marketing.quotes.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 font-medium">
        <i class="fas fa-plus mr-1"></i> Yeni Teklif
    </a>
</div>

{{-- Durum İstatistikleri --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Toplam</p>
        <p class="text-xl font-bold">{{ number_format($statusCounts['total'] ?? 0) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Taslak</p>
        <p class="text-xl font-bold text-gray-600">{{ $statusCounts['draft'] ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Gönderildi</p>
        <p class="text-xl font-bold text-blue-600">{{ $statusCounts['sent'] ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Bekleyen</p>
        <p class="text-xl font-bold text-yellow-600">{{ $statusCounts['pending'] ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Kabul Edildi</p>
        <p class="text-xl font-bold text-green-600">{{ $statusCounts['accepted'] ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Reddedildi</p>
        <p class="text-xl font-bold text-red-600">{{ $statusCounts['rejected'] ?? 0 }}</p>
    </div>
</div>

{{-- Filtreler --}}
<div class="bg-white rounded-xl shadow-sm p-4 mb-6 border">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs text-gray-500 mb-1">Ara</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Teklif no, müşteri adı..."
                   class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Durum</label>
            <select name="status" class="border rounded-lg px-3 py-2 text-sm">
                <option value="">Tümü</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Taslak</option>
                <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Gönderildi</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Bekleyen</option>
                <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Kabul Edildi</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Reddedildi</option>
                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Süresi Dolmuş</option>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Başlangıç</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="border rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Bitiş</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="border rounded-lg px-3 py-2 text-sm">
        </div>
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">
            <i class="fas fa-search mr-1"></i> Filtrele
        </button>
        <a href="{{ route('marketing.quotes.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm hover:bg-gray-200">Temizle</a>
    </form>
</div>

{{-- Tablo --}}
<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teklif No</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Müşteri</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Başlık</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Durum</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Tutar</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tarih</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Geçerlilik</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">İşlemler</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($quotes as $quote)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <a href="{{ route('marketing.quotes.show', $quote) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">{{ $quote->quote_number }}</a>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $quote->customer_name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ Str::limit($quote->title, 30) }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $quote->status_color }}-100 text-{{ $quote->status_color }}-800">
                                {{ $quote->status_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-semibold">₺{{ number_format($quote->grand_total, 2, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center text-gray-500">{{ $quote->issue_date?->format('d.m.Y') }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($quote->valid_until)
                                <span class="{{ $quote->valid_until->isPast() ? 'text-red-500' : 'text-gray-500' }}">
                                    {{ $quote->valid_until->format('d.m.Y') }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('marketing.quotes.show', $quote) }}" class="p-1.5 text-gray-400 hover:text-indigo-600 rounded" title="Detay">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($quote->status === 'draft')
                                    <form method="POST" action="{{ route('marketing.quotes.send', $quote) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="p-1.5 text-gray-400 hover:text-blue-600 rounded" title="Gönder" onclick="return confirm('Teklifi göndermek istediğinize emin misiniz?')">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('marketing.quotes.duplicate', $quote) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-green-600 rounded" title="Kopyala">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">
                        <i class="fas fa-file-invoice text-3xl mb-2"></i><p>Teklif bulunamadı</p>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($quotes->hasPages())
        <div class="px-4 py-3 border-t flex items-center justify-between">
            <span class="text-sm text-gray-500">Toplam {{ $quotes->total() }} kayıt, Sayfa {{ $quotes->currentPage() }}/{{ $quotes->lastPage() }}</span>
            <div>{{ $quotes->withQueryString()->links() }}</div>
        </div>
    @endif
</div>
@endsection
