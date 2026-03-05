@extends('layouts.app')
@section('title', 'Giden Faturalar')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Giden Faturalar</h1>
            <p class="text-sm text-gray-500 mt-1">Müşterilere kesilen tüm faturalar</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('faturalar.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left mr-1"></i> Panel
            </a>
            <a href="{{ route('faturalar.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">
                <i class="fas fa-plus mr-1"></i> Yeni Fatura Kes
            </a>
        </div>
    </div>

    {{-- İstatistikler --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Toplam Fatura</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                    <i class="fas fa-file-invoice text-indigo-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Taslak</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['draft'] }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-yellow-100 flex items-center justify-center">
                    <i class="fas fa-pen text-yellow-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Gönderildi</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['sent'] }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-paper-plane text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Toplam Tutar</p>
                    <p class="text-2xl font-bold text-green-600">₺{{ number_format($stats['total_amount'], 2, ',', '.') }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                    <i class="fas fa-lira-sign text-green-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtreler --}}
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <form method="GET" class="flex items-end gap-4 flex-wrap">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Başlangıç</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bitiş</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Durum</label>
                <select name="status" class="rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                    <option value="">Tümü</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Taslak</option>
                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Gönderildi</option>
                    <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>Kabul Edildi</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Reddedildi</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>İptal</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tür</label>
                <select name="type" class="rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                    <option value="">Tümü</option>
                    <option value="invoice" {{ request('type') === 'invoice' ? 'selected' : '' }}>Satış Faturası</option>
                    <option value="return" {{ request('type') === 'return' ? 'selected' : '' }}>İade Faturası</option>
                    <option value="withholding" {{ request('type') === 'withholding' ? 'selected' : '' }}>Tevkifatlı</option>
                    <option value="exception" {{ request('type') === 'exception' ? 'selected' : '' }}>İstisna</option>
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Arama</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Fatura no, alıcı adı, vergi no..." class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
            </div>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">
                <i class="fas fa-search mr-1"></i> Filtrele
            </button>
            @if(request()->hasAny(['start_date', 'end_date', 'status', 'type', 'search']))
            <a href="{{ route('faturalar.outgoing') }}" class="text-sm text-gray-500 hover:text-red-500">
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
                        <th class="px-4 py-3 text-left">Fatura No</th>
                        <th class="px-4 py-3 text-left">Alıcı</th>
                        <th class="px-4 py-3 text-left">Tür</th>
                        <th class="px-4 py-3 text-left">Senaryo</th>
                        <th class="px-4 py-3 text-right">Tutar</th>
                        <th class="px-4 py-3 text-center">Durum</th>
                        <th class="px-4 py-3 text-left">Tarih</th>
                        <th class="px-4 py-3 text-center">İşlem</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($invoices as $inv)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-indigo-600">
                            <a href="{{ route('faturalar.show', $inv) }}">{{ $inv->invoice_no ?? 'Taslak' }}</a>
                        </td>
                        <td class="px-4 py-3">
                            {{ $inv->receiver_name ?? ($inv->customer?->name ?? '-') }}
                            @if($inv->receiver_tax_number)
                            <span class="block text-xs text-gray-400">VKN: {{ $inv->receiver_tax_number }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $inv->type_label }}</td>
                        <td class="px-4 py-3">{{ $inv->scenario_label }}</td>
                        <td class="px-4 py-3 text-right font-medium">₺{{ number_format($inv->grand_total, 2, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                {{ $inv->status === 'draft' ? 'bg-gray-100 text-gray-700' : '' }}
                                {{ $inv->status === 'sent' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $inv->status === 'accepted' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $inv->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}
                                {{ $inv->status === 'cancelled' ? 'bg-orange-100 text-orange-700' : '' }}
                            ">{{ $inv->status_label }}</span>
                        </td>
                        <td class="px-4 py-3">{{ $inv->invoice_date?->format('d.m.Y') ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('faturalar.show', $inv) }}" class="text-indigo-600 hover:text-indigo-800" title="Detay">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-gray-400">
                            <i class="fas fa-file-invoice text-5xl mb-4 block"></i>
                            <p class="text-lg">Henüz giden fatura bulunmuyor.</p>
                            <p class="text-sm mt-2">
                                <a href="{{ route('faturalar.create') }}" class="text-indigo-600 hover:underline">
                                    <i class="fas fa-plus mr-1"></i>Yeni Fatura Kes
                                </a>
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($invoices->hasPages())
        <div class="px-6 py-4 border-t">{{ $invoices->links() }}</div>
        @endif
    </div>
</div>
@endsection
