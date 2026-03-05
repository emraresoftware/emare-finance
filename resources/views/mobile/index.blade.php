@extends('layouts.app')

@section('title', 'Mobil İşlemler')

@section('content')
<div class="max-w-lg mx-auto px-4 py-6">
    {{-- Başlık --}}
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg">
            <i class="fas fa-mobile-alt text-white text-2xl"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">Mobil İşlemler</h1>
        <p class="text-gray-500 text-sm mt-1">Hızlı ürün ekleme ve sipariş oluşturma</p>
    </div>

    {{-- İstatistik Kartları --}}
    <div class="grid grid-cols-2 gap-3 mb-8">
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="text-2xl font-bold text-indigo-600">{{ number_format($stats['total_products']) }}</div>
            <div class="text-xs text-gray-500 mt-1">Toplam Ürün</div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="text-2xl font-bold text-red-500">{{ number_format($stats['low_stock']) }}</div>
            <div class="text-xs text-gray-500 mt-1">Düşük Stok</div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="text-2xl font-bold text-green-600">{{ number_format($stats['today_sales']) }}</div>
            <div class="text-xs text-gray-500 mt-1">Bugün Satış</div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="text-2xl font-bold text-yellow-600">{{ number_format($stats['today_revenue'], 2, ',', '.') }}₺</div>
            <div class="text-xs text-gray-500 mt-1">Bugün Ciro</div>
        </div>
    </div>

    {{-- Ana Menü Butonları --}}
    <div class="space-y-4">
        {{-- Kameradan Ürün Ekle --}}
        <a href="{{ route('mobile.camera-add') }}" class="flex items-center bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-2xl p-5 shadow-md hover:shadow-lg transition-all active:scale-[0.98]">
            <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center mr-4 flex-shrink-0">
                <i class="fas fa-camera text-2xl"></i>
            </div>
            <div>
                <div class="font-bold text-lg">Kameradan Ürün Ekle</div>
                <div class="text-blue-100 text-sm">Fotoğraf çekerek hızlıca ürün ekleyin</div>
            </div>
            <i class="fas fa-chevron-right ml-auto opacity-50"></i>
        </a>

        {{-- Barkod Tara --}}
        <a href="{{ route('mobile.barcode-scan') }}" class="flex items-center bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-2xl p-5 shadow-md hover:shadow-lg transition-all active:scale-[0.98]">
            <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center mr-4 flex-shrink-0">
                <i class="fas fa-barcode text-2xl"></i>
            </div>
            <div>
                <div class="font-bold text-lg">Barkod Tara</div>
                <div class="text-purple-100 text-sm">Barkod okuyarak ürün bul veya ekle</div>
            </div>
            <i class="fas fa-chevron-right ml-auto opacity-50"></i>
        </a>

        {{-- Hızlı Sipariş --}}
        <a href="{{ route('mobile.quick-order') }}" class="flex items-center bg-gradient-to-r from-green-500 to-green-600 text-white rounded-2xl p-5 shadow-md hover:shadow-lg transition-all active:scale-[0.98]">
            <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center mr-4 flex-shrink-0">
                <i class="fas fa-cart-plus text-2xl"></i>
            </div>
            <div>
                <div class="font-bold text-lg">Hızlı Sipariş</div>
                <div class="text-green-100 text-sm">Ürün seçip anında sipariş oluşturun</div>
            </div>
            <i class="fas fa-chevron-right ml-auto opacity-50"></i>
        </a>

        {{-- Ürün Listesi --}}
        <a href="{{ route('products.index') }}" class="flex items-center bg-gradient-to-r from-gray-600 to-gray-700 text-white rounded-2xl p-5 shadow-md hover:shadow-lg transition-all active:scale-[0.98]">
            <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center mr-4 flex-shrink-0">
                <i class="fas fa-boxes-stacked text-2xl"></i>
            </div>
            <div>
                <div class="font-bold text-lg">Ürün Listesi</div>
                <div class="text-gray-300 text-sm">Tüm ürünleri görüntüle ve yönet</div>
            </div>
            <i class="fas fa-chevron-right ml-auto opacity-50"></i>
        </a>
    </div>
</div>
@endsection
