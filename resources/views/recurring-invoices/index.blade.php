@extends('layouts.app')
@section('title', 'Tekrarlayan Faturalar')

@section('content')
<div class="space-y-6">

    {{-- Başlık --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Tekrarlayan Faturalar</h2>
            <p class="text-sm text-gray-500">Aylık düzenli fatura ve abonelik yönetimi.</p>
        </div>
        <div class="flex gap-2">
            @if($stats['due_today'] > 0)
            <form action="{{ route('recurring_invoices.generate_due') }}" method="POST">
                @csrf
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700"
                    onclick="return confirm('Vadesi gelen {{ $stats['due_today'] }} fatura için e-fatura oluşturulacak. Devam?')">
                    <i class="fas fa-file-invoice mr-1"></i> Vadesi Gelenleri Oluştur ({{ $stats['due_today'] }})
                </button>
            </form>
            @endif
            <a href="{{ route('recurring_invoices.create') }}"
                class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">
                <i class="fas fa-plus mr-1"></i> Yeni Tekrarlayan Fatura
            </a>
        </div>
    </div>

    {{-- İstatistikler --}}
    <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <p class="text-xs text-gray-500">Toplam</p>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <p class="text-xs text-gray-500">Aktif</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['active'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <p class="text-xs text-gray-500">Duraklatılmış</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $stats['paused'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <p class="text-xs text-gray-500">Bugün Vadesi Gelen</p>
            <p class="text-2xl font-bold text-red-600">{{ $stats['due_today'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <p class="text-xs text-gray-500">Aylık Gelir</p>
            <p class="text-xl font-bold text-indigo-600">₺{{ number_format($stats['monthly_revenue'], 2, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <p class="text-xs text-gray-500">Oluşturulan Fatura</p>
            <p class="text-2xl font-bold text-blue-600">{{ $stats['total_generated'] }}</p>
        </div>
    </div>

    {{-- Filtreleme --}}
    <div class="bg-white rounded-xl shadow-sm border p-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs text-gray-500 mb-1">Ara</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Başlık veya müşteri..."
                    class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
            </div>
            <div class="w-36">
                <label class="block text-xs text-gray-500 mb-1">Durum</label>
                <select name="status" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                    <option value="">Tümü</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="paused" {{ request('status') == 'paused' ? 'selected' : '' }}>Duraklatılmış</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>İptal</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Tamamlandı</option>
                </select>
            </div>
            <div class="w-36">
                <label class="block text-xs text-gray-500 mb-1">Sıklık</label>
                <select name="frequency" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                    <option value="">Tümü</option>
                    <option value="weekly" {{ request('frequency') == 'weekly' ? 'selected' : '' }}>Haftalık</option>
                    <option value="monthly" {{ request('frequency') == 'monthly' ? 'selected' : '' }}>Aylık</option>
                    <option value="quarterly" {{ request('frequency') == 'quarterly' ? 'selected' : '' }}>3 Aylık</option>
                    <option value="annual" {{ request('frequency') == 'annual' ? 'selected' : '' }}>Yıllık</option>
                </select>
            </div>
            <div class="w-40">
                <label class="block text-xs text-gray-500 mb-1">Hizmet Kategorisi</label>
                <select name="service_category_id" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                    <option value="">Tümü</option>
                    @foreach($serviceCategories as $sc)
                        <option value="{{ $sc->id }}" {{ request('service_category_id') == $sc->id ? 'selected' : '' }}>{{ $sc->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-200">
                <i class="fas fa-search mr-1"></i> Filtrele
            </button>
        </form>
    </div>

    {{-- Liste --}}
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-6 py-3 text-left">Başlık</th>
                        <th class="px-6 py-3 text-left">Müşteri</th>
                        <th class="px-6 py-3 text-left">Kategori</th>
                        <th class="px-6 py-3 text-center">Sıklık</th>
                        <th class="px-6 py-3 text-right">Tutar</th>
                        <th class="px-6 py-3 text-center">Sonraki Fatura</th>
                        <th class="px-6 py-3 text-center">Oluşturulan</th>
                        <th class="px-6 py-3 text-center">Durum</th>
                        <th class="px-6 py-3 text-center">İşlem</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recurringInvoices as $ri)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3">
                            <a href="{{ route('recurring_invoices.show', $ri) }}" class="font-medium text-indigo-600 hover:text-indigo-800">
                                {{ $ri->title }}
                            </a>
                            @if($ri->auto_send)
                                <span class="ml-1 text-xs text-green-500" title="Otomatik gönderim"><i class="fas fa-bolt"></i></span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-gray-600">{{ $ri->customer?->name ?? '-' }}</td>
                        <td class="px-6 py-3">
                            @if($ri->serviceCategory)
                                <span class="inline-flex items-center gap-1 text-xs">
                                    <span class="w-2 h-2 rounded-full" style="background-color: {{ $ri->serviceCategory->color ?? '#6366f1' }}"></span>
                                    {{ $ri->serviceCategory->name }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-center">
                            <span class="bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded-full">{{ $ri->frequency_label }}</span>
                        </td>
                        <td class="px-6 py-3 text-right font-medium">
                            ₺{{ number_format($ri->grand_total, 2, ',', '.') }}
                        </td>
                        <td class="px-6 py-3 text-center text-xs">
                            @if($ri->next_invoice_date)
                                <span class="{{ $ri->next_invoice_date->isPast() ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                                    {{ $ri->next_invoice_date->format('d.m.Y') }}
                                </span>
                                @if($ri->next_invoice_date->isPast())
                                    <i class="fas fa-exclamation-triangle text-red-500 ml-1" title="Vadesi geçmiş!"></i>
                                @endif
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-center">
                            <span class="font-medium text-gray-700">{{ $ri->invoices_generated }}</span>
                            @if($ri->max_invoices)
                                <span class="text-gray-400">/ {{ $ri->max_invoices }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $ri->status_color }}-100 text-{{ $ri->status_color }}-700">
                                {{ $ri->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('recurring_invoices.show', $ri) }}" class="text-blue-600 hover:text-blue-800" title="Detay">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($ri->status === 'active')
                                <form action="{{ route('recurring_invoices.generate_single', $ri) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-800" title="Şimdi Fatura Oluştur"
                                        onclick="return confirm('Bu fatura için şimdi e-fatura oluşturmak istiyor musunuz?')">
                                        <i class="fas fa-file-invoice"></i>
                                    </button>
                                </form>
                                @endif
                                <form action="{{ route('recurring_invoices.destroy', $ri) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Silmek istediğinize emin misiniz?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Sil">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center text-gray-400">
                            <i class="fas fa-sync text-3xl mb-3 block"></i>
                            Henüz tekrarlayan fatura eklenmemiş.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($recurringInvoices->hasPages())
        <div class="px-6 py-4 border-t">
            {{ $recurringInvoices->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
