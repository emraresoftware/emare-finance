@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="max-w-lg mx-auto px-4 py-4">
    {{-- Üst Bar --}}
    <div class="flex items-center mb-4">
        <a href="javascript:history.back()" class="w-10 h-10 flex items-center justify-center bg-gray-100 rounded-xl mr-3">
            <i class="fas fa-arrow-left text-gray-600"></i>
        </a>
        <h1 class="text-xl font-bold text-gray-900 truncate">{{ $product->name }}</h1>
    </div>

    {{-- Ürün Resmi --}}
    <div class="bg-gray-100 rounded-2xl overflow-hidden mb-4" style="aspect-ratio: 4/3;">
        @if($product->image_url)
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full flex flex-col items-center justify-center text-gray-400">
                <i class="fas fa-box text-5xl mb-2"></i>
                <span class="text-sm">Fotoğraf yok</span>
            </div>
        @endif
    </div>

    {{-- Temel Bilgiler --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-4">
        <div class="p-4">
            <h2 class="text-xl font-bold text-gray-900 mb-1">{{ $product->name }}</h2>
            @if($product->barcode)
                <div class="flex items-center text-gray-500 text-sm">
                    <i class="fas fa-barcode mr-2"></i>
                    <span>{{ $product->barcode }}</span>
                </div>
            @endif
        </div>

        <div class="border-t border-gray-100 px-4 py-3 flex items-center justify-between">
            <div>
                <div class="text-sm text-gray-500">Satış Fiyatı</div>
                <div class="text-2xl font-bold text-blue-600">{{ number_format($product->sale_price, 2, ',', '.') }} ₺</div>
            </div>
            <div class="text-right">
                <div class="text-sm text-gray-500">Stok</div>
                <div class="text-2xl font-bold {{ $product->stock_quantity > ($product->critical_stock ?? 5) ? 'text-green-600' : ($product->stock_quantity > 0 ? 'text-yellow-600' : 'text-red-600') }}">
                    {{ $product->stock_quantity }} <span class="text-sm font-normal">{{ $product->unit ?? 'Adet' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Detaylar --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-4">
        <div class="px-4 py-3 border-b border-gray-100">
            <h3 class="font-bold text-gray-700"><i class="fas fa-info-circle mr-1 text-gray-400"></i> Detaylar</h3>
        </div>
        <div class="divide-y divide-gray-50">
            @if($product->category)
                <div class="flex justify-between px-4 py-3">
                    <span class="text-gray-500 text-sm">Kategori</span>
                    <span class="font-medium text-gray-900 text-sm">{{ $product->category->name }}</span>
                </div>
            @endif
            <div class="flex justify-between px-4 py-3">
                <span class="text-gray-500 text-sm">Alış Fiyatı</span>
                <span class="font-medium text-gray-900 text-sm">{{ number_format($product->purchase_price ?? 0, 2, ',', '.') }} ₺</span>
            </div>
            <div class="flex justify-between px-4 py-3">
                <span class="text-gray-500 text-sm">KDV Oranı</span>
                <span class="font-medium text-gray-900 text-sm">%{{ $product->vat_rate ?? 0 }}</span>
            </div>
            <div class="flex justify-between px-4 py-3">
                <span class="text-gray-500 text-sm">Birim</span>
                <span class="font-medium text-gray-900 text-sm">{{ $product->unit ?? 'Adet' }}</span>
            </div>
            @if($product->critical_stock)
                <div class="flex justify-between px-4 py-3">
                    <span class="text-gray-500 text-sm">Kritik Stok</span>
                    <span class="font-medium text-gray-900 text-sm">{{ $product->critical_stock }}</span>
                </div>
            @endif
            <div class="flex justify-between px-4 py-3">
                <span class="text-gray-500 text-sm">Durum</span>
                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $product->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ $product->is_active ? 'Aktif' : 'Pasif' }}
                </span>
            </div>
        </div>
    </div>

    {{-- Kâr Hesaplama --}}
    @if($product->purchase_price && $product->sale_price > 0)
        @php
            $profit = $product->sale_price - $product->purchase_price;
            $profitMargin = ($profit / $product->sale_price) * 100;
        @endphp
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-4">
            <div class="px-4 py-3 border-b border-gray-100">
                <h3 class="font-bold text-gray-700"><i class="fas fa-chart-line mr-1 text-gray-400"></i> Kâr Bilgisi</h3>
            </div>
            <div class="grid grid-cols-2 divide-x divide-gray-100 p-4">
                <div class="text-center">
                    <div class="text-sm text-gray-500 mb-1">Birim Kâr</div>
                    <div class="text-xl font-bold {{ $profit > 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ number_format($profit, 2, ',', '.') }} ₺
                    </div>
                </div>
                <div class="text-center">
                    <div class="text-sm text-gray-500 mb-1">Kâr Marjı</div>
                    <div class="text-xl font-bold {{ $profitMargin > 0 ? 'text-green-600' : 'text-red-600' }}">
                        %{{ number_format($profitMargin, 1, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- İşlem Butonları --}}
    <div class="grid grid-cols-2 gap-3">
        <a href="{{ route('mobile.quick-order') }}"
            onclick="sessionStorage.setItem('mobileCart', JSON.stringify([{id:{{ $product->id }},name:'{{ addslashes($product->name) }}',barcode:'{{ $product->barcode }}',price:{{ $product->sale_price }},vat_rate:{{ $product->vat_rate ?? 0 }},quantity:1}]))"
            class="flex items-center justify-center bg-green-500 text-white py-4 rounded-xl font-bold active:bg-green-600">
            <i class="fas fa-cart-plus mr-2"></i> Siparişe Ekle
        </a>
        <a href="{{ route('products.edit', $product) }}" class="flex items-center justify-center bg-gray-100 text-gray-700 py-4 rounded-xl font-bold active:bg-gray-200">
            <i class="fas fa-pen mr-2"></i> Düzenle
        </a>
    </div>
</div>
@endsection
