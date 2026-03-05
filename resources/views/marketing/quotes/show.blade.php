@extends('layouts.app')
@section('title', 'Teklif - ' . $quote->quote_number)

@section('content')
<div class="space-y-6">
    {{-- Üst Başlık --}}
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('marketing.quotes.index') }}" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>
                <div>
                    <h2 class="text-lg font-bold text-gray-800">{{ $quote->quote_number }}</h2>
                    <p class="text-sm text-gray-500">{{ $quote->title }}</p>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $quote->status_color }}-100 text-{{ $quote->status_color }}-800">
                    {{ $quote->status_label }}
                </span>
            </div>
            <div class="flex gap-2">
                @if($quote->status === 'draft')
                    <form method="POST" action="{{ route('marketing.quotes.send', $quote) }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 font-medium" onclick="return confirm('Teklifi müşteriye göndermek istediğinize emin misiniz?')">
                            <i class="fas fa-paper-plane mr-1"></i> Gönder
                        </button>
                    </form>
                @endif
                @if(in_array($quote->status, ['sent', 'pending']))
                    <form method="POST" action="{{ route('marketing.quotes.status', $quote) }}" class="inline">
                        @csrf
                        <input type="hidden" name="status" value="accepted">
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 font-medium">
                            <i class="fas fa-check mr-1"></i> Kabul Et
                        </button>
                    </form>
                    <form method="POST" action="{{ route('marketing.quotes.status', $quote) }}" class="inline">
                        @csrf
                        <input type="hidden" name="status" value="rejected">
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700 font-medium">
                            <i class="fas fa-times mr-1"></i> Reddet
                        </button>
                    </form>
                @endif
                <form method="POST" action="{{ route('marketing.quotes.duplicate', $quote) }}" class="inline">
                    @csrf
                    <button type="submit" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-200 font-medium">
                        <i class="fas fa-copy mr-1"></i> Kopyala
                    </button>
                </form>
                <button onclick="window.print()" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-200">
                    <i class="fas fa-print mr-1"></i> Yazdır
                </button>
            </div>
        </div>
    </div>

    {{-- Teklif Bilgileri --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            {{-- Müşteri & Teklif Detay --}}
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="text-md font-semibold text-gray-800 mb-4"><i class="fas fa-info-circle text-indigo-500 mr-2"></i>Teklif Bilgileri</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="flex justify-between"><span class="text-gray-500">Müşteri</span><span class="font-medium">{{ $quote->customer_name }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Teklif No</span><span class="font-medium">{{ $quote->quote_number }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Düzenleme Tarihi</span><span>{{ $quote->issue_date?->format('d.m.Y') }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Geçerlilik Tarihi</span>
                        <span class="{{ $quote->valid_until && $quote->valid_until->isPast() ? 'text-red-500 font-medium' : '' }}">
                            {{ $quote->valid_until?->format('d.m.Y') ?? '-' }}
                            @if($quote->valid_until && $quote->valid_until->isPast())
                                <i class="fas fa-exclamation-triangle ml-1"></i>
                            @endif
                        </span>
                    </div>
                    @if($quote->sent_at)
                        <div class="flex justify-between"><span class="text-gray-500">Gönderilme</span><span>{{ $quote->sent_at->format('d.m.Y H:i') }}</span></div>
                    @endif
                    @if($quote->creator)
                        <div class="flex justify-between"><span class="text-gray-500">Oluşturan</span><span>{{ $quote->creator->name }}</span></div>
                    @endif
                </div>
            </div>

            {{-- Kalem Tablosu --}}
            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                <div class="p-6 pb-0">
                    <h3 class="text-md font-semibold text-gray-800 mb-4"><i class="fas fa-list text-indigo-500 mr-2"></i>Teklif Kalemleri</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ürün/Hizmet</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Miktar</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Birim Fiyat</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">İndirim</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">KDV</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Toplam</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($quote->items as $i => $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-gray-400">{{ $i + 1 }}</td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-800">{{ $item->name }}</div>
                                        @if($item->description)
                                            <div class="text-xs text-gray-500 mt-0.5">{{ $item->description }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">{{ number_format($item->quantity, 2, ',', '.') }} {{ $item->unit }}</td>
                                    <td class="px-4 py-3 text-right">₺{{ number_format($item->unit_price, 2, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-center">
                                        @if($item->discount_rate > 0)
                                            <span class="text-red-600">%{{ $item->discount_rate }}</span>
                                            <div class="text-xs text-gray-400">-₺{{ number_format($item->discount_amount, 2, ',', '.') }}</div>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        %{{ $item->tax_rate }}
                                        <div class="text-xs text-gray-400">₺{{ number_format($item->tax_amount, 2, ',', '.') }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold">₺{{ number_format($item->total, 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Notlar & Şartlar --}}
            @if($quote->notes || $quote->terms)
                <div class="bg-white rounded-xl shadow-sm border p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($quote->notes)
                            <div>
                                <h4 class="text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-sticky-note text-yellow-500 mr-1"></i> Notlar</h4>
                                <p class="text-sm text-gray-600 whitespace-pre-line">{{ $quote->notes }}</p>
                            </div>
                        @endif
                        @if($quote->terms)
                            <div>
                                <h4 class="text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-gavel text-indigo-500 mr-1"></i> Şartlar & Koşullar</h4>
                                <p class="text-sm text-gray-600 whitespace-pre-line">{{ $quote->terms }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- Sağ Panel - Özet --}}
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="text-md font-semibold text-gray-800 mb-4">Tutar Özeti</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Ara Toplam</span>
                        <span>₺{{ number_format($quote->items->sum(fn($i) => $i->quantity * $i->unit_price), 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Toplam İndirim</span>
                        <span class="text-red-600">-₺{{ number_format($quote->items->sum('discount_amount'), 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">KDV</span>
                        <span>₺{{ number_format($quote->items->sum('tax_amount'), 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between border-t pt-3">
                        <span class="font-bold text-gray-800">Genel Toplam</span>
                        <span class="font-bold text-xl text-indigo-600">₺{{ number_format($quote->grand_total, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- Zaman Çizelgesi --}}
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="text-md font-semibold text-gray-800 mb-4">Zaman Çizelgesi</h3>
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-plus text-gray-500 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">Oluşturuldu</p>
                            <p class="text-xs text-gray-500">{{ $quote->created_at->format('d.m.Y H:i') }}</p>
                        </div>
                    </div>
                    @if($quote->sent_at)
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fas fa-paper-plane text-blue-500 text-xs"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">Gönderildi</p>
                                <p class="text-xs text-gray-500">{{ $quote->sent_at->format('d.m.Y H:i') }}</p>
                            </div>
                        </div>
                    @endif
                    @if($quote->status === 'accepted')
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fas fa-check text-green-500 text-xs"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-green-700">Kabul Edildi</p>
                                <p class="text-xs text-gray-500">{{ $quote->updated_at->format('d.m.Y H:i') }}</p>
                            </div>
                        </div>
                    @elseif($quote->status === 'rejected')
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fas fa-times text-red-500 text-xs"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-red-700">Reddedildi</p>
                                <p class="text-xs text-gray-500">{{ $quote->updated_at->format('d.m.Y H:i') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
