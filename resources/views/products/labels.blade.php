@extends('layouts.app')
@section('title', 'Ürün Etiketi')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <form method="GET" class="flex items-end gap-4 flex-wrap">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Ürün Ara</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ürün adı veya barkod..." class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Etiket Boyutu</label>
                <select name="size" class="rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                    <option value="small" {{ request('size') == 'small' ? 'selected' : '' }}>Küçük (30x20mm)</option>
                    <option value="medium" {{ request('size', 'medium') == 'medium' ? 'selected' : '' }}>Orta (50x30mm)</option>
                    <option value="large" {{ request('size') == 'large' ? 'selected' : '' }}>Büyük (70x40mm)</option>
                </select>
            </div>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">
                <i class="fas fa-search mr-1"></i> Ara
            </button>
            <button type="button" onclick="printToHardware()" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700">
                <i class="fas fa-print mr-1"></i> Etiket Yazıcıya Gönder
            </button>
            <button type="button" onclick="window.print()" class="bg-gray-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-600">
                <i class="fas fa-file-alt mr-1"></i> Tarayıcıdan Yazdır
            </button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="font-semibold text-gray-800"><i class="fas fa-tags mr-2"></i>Ürün Etiketleri</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4" id="label-grid">
                @forelse($products ?? [] as $product)
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-3 text-center hover:border-indigo-400 cursor-pointer label-card"
                     data-name="{{ $product->name }}" data-barcode="{{ $product->barcode }}" data-price="{{ $product->sale_price }}">
                    <div class="text-xs font-bold truncate">{{ $product->name }}</div>
                    <div class="text-lg font-bold text-indigo-600 my-1">₺{{ number_format($product->sale_price, 2, ',', '.') }}</div>
                    <div class="text-xs text-gray-500">{{ $product->barcode ?? 'Barkod yok' }}</div>
                    @if($product->barcode)
                    <div class="mt-2">
                        <svg class="barcode-svg mx-auto" data-barcode="{{ $product->barcode }}"></svg>
                    </div>
                    @else
                    <div class="mt-2 bg-gray-100 rounded p-1">
                        <div class="text-xs text-gray-400">Barkod yok</div>
                    </div>
                    @endif
                </div>
                @empty
                <div class="col-span-full text-center text-gray-400 py-8">
                    <i class="fas fa-tags text-3xl mb-2"></i>
                    <p>Etiket oluşturmak için ürün arayın.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Barkodları oluştur
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.barcode-svg').forEach(function(svg) {
        const barcode = svg.dataset.barcode;
        if (barcode && typeof JsBarcode !== 'undefined') {
            try {
                JsBarcode(svg, barcode, {
                    format: barcode.length === 13 ? 'EAN13' : (barcode.length === 8 ? 'EAN8' : 'CODE128'),
                    width: 1.2,
                    height: 35,
                    fontSize: 10,
                    margin: 2,
                    displayValue: true,
                });
            } catch(e) {
                JsBarcode(svg, barcode, { format: 'CODE128', width: 1, height: 30, fontSize: 9, margin: 2, displayValue: true });
            }
        }
    });
});

// Etiket yazıcıya doğrudan gönder
async function printToHardware() {
    const labels = [];
    document.querySelectorAll('.label-card').forEach(card => {
        labels.push({
            name: card.dataset.name,
            barcode: card.dataset.barcode,
            price: parseFloat(card.dataset.price) || 0,
        });
    });

    if (labels.length === 0) {
        alert('Yazdırılacak etiket bulunamadı. Önce ürün arayın.');
        return;
    }

    try {
        await window.hw.connectUSB('label_printer');
        await window.hw.printLabels(labels);
    } catch(e) {
        if (e.name === 'NotFoundError') return;
        // USB başarısızsa tarayıcı yazdırma
        if (confirm('Etiket yazıcı bulunamadı. Tarayıcıdan yazdırmak ister misiniz?')) {
            window.print();
        }
    }
}
</script>
@endpush

<style>
@media print {
    .space-y-6 > :first-child { display: none !important; }
    .border-b, .px-6.py-4 { display: none !important; }
    #label-grid { grid-template-columns: repeat(4, 1fr) !important; gap: 0.5rem !important; }
    .label-card { border-style: solid !important; border-width: 1px !important; page-break-inside: avoid; }
}
</style>
@endsection
