@extends('layouts.app')
@section('title', 'e-Arşiv Faturalar')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">e-Arşiv Faturalar</h1>
            <p class="text-sm text-gray-500 mt-1">GİB e-Arşiv sistemi üzerinden kesilen tüm faturalar</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('faturalar.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left mr-1"></i> Panel
            </a>
            <a href="{{ route('faturalar.create', ['scenario' => 'e_arsiv']) }}" class="bg-amber-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-amber-700">
                <i class="fas fa-plus mr-1"></i> Yeni e-Arşiv Fatura
            </a>
        </div>
    </div>

    {{-- İstatistikler --}}
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Toplam</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Taslak</p>
            <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $stats['draft'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Gönderildi</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['sent'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Toplam Tutar</p>
            <p class="text-xl font-bold text-green-600 mt-1">₺{{ number_format($stats['total_amount'], 2, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Bireysel</p>
            <p class="text-2xl font-bold text-purple-600 mt-1">{{ $stats['individual_count'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Kurumsal</p>
            <p class="text-2xl font-bold text-indigo-600 mt-1">{{ $stats['corporate_count'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">İnternet Satışı</p>
            <p class="text-2xl font-bold text-orange-600 mt-1">{{ $stats['internet_count'] }}</p>
        </div>
    </div>

    {{-- Bilgi Kartı --}}
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-info-circle text-amber-600"></i>
            </div>
            <div>
                <h4 class="text-sm font-semibold text-amber-800">e-Arşiv Fatura Nedir?</h4>
                <p class="text-xs text-amber-700 mt-1">
                    e-Arşiv Fatura, e-Fatura sistemine kayıtlı olmayan alıcılara (bireysel müşteriler, kayıt dışı firmalar) kesilen elektronik faturadır.
                    TC Kimlik No veya Vergi Kimlik No ile düzenlenebilir. İnternet satışlarında zorunludur.
                </p>
            </div>
        </div>
    </div>

    {{-- Filtreler --}}
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <form method="GET" class="flex items-end gap-4 flex-wrap">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Başlangıç</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Bitiş</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Yön</label>
                <select name="direction" class="rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                    <option value="">Tümü</option>
                    <option value="outgoing" {{ request('direction') === 'outgoing' ? 'selected' : '' }}>Giden</option>
                    <option value="incoming" {{ request('direction') === 'incoming' ? 'selected' : '' }}>Gelen</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Durum</label>
                <select name="status" class="rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                    <option value="">Tümü</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Taslak</option>
                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Gönderildi</option>
                    <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>Kabul Edildi</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>İptal</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Alıcı Türü</label>
                <select name="recipient_type" class="rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                    <option value="">Tümü</option>
                    <option value="individual" {{ request('recipient_type') === 'individual' ? 'selected' : '' }}>Bireysel</option>
                    <option value="corporate" {{ request('recipient_type') === 'corporate' ? 'selected' : '' }}>Kurumsal</option>
                </select>
            </div>
            <div>
                <label class="flex items-center gap-2 text-xs font-medium text-gray-700 mb-1">
                    <input type="checkbox" name="internet_sale" value="1" {{ request('internet_sale') ? 'checked' : '' }} class="rounded border-gray-300 text-amber-600">
                    İnternet Satışı
                </label>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-gray-700 mb-1">Arama</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Fatura no, alıcı adı, VKN, TCKN..." class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
            </div>
            <button type="submit" class="bg-amber-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-amber-700">
                <i class="fas fa-search mr-1"></i> Filtrele
            </button>
            @if(request()->hasAny(['start_date', 'end_date', 'direction', 'status', 'recipient_type', 'internet_sale', 'search']))
            <a href="{{ route('faturalar.earsiv') }}" class="text-sm text-gray-500 hover:text-red-500">
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
                        <th class="px-4 py-3 text-left">Yön</th>
                        <th class="px-4 py-3 text-left">Alıcı</th>
                        <th class="px-4 py-3 text-left">Alıcı Türü</th>
                        <th class="px-4 py-3 text-left">VKN / TCKN</th>
                        <th class="px-4 py-3 text-right">Tutar</th>
                        <th class="px-4 py-3 text-center">Durum</th>
                        <th class="px-4 py-3 text-center">İnternet</th>
                        <th class="px-4 py-3 text-left">Tarih</th>
                        <th class="px-4 py-3 text-center">İşlem</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($invoices as $inv)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-amber-600">
                            <a href="{{ route('faturalar.show', $inv) }}">{{ $inv->invoice_no ?? 'Taslak' }}</a>
                            @if($inv->earsiv_report_no)
                            <span class="block text-xs text-gray-400">Rapor: {{ $inv->earsiv_report_no }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $inv->direction === 'outgoing' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                                {{ $inv->direction_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            {{ $inv->receiver_name ?? ($inv->customer?->name ?? '-') }}
                        </td>
                        <td class="px-4 py-3">
                            @if($inv->recipient_type === 'individual')
                            <span class="px-2 py-0.5 rounded-full text-xs bg-purple-100 text-purple-700">
                                <i class="fas fa-user text-[10px] mr-0.5"></i> Bireysel
                            </span>
                            @else
                            <span class="px-2 py-0.5 rounded-full text-xs bg-indigo-100 text-indigo-700">
                                <i class="fas fa-building text-[10px] mr-0.5"></i> Kurumsal
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-mono text-xs">
                            @if($inv->recipient_type === 'individual' && $inv->tc_kimlik_no)
                                <span class="text-purple-600">{{ $inv->tc_kimlik_no }}</span>
                            @elseif($inv->receiver_tax_number)
                                {{ $inv->receiver_tax_number }}
                            @else
                                <span class="text-gray-300">-</span>
                            @endif
                        </td>
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
                        <td class="px-4 py-3 text-center">
                            @if($inv->is_internet_sale)
                            <span class="px-2 py-0.5 rounded-full text-xs bg-orange-100 text-orange-700">
                                <i class="fas fa-globe text-[10px] mr-0.5"></i> Evet
                            </span>
                            @else
                            <span class="text-gray-300">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $inv->invoice_date?->format('d.m.Y') ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('faturalar.show', $inv) }}" class="text-amber-600 hover:text-amber-800" title="Detay">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-4 py-12 text-center text-gray-400">
                            <i class="fas fa-archive text-5xl mb-4 block"></i>
                            <p class="text-lg">Henüz e-Arşiv fatura bulunmuyor.</p>
                            <p class="text-sm mt-2">
                                <a href="{{ route('faturalar.create', ['scenario' => 'e_arsiv']) }}" class="text-amber-600 hover:underline">
                                    <i class="fas fa-plus mr-1"></i>Yeni e-Arşiv Fatura Kes
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
