@extends('layouts.app')
@section('title', 'Satış Detay - ' . ($sale->receipt_no ?: '#'.$sale->id))

@section('content')
<div class="mb-4 flex items-center justify-between">
    <a href="{{ route('sales.index') }}" class="text-sm text-indigo-600"><i class="fas fa-arrow-left mr-1"></i> Satışlara Dön</a>
    <div class="flex items-center gap-2">
        <button onclick="printReceipt()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">
            <i class="fas fa-receipt mr-1"></i> Fiş Yazdır
        </button>
        <button onclick="openDrawer()" class="px-4 py-2 bg-amber-500 text-white rounded-lg text-sm hover:bg-amber-600">
            <i class="fas fa-cash-register mr-1"></i> Çekmece Aç
        </button>
        <button onclick="window.print()" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm hover:bg-gray-200">
            <i class="fas fa-print mr-1"></i> Yazdır
        </button>
    </div>
</div>

{{-- İstatistik Kartları --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Kalem Sayısı</p>
        <p class="text-xl font-bold">{{ $saleStats['item_count'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Toplam Adet</p>
        <p class="text-xl font-bold text-blue-600">{{ $saleStats['total_quantity'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Toplam İndirim</p>
        <p class="text-xl font-bold text-red-600">₺{{ number_format($saleStats['total_discount'], 2, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Tahmini Kâr</p>
        <p class="text-xl font-bold text-green-600">₺{{ number_format($saleStats['profit'], 2, ',', '.') }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Sol Panel - Satış Bilgileri --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border">
        <h3 class="font-semibold text-gray-800 mb-4"><i class="fas fa-info-circle mr-1 text-indigo-500"></i> Satış Bilgileri</h3>
        <div class="space-y-3 text-sm">
            <div class="flex justify-between"><span class="text-gray-500">Fiş No</span><span class="font-bold">{{ $sale->receipt_no ?: '-' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Tarih</span><span>{{ $sale->sold_at?->format('d.m.Y H:i') }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Müşteri</span>
                @if($sale->customer)
                    <a href="{{ route('customers.show', $sale->customer) }}" class="text-indigo-600 hover:underline">{{ $sale->customer->name }}</a>
                @else
                    <span>Genel</span>
                @endif
            </div>
            <div class="flex justify-between"><span class="text-gray-500">Personel</span><span>{{ $sale->staff_name ?? '-' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Şube</span><span>{{ $sale->branch?->name ?? '-' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Ödeme</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                    {{ $sale->payment_method === 'cash' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $sale->payment_method === 'card' ? 'bg-blue-100 text-blue-800' : '' }}
                    {{ $sale->payment_method === 'credit' ? 'bg-red-100 text-red-800' : '' }}
                    {{ $sale->payment_method === 'mixed' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                    {{ $sale->payment_method_label }}
                </span>
            </div>
            <div class="flex justify-between"><span class="text-gray-500">Durum</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                    {{ $sale->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $sale->status_label }}
                </span>
            </div>
            <hr>
            <div class="flex justify-between"><span class="text-gray-500">Ara Toplam</span><span>₺{{ number_format($sale->subtotal, 2, ',', '.') }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">KDV</span><span>₺{{ number_format($sale->vat_total, 2, ',', '.') }}</span></div>
            @if($sale->discount_total > 0)
            <div class="flex justify-between"><span class="text-gray-500">İndirim</span><span class="text-red-500">-₺{{ number_format($sale->discount_total, 2, ',', '.') }}</span></div>
            @endif
            <div class="flex justify-between text-lg"><span class="font-bold">Toplam</span><span class="font-bold text-green-600">₺{{ number_format($sale->grand_total, 2, ',', '.') }}</span></div>
        </div>
    </div>

    {{-- Sağ Panel - Satış Kalemleri --}}
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6 border">
        <h3 class="font-semibold text-gray-800 mb-4"><i class="fas fa-list mr-1 text-indigo-500"></i> Satış Kalemleri</h3>
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Ürün</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Barkod</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Miktar</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">B.Fiyat</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">İndirim</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Toplam</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($sale->items as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 font-medium">
                            @if($item->product)
                                <a href="{{ route('products.show', $item->product) }}" class="text-indigo-600 hover:underline">{{ $item->product_name }}</a>
                            @else
                                {{ $item->product_name }}
                            @endif
                        </td>
                        <td class="px-3 py-2 text-gray-500 font-mono text-xs">{{ $item->barcode ?: '-' }}</td>
                        <td class="px-3 py-2 text-right">{{ $item->quantity }}</td>
                        <td class="px-3 py-2 text-right">₺{{ number_format($item->unit_price, 2, ',', '.') }}</td>
                        <td class="px-3 py-2 text-right text-red-500">{{ $item->discount > 0 ? '-₺'.number_format($item->discount, 2, ',', '.') : '-' }}</td>
                        <td class="px-3 py-2 text-right font-semibold">₺{{ number_format($item->total, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50">
                <tr>
                    <td colspan="2" class="px-3 py-2 font-semibold">Toplam</td>
                    <td class="px-3 py-2 text-right font-semibold">{{ $sale->items->sum('quantity') }}</td>
                    <td class="px-3 py-2"></td>
                    <td class="px-3 py-2 text-right font-semibold text-red-500">
                        @if($sale->items->sum('discount') > 0)-₺{{ number_format($sale->items->sum('discount'), 2, ',', '.') }}@else - @endif
                    </td>
                    <td class="px-3 py-2 text-right font-bold text-green-600">₺{{ number_format($sale->items->sum('total'), 2, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Fiş yazdırma
async function printReceipt() {
    if (!window.hw) { alert('Donanım sürücüleri yüklenmedi.'); return; }

    const receipt = {
        header: {
            company: 'EMARE FİNANS',
            address: '',
            phone: '',
            taxId: ''
        },
        receiptNo: @json($sale->receipt_no ?: '#'.$sale->id),
        date: @json($sale->sold_at?->format('d.m.Y H:i')),
        cashier: @json($sale->staff_name ?? 'Personel'),
        customer: @json($sale->customer?->name ?? 'Genel Müşteri'),
        items: [
            @foreach($sale->items as $item)
            {
                name: @json($item->product_name),
                quantity: {{ $item->quantity }},
                price: {{ $item->unit_price }},
                discount: {{ $item->discount ?? 0 }},
                total: {{ $item->total }}
            },
            @endforeach
        ],
        subtotal: {{ $sale->subtotal }},
        vat: {{ $sale->vat_total }},
        discount: {{ $sale->discount_total }},
        total: {{ $sale->grand_total }},
        paymentMethod: @json($sale->payment_method_label),
        footer: 'Teşekkür ederiz!\nİyi günler dileriz.'
    };

    try {
        // Önce kayıtlı yazıcıyı dene
        const connId = await window.hw.connectUSB('receipt_printer');
        await window.hw.printReceipt(receipt, connId);
    } catch(e) {
        if (e.name !== 'NotFoundError') {
            console.error('Fiş yazdırma hatası:', e);
        }
        // Tarayıcı yazdırma fallback
        window.print();
    }
}

// Kasa çekmecesi açma
async function openDrawer() {
    if (!window.hw) { alert('Donanım sürücüleri yüklenmedi.'); return; }

    try {
        const connId = await window.hw.connectUSB('receipt_printer');
        await window.hw.openCashDrawer(2, connId);
    } catch(e) {
        if (e.name !== 'NotFoundError') {
            alert('Çekmece açma hatası: ' + e.message);
        }
    }
}
</script>
@endpush
@endsection
