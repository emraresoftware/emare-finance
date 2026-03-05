@extends('layouts.app')
@section('title', 'Ürün Düzenle')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Ürün Düzenle: {{ $product->name }}</h2>
            <p class="text-sm text-gray-500">Ürün bilgilerini güncelleyin.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('products.show', $product) }}" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-200">
                <i class="fas fa-eye mr-1"></i> Detay
            </a>
            <a href="{{ route('products.index') }}" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-200">
                <i class="fas fa-arrow-left mr-1"></i> Liste
            </a>
        </div>
    </div>

    <form action="{{ route('products.update', $product) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Temel Bilgiler --}}
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="font-semibold text-gray-800 mb-4"><i class="fas fa-info-circle mr-2 text-blue-500"></i>Temel Bilgiler</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ürün Adı <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border @error('name') border-red-500 @enderror">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Barkod</label>
                    <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border @error('barcode') border-red-500 @enderror">
                    @error('barcode') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select name="category_id" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                        <option value="">Kategori Seçin</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hizmet Kategorisi</label>
                    <select name="service_category_id" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                        <option value="">Hizmet Kategorisi Seçin</option>
                        @foreach($serviceCategories as $sc)
                            <option value="{{ $sc->id }}" {{ old('service_category_id', $product->service_category_id) == $sc->id ? 'selected' : '' }}>{{ $sc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Birim</label>
                    <select name="unit" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                        @foreach(['Adet', 'Kg', 'Gram', 'Litre', 'Metre', 'Paket', 'Kutu', 'Porsiyon'] as $unit)
                            <option value="{{ $unit }}" {{ old('unit', $product->unit) == $unit ? 'selected' : '' }}>{{ $unit }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
                    <textarea name="description" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">{{ old('description', $product->description) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Fiyat Bilgileri --}}
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="font-semibold text-gray-800 mb-4"><i class="fas fa-turkish-lira-sign mr-2 text-green-500"></i>Fiyat Bilgileri</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alış Fiyatı (₺)</label>
                    <input type="number" step="0.01" name="purchase_price" value="{{ old('purchase_price', $product->purchase_price) }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Satış Fiyatı (₺)</label>
                    <input type="number" step="0.01" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">KDV Oranı (%)</label>
                    <select name="vat_rate" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                        @foreach([0, 1, 8, 10, 18, 20] as $rate)
                            <option value="{{ $rate }}" {{ old('vat_rate', $product->vat_rate) == $rate ? 'selected' : '' }}>%{{ $rate }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            {{-- Ek Vergiler --}}
            <div class="mt-4">
                <h4 class="text-sm font-semibold text-gray-700 mb-3"><i class="fas fa-percent mr-1 text-orange-500"></i>Ek Vergiler (ÖTV, ÖİV vb.)</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @php
                        $taxGroups = \App\Models\TaxRate::where('is_active', true)->where('code', '!=', 'KDV')->orderBy('code')->orderBy('rate')->get()->groupBy('code');
                        $currentTaxes = old('additional_taxes', collect($product->additional_taxes ?? [])->pluck('tax_rate_id', 'code')->toArray());
                    @endphp
                    @foreach($taxGroups as $code => $rates)
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">{{ $code }}</label>
                            <select name="additional_taxes[{{ $code }}]" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                                <option value="">{{ $code }} Seçin</option>
                                @foreach($rates as $rate)
                                    <option value="{{ $rate->id }}" {{ (isset($currentTaxes[$code]) && $currentTaxes[$code] == $rate->id) ? 'selected' : '' }}>
                                        {{ $rate->name }} - {{ $rate->type === 'percentage' ? '%' . $rate->rate : '₺' . number_format($rate->rate, 2) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Kâr Marjı Bilgisi --}}
            <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center gap-4 text-sm">
                    <span class="text-gray-500">Mevcut Kâr Marjı:</span>
                    <span class="font-bold {{ $product->profit_margin > 0 ? 'text-green-600' : 'text-red-600' }}">
                        %{{ $product->profit_margin }}
                    </span>
                    <span class="text-gray-400">|</span>
                    <span class="text-gray-500">Kâr: ₺{{ number_format($product->sale_price - $product->purchase_price, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Stok Bilgileri --}}
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="font-semibold text-gray-800 mb-4"><i class="fas fa-warehouse mr-2 text-purple-500"></i>Stok Bilgileri</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stok Miktarı</label>
                    <input type="number" step="0.01" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kritik Stok Seviyesi</label>
                    <input type="number" step="0.01" name="critical_stock" value="{{ old('critical_stock', $product->critical_stock) }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Görsel URL</label>
                    <input type="url" name="image_url" value="{{ old('image_url', $product->image_url) }}" placeholder="https://..."
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
            </div>
        </div>

        {{-- Durum --}}
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-gray-800"><i class="fas fa-toggle-on mr-2 text-indigo-500"></i>Durum</h3>
                    <p class="text-sm text-gray-500 mt-1">Ürünün satışa açık olup olmadığını belirleyin.</p>
                </div>
                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-indigo-600 w-5 h-5">
                        <span class="text-sm font-medium text-gray-700">Aktif</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_service" value="1" {{ old('is_service', $product->is_service) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-teal-600 w-5 h-5">
                        <span class="text-sm font-medium text-gray-700">Hizmet Ürünü</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Kaydet --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('products.show', $product) }}" class="bg-gray-100 text-gray-600 px-6 py-2.5 rounded-lg text-sm hover:bg-gray-200">
                İptal
            </a>
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg text-sm hover:bg-indigo-700 font-medium">
                <i class="fas fa-save mr-1"></i> Güncelle
            </button>
        </div>
    </form>
</div>
@endsection
