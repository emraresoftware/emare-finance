@extends('layouts.app')
@section('title', 'İrsaliyeler')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">İrsaliyeler</h1>
            <p class="text-sm text-gray-500 mt-1">Giden ve gelen sevk irsaliyeleri</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('faturalar.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left mr-1"></i> Panel
            </a>
            <a href="{{ route('faturalar.create', ['document_type' => 'irsaliye']) }}" class="bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-emerald-700">
                <i class="fas fa-truck mr-1"></i> Yeni İrsaliye
            </a>
        </div>
    </div>

    {{-- İstatistikler --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Toplam İrsaliye</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                    <i class="fas fa-truck text-purple-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Giden İrsaliye</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['outgoing_count'] }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-arrow-up text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Gelen İrsaliye</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['incoming_count'] }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                    <i class="fas fa-arrow-down text-green-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Toplam Tutar</p>
                    <p class="text-2xl font-bold text-indigo-600">₺{{ number_format($stats['total_amount'], 2, ',', '.') }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                    <i class="fas fa-lira-sign text-indigo-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtreler --}}
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <form method="GET" class="flex items-end gap-4 flex-wrap">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Yön</label>
                <select name="direction" class="rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                    <option value="">Tümü</option>
                    <option value="outgoing" {{ request('direction') === 'outgoing' ? 'selected' : '' }}>Giden</option>
                    <option value="incoming" {{ request('direction') === 'incoming' ? 'selected' : '' }}>Gelen</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Başlangıç</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bitiş</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Arama</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="İrsaliye no, takip no, alıcı adı..." class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
            </div>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">
                <i class="fas fa-search mr-1"></i> Filtrele
            </button>
            @if(request()->hasAny(['direction', 'start_date','end_date','search']))
            <a href="{{ route('faturalar.waybills') }}" class="text-sm text-gray-500 hover:text-red-500">
                <i class="fas fa-times mr-1"></i> Temizle
            </a>
            @endif
        </form>
    </div>

    {{-- Tablo --}}
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">İrsaliye No</th>
                        <th class="px-4 py-3 text-left">Yön</th>
                        <th class="px-4 py-3 text-left">Alıcı / Gönderen</th>
                        <th class="px-4 py-3 text-left">Sevk Tarihi</th>
                        <th class="px-4 py-3 text-left">Araç / Şoför</th>
                        <th class="px-4 py-3 text-left">Takip No</th>
                        <th class="px-4 py-3 text-right">Tutar</th>
                        <th class="px-4 py-3 text-center">Durum</th>
                        <th class="px-4 py-3 text-center">İşlem</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($waybills as $wb)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-purple-600">
                            <a href="{{ route('faturalar.show', $wb) }}">{{ $wb->waybill_no ?: ($wb->invoice_no ?? 'Taslak') }}</a>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs {{ $wb->direction === 'outgoing' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                                {{ $wb->direction === 'outgoing' ? 'Giden' : 'Gelen' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ $wb->receiver_name ?? ($wb->customer?->name ?? '-') }}</td>
                        <td class="px-4 py-3">{{ $wb->shipment_date?->format('d.m.Y') ?? ($wb->invoice_date?->format('d.m.Y') ?? '-') }}</td>
                        <td class="px-4 py-3">
                            @if($wb->vehicle_plate)
                            <span class="text-xs bg-gray-100 px-2 py-0.5 rounded font-mono">{{ $wb->vehicle_plate }}</span>
                            @endif
                            @if($wb->driver_name)
                            <span class="block text-xs text-gray-400 mt-0.5">{{ $wb->driver_name }}</span>
                            @endif
                            @if(!$wb->vehicle_plate && !$wb->driver_name) - @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($wb->tracking_no)
                            <span class="text-xs bg-indigo-50 px-2 py-0.5 rounded text-indigo-700">{{ $wb->tracking_no }}</span>
                            @else - @endif
                        </td>
                        <td class="px-4 py-3 text-right font-medium">₺{{ number_format($wb->grand_total, 2, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                {{ $wb->status === 'draft' ? 'bg-gray-100 text-gray-700' : '' }}
                                {{ $wb->status === 'sent' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $wb->status === 'accepted' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $wb->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}
                                {{ $wb->status === 'cancelled' ? 'bg-orange-100 text-orange-700' : '' }}
                            ">{{ $wb->status_label }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('faturalar.show', $wb) }}" class="text-purple-600 hover:text-purple-800"><i class="fas fa-eye"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-12 text-center text-gray-400">
                            <i class="fas fa-truck text-5xl mb-4 block"></i>
                            <p class="text-lg">Henüz irsaliye bulunmuyor.</p>
                            <p class="text-sm mt-2">
                                <a href="{{ route('faturalar.create', ['document_type' => 'irsaliye']) }}" class="text-emerald-600 hover:underline">
                                    <i class="fas fa-plus mr-1"></i>Yeni İrsaliye Oluştur
                                </a>
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($waybills->hasPages())
        <div class="px-6 py-4 border-t">{{ $waybills->links() }}</div>
        @endif
    </div>
</div>
@endsection
