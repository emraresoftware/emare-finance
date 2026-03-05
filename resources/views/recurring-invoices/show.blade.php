@extends('layouts.app')
@section('title', $recurringInvoice->title)

@section('content')
<div class="space-y-6">

    {{-- Başlık --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">{{ $recurringInvoice->title }}</h2>
            <p class="text-sm text-gray-500">Tekrarlayan fatura detayları</p>
        </div>
        <div class="flex gap-2">
            @if($recurringInvoice->status === 'active')
            <form action="{{ route('recurring_invoices.generate_single', $recurringInvoice) }}" method="POST">
                @csrf
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700"
                    onclick="return confirm('Şimdi e-fatura oluşturmak istiyor musunuz?')">
                    <i class="fas fa-file-invoice mr-1"></i> Şimdi Fatura Oluştur
                </button>
            </form>
            @endif
            <a href="{{ route('recurring_invoices.index') }}" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-200">
                <i class="fas fa-arrow-left mr-1"></i> Listeye Dön
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Sol: Bilgiler --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Genel Bilgiler --}}
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="font-semibold text-gray-800 mb-4"><i class="fas fa-info-circle mr-2 text-blue-500"></i>Genel Bilgiler</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Müşteri</span>
                        <p class="font-medium text-gray-800">{{ $recurringInvoice->customer?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Şube</span>
                        <p class="font-medium text-gray-800">{{ $recurringInvoice->branch?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Hizmet Kategorisi</span>
                        <p class="font-medium text-gray-800">
                            @if($recurringInvoice->serviceCategory)
                                <span class="inline-flex items-center gap-1">
                                    <span class="w-2 h-2 rounded-full" style="background-color: {{ $recurringInvoice->serviceCategory->color ?? '#6366f1' }}"></span>
                                    {{ $recurringInvoice->serviceCategory->name }}
                                </span>
                            @else
                                -
                            @endif
                        </p>
                    </div>
                    <div>
                        <span class="text-gray-500">Ödeme Yöntemi</span>
                        <p class="font-medium text-gray-800">
                            @php
                                $pm = ['cash' => 'Nakit', 'card' => 'Kart', 'transfer' => 'Havale/EFT', 'credit' => 'Veresiye'];
                            @endphp
                            {{ $pm[$recurringInvoice->payment_method] ?? $recurringInvoice->payment_method ?? '-' }}
                        </p>
                    </div>
                    @if($recurringInvoice->description)
                    <div class="col-span-2">
                        <span class="text-gray-500">Açıklama</span>
                        <p class="text-gray-700">{{ $recurringInvoice->description }}</p>
                    </div>
                    @endif
                    @if($recurringInvoice->notes)
                    <div class="col-span-2">
                        <span class="text-gray-500">Notlar</span>
                        <p class="text-gray-700">{{ $recurringInvoice->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Kalemler --}}
            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                <div class="px-6 py-4 border-b">
                    <h3 class="font-semibold text-gray-800"><i class="fas fa-list mr-2 text-green-500"></i>Fatura Kalemleri</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                            <tr>
                                <th class="px-6 py-3 text-left">#</th>
                                <th class="px-6 py-3 text-left">Ürün/Hizmet</th>
                                <th class="px-6 py-3 text-center">Miktar</th>
                                <th class="px-6 py-3 text-right">Birim Fiyat</th>
                                <th class="px-6 py-3 text-right">İskonto</th>
                                <th class="px-6 py-3 text-left">Vergiler</th>
                                <th class="px-6 py-3 text-right">Toplam</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($recurringInvoice->items as $index => $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-gray-400">{{ $index + 1 }}</td>
                                <td class="px-6 py-3">
                                    <div class="font-medium text-gray-800">{{ $item->product_name }}</div>
                                    @if($item->product_code)
                                        <div class="text-xs text-gray-400">{{ $item->product_code }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-center">{{ $item->quantity }} {{ $item->unit }}</td>
                                <td class="px-6 py-3 text-right">₺{{ number_format($item->unit_price, 2, ',', '.') }}</td>
                                <td class="px-6 py-3 text-right">
                                    @if($item->discount > 0)
                                        <span class="text-red-600">-₺{{ number_format($item->discount, 2, ',', '.') }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3">
                                    @if(!empty($item->taxes))
                                        @foreach($item->taxes as $tax)
                                            <span class="inline-block bg-blue-100 text-blue-700 text-xs px-1.5 py-0.5 rounded mr-1">
                                                {{ $tax['code'] ?? '' }} %{{ $tax['rate'] ?? '' }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-right font-medium">₺{{ number_format($item->total, 2, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 font-medium">
                            <tr>
                                <td colspan="6" class="px-6 py-2 text-right text-gray-600">Ara Toplam:</td>
                                <td class="px-6 py-2 text-right">₺{{ number_format($recurringInvoice->subtotal, 2, ',', '.') }}</td>
                            </tr>
                            @if($recurringInvoice->discount_total > 0)
                            <tr>
                                <td colspan="6" class="px-6 py-2 text-right text-gray-600">İskonto:</td>
                                <td class="px-6 py-2 text-right text-red-600">-₺{{ number_format($recurringInvoice->discount_total, 2, ',', '.') }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td colspan="6" class="px-6 py-2 text-right text-gray-600">Vergi Toplamı:</td>
                                <td class="px-6 py-2 text-right">₺{{ number_format($recurringInvoice->tax_total, 2, ',', '.') }}</td>
                            </tr>
                            <tr class="text-lg">
                                <td colspan="6" class="px-6 py-3 text-right text-gray-800 font-bold">Genel Toplam:</td>
                                <td class="px-6 py-3 text-right text-indigo-700 font-bold">₺{{ number_format($recurringInvoice->grand_total, 2, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Oluşturulan E-Faturalar --}}
            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                <div class="px-6 py-4 border-b">
                    <h3 class="font-semibold text-gray-800"><i class="fas fa-file-invoice mr-2 text-orange-500"></i>Oluşturulan E-Faturalar ({{ $generatedInvoices->count() }})</h3>
                </div>
                @if($generatedInvoices->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                            <tr>
                                <th class="px-6 py-3 text-left">Fatura No</th>
                                <th class="px-6 py-3 text-center">Tarih</th>
                                <th class="px-6 py-3 text-right">Tutar</th>
                                <th class="px-6 py-3 text-center">Durum</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($generatedInvoices as $gi)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3">
                                    <a href="{{ route('einvoices.show', $gi) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                                        {{ $gi->invoice_no }}
                                    </a>
                                </td>
                                <td class="px-6 py-3 text-center">{{ $gi->invoice_date?->format('d.m.Y') }}</td>
                                <td class="px-6 py-3 text-right font-medium">₺{{ number_format($gi->grand_total, 2, ',', '.') }}</td>
                                <td class="px-6 py-3 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $gi->status_color }}-100 text-{{ $gi->status_color }}-700">
                                        {{ $gi->status_label }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="p-8 text-center text-gray-400">
                    <i class="fas fa-file-invoice text-3xl mb-2"></i>
                    <p>Henüz e-fatura oluşturulmamış.</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Sağ: Durum & Zamanlama --}}
        <div class="space-y-6">
            {{-- Durum --}}
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="font-semibold text-gray-800 mb-4"><i class="fas fa-signal mr-2 text-indigo-500"></i>Durum</h3>
                <div class="text-center mb-4">
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-{{ $recurringInvoice->status_color }}-100 text-{{ $recurringInvoice->status_color }}-700">
                        <i class="fas fa-circle text-xs mr-2"></i>
                        {{ $recurringInvoice->status_label }}
                    </span>
                </div>

                {{-- Durum Değiştir --}}
                <div class="space-y-2">
                    @if($recurringInvoice->status !== 'active')
                    <form action="{{ route('recurring_invoices.update_status', $recurringInvoice) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="active">
                        <button type="submit" class="w-full bg-green-50 text-green-700 px-3 py-2 rounded-lg text-sm hover:bg-green-100 border border-green-200">
                            <i class="fas fa-play mr-1"></i> Aktif Et
                        </button>
                    </form>
                    @endif
                    @if($recurringInvoice->status === 'active')
                    <form action="{{ route('recurring_invoices.update_status', $recurringInvoice) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="paused">
                        <button type="submit" class="w-full bg-yellow-50 text-yellow-700 px-3 py-2 rounded-lg text-sm hover:bg-yellow-100 border border-yellow-200">
                            <i class="fas fa-pause mr-1"></i> Duraklat
                        </button>
                    </form>
                    @endif
                    @if(!in_array($recurringInvoice->status, ['cancelled', 'completed']))
                    <form action="{{ route('recurring_invoices.update_status', $recurringInvoice) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="cancelled">
                        <button type="submit" class="w-full bg-red-50 text-red-700 px-3 py-2 rounded-lg text-sm hover:bg-red-100 border border-red-200"
                            onclick="return confirm('İptal etmek istediğinize emin misiniz?')">
                            <i class="fas fa-times mr-1"></i> İptal Et
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            {{-- Zamanlama --}}
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="font-semibold text-gray-800 mb-4"><i class="fas fa-calendar mr-2 text-purple-500"></i>Zamanlama</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Sıklık</span>
                        <span class="font-medium bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-xs">{{ $recurringInvoice->frequency_label }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Ayın Günü</span>
                        <span class="font-medium">{{ $recurringInvoice->frequency_day }}.</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Başlangıç</span>
                        <span class="font-medium">{{ $recurringInvoice->start_date->format('d.m.Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Bitiş</span>
                        <span class="font-medium">{{ $recurringInvoice->end_date?->format('d.m.Y') ?? 'Süresiz' }}</span>
                    </div>
                    <div class="border-t pt-3 flex justify-between">
                        <span class="text-gray-500">Sonraki Fatura</span>
                        <span class="font-medium {{ $recurringInvoice->next_invoice_date?->isPast() ? 'text-red-600' : 'text-green-600' }}">
                            {{ $recurringInvoice->next_invoice_date?->format('d.m.Y') ?? '-' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Son Fatura</span>
                        <span class="font-medium">{{ $recurringInvoice->last_invoice_date?->format('d.m.Y') ?? 'Henüz yok' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Oluşturulan</span>
                        <span class="font-medium">
                            {{ $recurringInvoice->invoices_generated }}
                            @if($recurringInvoice->max_invoices)
                                / {{ $recurringInvoice->max_invoices }}
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Otomatik Gönderim</span>
                        <span class="font-medium">
                            @if($recurringInvoice->auto_send)
                                <i class="fas fa-check-circle text-green-500"></i> Açık
                            @else
                                <i class="fas fa-times-circle text-gray-400"></i> Kapalı
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            {{-- Finansal --}}
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="font-semibold text-gray-800 mb-4"><i class="fas fa-turkish-lira-sign mr-2 text-green-500"></i>Finansal</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Ara Toplam</span>
                        <span class="font-medium">₺{{ number_format($recurringInvoice->subtotal, 2, ',', '.') }}</span>
                    </div>
                    @if($recurringInvoice->discount_total > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-500">İskonto</span>
                        <span class="font-medium text-red-600">-₺{{ number_format($recurringInvoice->discount_total, 2, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-500">Vergi</span>
                        <span class="font-medium">₺{{ number_format($recurringInvoice->tax_total, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between border-t pt-3 text-lg">
                        <span class="font-bold text-gray-800">Toplam</span>
                        <span class="font-bold text-indigo-700">₺{{ number_format($recurringInvoice->grand_total, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Para Birimi</span>
                        <span class="font-medium">{{ $recurringInvoice->currency }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
