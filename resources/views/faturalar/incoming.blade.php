@extends('layouts.app')
@section('title', 'Gelen Faturalar')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gelen Faturalar</h1>
            <p class="text-sm text-gray-500 mt-1">Tedarikçilerden gelen e-faturalar ve alış faturaları</p>
        </div>
        <a href="{{ route('faturalar.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-1"></i> Panel
        </a>
    </div>

    {{-- İstatistikler --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">E-Fatura (Gelen)</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['einvoice_total'] }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-file-invoice text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Alış Faturaları</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['purchase_total'] }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-green-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">E-Fatura Tutar</p>
                    <p class="text-2xl font-bold text-blue-600">₺{{ number_format($stats['einvoice_amount'], 2, ',', '.') }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-lira-sign text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Alış Tutar</p>
                    <p class="text-2xl font-bold text-green-600">₺{{ number_format($stats['purchase_amount'], 2, ',', '.') }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                    <i class="fas fa-lira-sign text-green-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Tab Seçici --}}
    <div class="bg-white rounded-xl shadow-sm border p-4">
        <div class="flex items-center gap-4 flex-wrap">
            <a href="{{ route('faturalar.incoming', array_merge(request()->except('tab', 'page', 'epage', 'ppage'), ['tab' => 'all'])) }}"
               class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $tab === 'all' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                <i class="fas fa-layer-group mr-1"></i> Tümü
            </a>
            <a href="{{ route('faturalar.incoming', array_merge(request()->except('tab', 'page', 'epage', 'ppage'), ['tab' => 'einvoice'])) }}"
               class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $tab === 'einvoice' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                <i class="fas fa-file-invoice mr-1"></i> E-Faturalar
            </a>
            <a href="{{ route('faturalar.incoming', array_merge(request()->except('tab', 'page', 'epage', 'ppage'), ['tab' => 'purchase'])) }}"
               class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $tab === 'purchase' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                <i class="fas fa-shopping-cart mr-1"></i> Alış Faturaları
            </a>

            {{-- Filtreler --}}
            <form method="GET" class="flex items-end gap-3 ml-auto flex-wrap">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <div>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border" placeholder="Başlangıç">
                </div>
                <div>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border" placeholder="Bitiş">
                </div>
                <div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ara..." class="rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
                <button type="submit" class="bg-indigo-600 text-white px-3 py-2 rounded-lg text-sm hover:bg-indigo-700">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
    </div>

    {{-- E-Faturalar Tablosu --}}
    @if($tab !== 'purchase')
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="font-semibold text-gray-800">
                <i class="fas fa-file-invoice text-blue-500 mr-2"></i>Gelen E-Faturalar
                @if($eInvoices instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <span class="text-sm font-normal text-gray-400 ml-2">({{ $eInvoices->total() }} kayıt)</span>
                @endif
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Fatura No</th>
                        <th class="px-4 py-3 text-left">Gönderen</th>
                        <th class="px-4 py-3 text-left">Tür</th>
                        <th class="px-4 py-3 text-right">Tutar</th>
                        <th class="px-4 py-3 text-center">Durum</th>
                        <th class="px-4 py-3 text-left">Tarih</th>
                        <th class="px-4 py-3 text-center">İşlem</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($eInvoices as $inv)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-indigo-600">
                            <a href="{{ route('faturalar.show', $inv) }}">{{ $inv->invoice_no ?? '-' }}</a>
                        </td>
                        <td class="px-4 py-3">{{ $inv->receiver_name ?? ($inv->customer?->name ?? '-') }}</td>
                        <td class="px-4 py-3">{{ $inv->type_label }}</td>
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
                            <a href="{{ route('faturalar.show', $inv) }}" class="text-indigo-600 hover:text-indigo-800"><i class="fas fa-eye"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Gelen e-fatura bulunmuyor.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($eInvoices instanceof \Illuminate\Pagination\LengthAwarePaginator && $eInvoices->hasPages())
        <div class="px-6 py-4 border-t">{{ $eInvoices->links() }}</div>
        @endif
    </div>
    @endif

    {{-- Alış Faturaları Tablosu --}}
    @if($tab !== 'einvoice')
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="font-semibold text-gray-800">
                <i class="fas fa-shopping-cart text-green-500 mr-2"></i>Alış Faturaları (POS)
                @if($purchasePaginator instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <span class="text-sm font-normal text-gray-400 ml-2">({{ $purchasePaginator->total() }} kayıt)</span>
                @endif
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Fatura No</th>
                        <th class="px-4 py-3 text-left">Firma</th>
                        <th class="px-4 py-3 text-left">Tür</th>
                        <th class="px-4 py-3 text-center">Kalem</th>
                        <th class="px-4 py-3 text-right">Tutar</th>
                        <th class="px-4 py-3 text-left">Şube</th>
                        <th class="px-4 py-3 text-left">Tarih</th>
                        <th class="px-4 py-3 text-center">İşlem</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($purchases as $p)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-green-600">
                            <a href="{{ route('faturalar.purchase.show', $p) }}">{{ $p->invoice_no ?? '-' }}</a>
                        </td>
                        <td class="px-4 py-3">{{ $p->firm?->name ?? 'Bilinmeyen' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs {{ $p->invoice_type === 'return' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                {{ $p->invoice_type === 'return' ? 'İade' : 'Alış' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">{{ $p->items_count ?? $p->items->count() }}</td>
                        <td class="px-4 py-3 text-right font-medium">₺{{ number_format($p->total_amount, 2, ',', '.') }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $p->branch?->name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $p->invoice_date?->format('d.m.Y') ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('faturalar.purchase.show', $p) }}" class="text-green-600 hover:text-green-800"><i class="fas fa-eye"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">Alış faturası bulunmuyor.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($purchasePaginator instanceof \Illuminate\Pagination\LengthAwarePaginator && $purchasePaginator->hasPages())
        <div class="px-6 py-4 border-t">{{ $purchasePaginator->links() }}</div>
        @endif
    </div>
    @endif
</div>
@endsection
