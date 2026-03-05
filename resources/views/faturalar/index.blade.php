@extends('layouts.app')
@section('title', 'Faturalar')

@section('content')
<div class="space-y-6">
    {{-- Başlık --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Faturalar & İrsaliyeler</h1>
            <p class="text-sm text-gray-500 mt-1">Tüm fatura ve irsaliye işlemlerini tek panelden yönetin</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('faturalar.create') }}" class="bg-indigo-600 text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-indigo-700 shadow-sm">
                <i class="fas fa-plus mr-1"></i> Yeni Fatura Kes
            </a>
            <a href="{{ route('faturalar.create', ['document_type' => 'irsaliye']) }}" class="bg-emerald-600 text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-emerald-700 shadow-sm">
                <i class="fas fa-truck mr-1"></i> Yeni İrsaliye
            </a>
        </div>
    </div>

    {{-- İstatistik Kartları --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Giden Faturalar --}}
        <div class="bg-white rounded-xl shadow-sm border p-5 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Giden Faturalar</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['outgoing_total'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $stats['outgoing_draft'] }} taslak, {{ $stats['outgoing_sent'] }} gönderildi</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-file-export text-blue-600 text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Gelen Faturalar --}}
        <div class="bg-white rounded-xl shadow-sm border p-5 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Gelen Faturalar</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['incoming_total'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">E-Fatura + Alış Faturaları</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center">
                    <i class="fas fa-file-import text-green-600 text-lg"></i>
                </div>
            </div>
        </div>

        {{-- İrsaliyeler --}}
        <div class="bg-white rounded-xl shadow-sm border p-5 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">İrsaliyeler</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['waybill_total'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">Sevk İrsaliyesi</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center">
                    <i class="fas fa-truck text-purple-600 text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Aylık Ciro --}}
        <div class="bg-white rounded-xl shadow-sm border p-5 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Bu Ay (Giden)</p>
                    <p class="text-2xl font-bold text-indigo-600">₺{{ number_format($stats['this_month_out'], 2, ',', '.') }}</p>
                    <p class="text-xs text-gray-400 mt-1">Gelen: ₺{{ number_format($stats['this_month_in'], 2, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center">
                    <i class="fas fa-chart-line text-indigo-600 text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Toplam Tutar Barı --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-sm p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Toplam Giden Tutar</p>
                    <p class="text-3xl font-bold mt-1">₺{{ number_format($stats['outgoing_amount'], 2, ',', '.') }}</p>
                </div>
                <i class="fas fa-arrow-up text-4xl text-blue-300 opacity-40"></i>
            </div>
        </div>
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-sm p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Toplam Gelen Tutar</p>
                    <p class="text-3xl font-bold mt-1">₺{{ number_format($stats['incoming_amount'], 2, ',', '.') }}</p>
                </div>
                <i class="fas fa-arrow-down text-4xl text-green-300 opacity-40"></i>
            </div>
        </div>
    </div>

    {{-- Hızlı Erişim Kartları --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('faturalar.outgoing') }}" class="bg-white rounded-xl shadow-sm border p-6 hover:shadow-md transition group text-center">
            <div class="w-14 h-14 rounded-xl bg-blue-100 flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition">
                <i class="fas fa-file-export text-blue-600 text-xl"></i>
            </div>
            <h3 class="font-semibold text-gray-800">Giden Faturalar</h3>
            <p class="text-xs text-gray-500 mt-1">Kesilen faturaları görüntüle</p>
        </a>
        <a href="{{ route('faturalar.incoming') }}" class="bg-white rounded-xl shadow-sm border p-6 hover:shadow-md transition group text-center">
            <div class="w-14 h-14 rounded-xl bg-green-100 flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition">
                <i class="fas fa-file-import text-green-600 text-xl"></i>
            </div>
            <h3 class="font-semibold text-gray-800">Gelen Faturalar</h3>
            <p class="text-xs text-gray-500 mt-1">Alış ve gelen faturaları görüntüle</p>
        </a>
        <a href="{{ route('faturalar.waybills') }}" class="bg-white rounded-xl shadow-sm border p-6 hover:shadow-md transition group text-center">
            <div class="w-14 h-14 rounded-xl bg-purple-100 flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition">
                <i class="fas fa-truck text-purple-600 text-xl"></i>
            </div>
            <h3 class="font-semibold text-gray-800">İrsaliyeler</h3>
            <p class="text-xs text-gray-500 mt-1">Sevk irsaliyelerini yönet</p>
        </a>
        <a href="{{ route('recurring_invoices.index') }}" class="bg-white rounded-xl shadow-sm border p-6 hover:shadow-md transition group text-center">
            <div class="w-14 h-14 rounded-xl bg-orange-100 flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition">
                <i class="fas fa-redo text-orange-600 text-xl"></i>
            </div>
            <h3 class="font-semibold text-gray-800">Tekrarlayan Faturalar</h3>
            <p class="text-xs text-gray-500 mt-1">Otomatik fatura şablonları</p>
        </a>
    </div>

    {{-- Son Belgeler --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Son E-Faturalar --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border overflow-hidden">
            <div class="px-6 py-4 border-b flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">
                    <i class="fas fa-file-invoice text-indigo-500 mr-2"></i>Son Faturalar & İrsaliyeler
                </h3>
                <a href="{{ route('faturalar.outgoing') }}" class="text-sm text-indigo-600 hover:text-indigo-800">Tümünü Gör →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-3 text-left">No</th>
                            <th class="px-4 py-3 text-left">Belge</th>
                            <th class="px-4 py-3 text-left">Alıcı / Gönderen</th>
                            <th class="px-4 py-3 text-right">Tutar</th>
                            <th class="px-4 py-3 text-center">Durum</th>
                            <th class="px-4 py-3 text-left">Tarih</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentInvoices as $inv)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <a href="{{ route('faturalar.show', $inv) }}" class="font-medium text-indigo-600 hover:underline">{{ $inv->invoice_no ?? 'Taslak' }}</a>
                            </td>
                            <td class="px-4 py-3">
                                @if($inv->document_type === 'irsaliye')
                                <span class="px-2 py-0.5 rounded-full text-xs bg-purple-100 text-purple-700">İrsaliye</span>
                                @else
                                <span class="px-2 py-0.5 rounded-full text-xs {{ $inv->direction === 'outgoing' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                                    {{ $inv->direction === 'outgoing' ? 'Giden' : 'Gelen' }} Fatura
                                </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-700">{{ Str::limit($inv->receiver_name ?? $inv->customer?->name ?? '-', 30) }}</td>
                            <td class="px-4 py-3 text-right font-medium">₺{{ number_format($inv->grand_total, 2, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $inv->status === 'draft' ? 'bg-gray-100 text-gray-600' : '' }}
                                    {{ $inv->status === 'sent' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $inv->status === 'accepted' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $inv->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}
                                    {{ $inv->status === 'cancelled' ? 'bg-orange-100 text-orange-700' : '' }}
                                ">{{ $inv->status_label }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $inv->invoice_date?->format('d.m.Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Henüz fatura bulunmuyor.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Son Alış Faturaları --}}
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <div class="px-6 py-4 border-b flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">
                    <i class="fas fa-shopping-cart text-green-500 mr-2"></i>Son Alış Faturaları
                </h3>
                <a href="{{ route('faturalar.incoming', ['tab' => 'purchase']) }}" class="text-sm text-indigo-600 hover:text-indigo-800">Tümü →</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($recentPurchases as $p)
                <a href="{{ route('faturalar.purchase.show', $p) }}" class="block px-6 py-3 hover:bg-gray-50 transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-gray-800 text-sm">{{ $p->firm?->name ?? 'Bilinmeyen' }}</p>
                            <p class="text-xs text-gray-400">{{ $p->invoice_no }} • {{ $p->invoice_date?->format('d.m.Y') }}</p>
                        </div>
                        <span class="font-medium text-green-600 text-sm">₺{{ number_format($p->total_amount, 2, ',', '.') }}</span>
                    </div>
                </a>
                @empty
                <div class="px-6 py-8 text-center text-gray-400 text-sm">Alış faturası bulunmuyor.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
