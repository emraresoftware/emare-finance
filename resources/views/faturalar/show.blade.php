@extends('layouts.app')
@section('title', ($einvoice->document_type === 'irsaliye' ? 'İrsaliye' : 'Fatura') . ' Detay - ' . ($einvoice->invoice_no ?? 'Taslak'))

@section('content')
<div class="space-y-6">
    {{-- Başlık --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $einvoice->invoice_no ?? 'Taslak Belge' }}</h1>
            <p class="text-sm text-gray-500 mt-1">
                {{ $einvoice->document_type_label }} • {{ $einvoice->direction_label }} • {{ $einvoice->type_label }}
                @if($einvoice->scenario_label) • {{ $einvoice->scenario_label }} @endif
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 rounded-full text-sm font-medium
                {{ $einvoice->status === 'draft' ? 'bg-gray-100 text-gray-700' : '' }}
                {{ $einvoice->status === 'sent' ? 'bg-blue-100 text-blue-700' : '' }}
                {{ $einvoice->status === 'accepted' ? 'bg-green-100 text-green-700' : '' }}
                {{ $einvoice->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}
                {{ $einvoice->status === 'cancelled' ? 'bg-orange-100 text-orange-700' : '' }}
            ">{{ $einvoice->status_label }}</span>

            {{-- Durum Güncelle --}}
            @if($einvoice->status === 'draft')
            <form method="POST" action="{{ route('faturalar.status', $einvoice) }}" class="inline">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="sent">
                <button type="submit" class="bg-blue-600 text-white px-3 py-1.5 rounded-lg text-xs hover:bg-blue-700">
                    <i class="fas fa-paper-plane mr-1"></i> Gönderildi Yap
                </button>
            </form>
            @endif

            @php
                $backUrl = $einvoice->document_type === 'irsaliye'
                    ? route('faturalar.waybills')
                    : ($einvoice->direction === 'outgoing'
                        ? route('faturalar.outgoing')
                        : route('faturalar.incoming'));
            @endphp
            <a href="{{ $backUrl }}" class="text-sm text-gray-500 hover:text-gray-700 ml-2">
                <i class="fas fa-arrow-left mr-1"></i> Geri
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Sol: Belge Bilgileri --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Genel Bilgiler --}}
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-info-circle text-indigo-500 mr-2"></i>
                    {{ $einvoice->document_type === 'irsaliye' ? 'İrsaliye Bilgileri' : 'Fatura Bilgileri' }}
                </h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Belge No:</span>
                        <span class="font-medium ml-2">{{ $einvoice->invoice_no ?? '-' }}</span>
                    </div>
                    @if($einvoice->uuid)
                    <div>
                        <span class="text-gray-500">UUID:</span>
                        <span class="font-medium ml-2 text-xs">{{ $einvoice->uuid }}</span>
                    </div>
                    @endif
                    <div>
                        <span class="text-gray-500">Belge Türü:</span>
                        <span class="font-medium ml-2">
                            @if($einvoice->document_type === 'irsaliye')
                            <span class="px-2 py-0.5 bg-purple-100 text-purple-700 rounded-full text-xs">İrsaliye</span>
                            @else
                            <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded-full text-xs">Fatura</span>
                            @endif
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-500">Tarih:</span>
                        <span class="font-medium ml-2">{{ $einvoice->invoice_date?->format('d.m.Y') ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Para Birimi:</span>
                        <span class="font-medium ml-2">{{ $einvoice->currency }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Ödeme Yöntemi:</span>
                        <span class="font-medium ml-2">{{ $einvoice->payment_method ?? '-' }}</span>
                    </div>
                    @if($einvoice->branch)
                    <div>
                        <span class="text-gray-500">Şube:</span>
                        <span class="font-medium ml-2">{{ $einvoice->branch->name }}</span>
                    </div>
                    @endif
                    @if($einvoice->sale)
                    <div>
                        <span class="text-gray-500">Satış:</span>
                        <a href="{{ route('sales.show', $einvoice->sale) }}" class="font-medium ml-2 text-indigo-600 hover:underline">{{ $einvoice->sale->receipt_no }}</a>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Sevkiyat Bilgileri (İrsaliye ise) --}}
            @if($einvoice->document_type === 'irsaliye')
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-shipping-fast text-emerald-500 mr-2"></i>Sevkiyat Bilgileri
                </h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    @if($einvoice->waybill_no)
                    <div>
                        <span class="text-gray-500">İrsaliye No:</span>
                        <span class="font-medium ml-2">{{ $einvoice->waybill_no }}</span>
                    </div>
                    @endif
                    @if($einvoice->shipment_date)
                    <div>
                        <span class="text-gray-500">Sevk Tarihi:</span>
                        <span class="font-medium ml-2">{{ $einvoice->shipment_date->format('d.m.Y') }}</span>
                    </div>
                    @endif
                    @if($einvoice->delivery_address)
                    <div class="col-span-2">
                        <span class="text-gray-500">Teslimat Adresi:</span>
                        <span class="font-medium ml-2">{{ $einvoice->delivery_address }}</span>
                    </div>
                    @endif
                    @if($einvoice->vehicle_plate)
                    <div>
                        <span class="text-gray-500">Araç Plakası:</span>
                        <span class="font-medium ml-2 bg-gray-100 px-2 py-0.5 rounded font-mono">{{ $einvoice->vehicle_plate }}</span>
                    </div>
                    @endif
                    @if($einvoice->driver_name)
                    <div>
                        <span class="text-gray-500">Şoför:</span>
                        <span class="font-medium ml-2">{{ $einvoice->driver_name }}</span>
                    </div>
                    @endif
                    @if($einvoice->driver_tc)
                    <div>
                        <span class="text-gray-500">Şoför TC:</span>
                        <span class="font-medium ml-2">{{ $einvoice->driver_tc }}</span>
                    </div>
                    @endif
                    @if($einvoice->shipping_company)
                    <div>
                        <span class="text-gray-500">Kargo Firması:</span>
                        <span class="font-medium ml-2">{{ $einvoice->shipping_company }}</span>
                    </div>
                    @endif
                    @if($einvoice->tracking_no)
                    <div>
                        <span class="text-gray-500">Takip No:</span>
                        <span class="font-medium ml-2 bg-indigo-50 px-2 py-0.5 rounded text-indigo-700">{{ $einvoice->tracking_no }}</span>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Kalemler --}}
            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                <div class="px-6 py-4 border-b">
                    <h3 class="font-semibold text-gray-800">
                        <i class="fas fa-list text-purple-500 mr-2"></i>Kalemler
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                            <tr>
                                <th class="px-4 py-3 text-left">#</th>
                                <th class="px-4 py-3 text-left">Ürün / Hizmet</th>
                                <th class="px-4 py-3 text-left">Kod</th>
                                <th class="px-4 py-3 text-right">Miktar</th>
                                <th class="px-4 py-3 text-right">Birim Fiyat</th>
                                <th class="px-4 py-3 text-right">İskonto</th>
                                <th class="px-4 py-3 text-right">KDV</th>
                                <th class="px-4 py-3 text-right">Toplam</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($einvoice->items as $i => $item)
                            <tr>
                                <td class="px-4 py-3 text-gray-400">{{ $i + 1 }}</td>
                                <td class="px-4 py-3 font-medium">{{ $item->product_name }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ $item->product_code ?? '-' }}</td>
                                <td class="px-4 py-3 text-right">{{ rtrim(rtrim(number_format($item->quantity, 3, ',', '.'), '0'), ',') }} {{ $item->unit }}</td>
                                <td class="px-4 py-3 text-right">₺{{ number_format($item->unit_price, 2, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right text-red-500">{{ $item->discount > 0 ? '-₺' . number_format($item->discount, 2, ',', '.') : '-' }}</td>
                                <td class="px-4 py-3 text-right">%{{ $item->vat_rate }}</td>
                                <td class="px-4 py-3 text-right font-medium">₺{{ number_format($item->total, 2, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Notlar --}}
            @if($einvoice->notes)
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="font-semibold text-gray-800 mb-2">
                    <i class="fas fa-sticky-note text-yellow-500 mr-2"></i>Notlar
                </h3>
                <p class="text-sm text-gray-600">{{ $einvoice->notes }}</p>
            </div>
            @endif
        </div>

        {{-- Sağ: Alıcı & Toplamlar --}}
        <div class="space-y-6">
            {{-- Alıcı --}}
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="font-semibold text-gray-800 mb-3">
                    <i class="fas fa-user text-green-500 mr-2"></i>{{ $einvoice->direction === 'outgoing' ? 'Alıcı' : 'Gönderen' }}
                </h3>
                <div class="space-y-2 text-sm">
                    <p class="font-medium text-gray-800">{{ $einvoice->receiver_name ?? '-' }}</p>
                    @if($einvoice->receiver_tax_number)
                    <p class="text-gray-500">VKN/TCKN: {{ $einvoice->receiver_tax_number }}</p>
                    @endif
                    @if($einvoice->receiver_tax_office)
                    <p class="text-gray-500">V.D.: {{ $einvoice->receiver_tax_office }}</p>
                    @endif
                    @if($einvoice->receiver_address)
                    <p class="text-gray-500">{{ $einvoice->receiver_address }}</p>
                    @endif
                    @if($einvoice->customer)
                    <a href="{{ route('customers.show', $einvoice->customer) }}" class="text-indigo-600 hover:underline text-xs">
                        <i class="fas fa-external-link-alt mr-1"></i>Müşteri Detay
                    </a>
                    @endif
                </div>
            </div>

            {{-- Toplamlar --}}
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="font-semibold text-gray-800 mb-3">
                    <i class="fas fa-calculator text-indigo-500 mr-2"></i>Toplamlar
                </h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Ara Toplam:</span>
                        <span class="font-medium">₺{{ number_format($einvoice->subtotal, 2, ',', '.') }}</span>
                    </div>
                    @if($einvoice->discount_total > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-500">İskonto:</span>
                        <span class="font-medium text-red-500">-₺{{ number_format($einvoice->discount_total, 2, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-500">KDV:</span>
                        <span class="font-medium">₺{{ number_format($einvoice->vat_total, 2, ',', '.') }}</span>
                    </div>
                    @if($einvoice->withholding_total > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-500">Tevkifat:</span>
                        <span class="font-medium text-red-500">-₺{{ number_format($einvoice->withholding_total, 2, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="border-t pt-3 flex justify-between">
                        <span class="font-bold text-gray-800">Genel Toplam:</span>
                        <span class="font-bold text-indigo-600 text-lg">₺{{ number_format($einvoice->grand_total, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- Tarih Bilgileri --}}
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="font-semibold text-gray-800 mb-3">
                    <i class="fas fa-clock text-gray-500 mr-2"></i>Tarihçe
                </h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Oluşturulma:</span>
                        <span>{{ $einvoice->created_at?->format('d.m.Y H:i') }}</span>
                    </div>
                    @if($einvoice->sent_at)
                    <div class="flex justify-between">
                        <span class="text-gray-500">Gönderilme:</span>
                        <span>{{ $einvoice->sent_at->format('d.m.Y H:i') }}</span>
                    </div>
                    @endif
                    @if($einvoice->received_at)
                    <div class="flex justify-between">
                        <span class="text-gray-500">Alınma:</span>
                        <span>{{ $einvoice->received_at->format('d.m.Y H:i') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- İşlemler --}}
            @if($einvoice->status !== 'cancelled')
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="font-semibold text-gray-800 mb-3">
                    <i class="fas fa-cog text-gray-500 mr-2"></i>İşlemler
                </h3>
                <div class="space-y-2">
                    @if($einvoice->status === 'draft')
                    <form method="POST" action="{{ route('faturalar.status', $einvoice) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="sent">
                        <button class="w-full text-left px-3 py-2 rounded-lg text-sm hover:bg-blue-50 text-blue-600">
                            <i class="fas fa-paper-plane mr-2"></i>Gönderildi Olarak İşaretle
                        </button>
                    </form>
                    @endif
                    @if(in_array($einvoice->status, ['sent']))
                    <form method="POST" action="{{ route('faturalar.status', $einvoice) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="accepted">
                        <button class="w-full text-left px-3 py-2 rounded-lg text-sm hover:bg-green-50 text-green-600">
                            <i class="fas fa-check mr-2"></i>Kabul Edildi
                        </button>
                    </form>
                    <form method="POST" action="{{ route('faturalar.status', $einvoice) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="rejected">
                        <button class="w-full text-left px-3 py-2 rounded-lg text-sm hover:bg-red-50 text-red-600">
                            <i class="fas fa-times mr-2"></i>Reddedildi
                        </button>
                    </form>
                    @endif
                    <form method="POST" action="{{ route('faturalar.status', $einvoice) }}" onsubmit="return confirm('Bu belgeyi iptal etmek istediğinize emin misiniz?')">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="cancelled">
                        <button class="w-full text-left px-3 py-2 rounded-lg text-sm hover:bg-orange-50 text-orange-600">
                            <i class="fas fa-ban mr-2"></i>İptal Et
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
