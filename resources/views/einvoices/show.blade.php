@extends('layouts.app')
@section('title', 'E-Fatura Detay - ' . ($einvoice->invoice_no ?? 'Taslak'))

@section('content')
<div class="space-y-6">
    {{-- Başlık --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $einvoice->invoice_no ?? 'Taslak Fatura' }}</h1>
            <p class="text-sm text-gray-500 mt-1">
                {{ $einvoice->direction_label }} • {{ $einvoice->type_label }} • {{ $einvoice->scenario_label }}
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
            <a href="{{ $einvoice->direction === 'outgoing' ? route('einvoices.outgoing') : route('einvoices.incoming') }}" class="text-sm text-gray-500 hover:text-gray-700 ml-2">
                <i class="fas fa-arrow-left mr-1"></i> Geri
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Sol: Fatura Bilgileri --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Genel Bilgiler --}}
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-info-circle text-indigo-500 mr-2"></i>Fatura Bilgileri
                </h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Fatura No:</span>
                        <span class="font-medium ml-2">{{ $einvoice->invoice_no ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">UUID:</span>
                        <span class="font-medium ml-2 text-xs">{{ $einvoice->uuid ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Fatura Tarihi:</span>
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

            {{-- Kalemler --}}
            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                <div class="px-6 py-4 border-b">
                    <h3 class="font-semibold text-gray-800">
                        <i class="fas fa-list text-purple-500 mr-2"></i>Fatura Kalemleri
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
        </div>
    </div>
</div>
@endsection
