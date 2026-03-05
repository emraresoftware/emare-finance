@extends('layouts.app')
@section('title', 'Ürün Ekle')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Yeni Ürün Ekle</h2>
            <p class="text-sm text-gray-500">Yeni bir ürün oluşturmak için formu doldurun.</p>
        </div>
        <a href="{{ route('products.index') }}" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-200">
            <i class="fas fa-arrow-left mr-1"></i> Ürün Listesi
        </a>
    </div>

    <form action="{{ route('products.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Temel Bilgiler --}}
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="font-semibold text-gray-800 mb-4"><i class="fas fa-info-circle mr-2 text-blue-500"></i>Temel Bilgiler</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ürün Adı <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border @error('name') border-red-500 @enderror">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Barkod</label>
                    <input type="text" name="barcode" value="{{ old('barcode') }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border @error('barcode') border-red-500 @enderror">
                    @error('barcode') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select name="category_id" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                        <option value="">Kategori Seçin</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hizmet Kategorisi</label>
                    <select name="service_category_id" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                        <option value="">Hizmet Kategorisi Seçin</option>
                        @foreach($serviceCategories ?? [] as $sc)
                            <option value="{{ $sc->id }}" {{ old('service_category_id') == $sc->id ? 'selected' : '' }}>{{ $sc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Birim</label>
                    <select name="unit" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                        <option value="Adet" {{ old('unit', 'Adet') == 'Adet' ? 'selected' : '' }}>Adet</option>
                        <option value="Kg" {{ old('unit') == 'Kg' ? 'selected' : '' }}>Kg</option>
                        <option value="Gram" {{ old('unit') == 'Gram' ? 'selected' : '' }}>Gram</option>
                        <option value="Litre" {{ old('unit') == 'Litre' ? 'selected' : '' }}>Litre</option>
                        <option value="Metre" {{ old('unit') == 'Metre' ? 'selected' : '' }}>Metre</option>
                        <option value="Paket" {{ old('unit') == 'Paket' ? 'selected' : '' }}>Paket</option>
                        <option value="Kutu" {{ old('unit') == 'Kutu' ? 'selected' : '' }}>Kutu</option>
                        <option value="Porsiyon" {{ old('unit') == 'Porsiyon' ? 'selected' : '' }}>Porsiyon</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
                    <textarea name="description" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Fiyat Bilgileri --}}
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="font-semibold text-gray-800 mb-4"><i class="fas fa-turkish-lira-sign mr-2 text-green-500"></i>Fiyat Bilgileri</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alış Fiyatı (₺)</label>
                    <input type="number" step="0.01" name="purchase_price" value="{{ old('purchase_price', '0.00') }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Satış Fiyatı (₺)</label>
                    <input type="number" step="0.01" name="sale_price" value="{{ old('sale_price', '0.00') }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">KDV Oranı (%)</label>
                    <select name="vat_rate" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                        <option value="0" {{ old('vat_rate') == '0' ? 'selected' : '' }}>%0</option>
                        <option value="1" {{ old('vat_rate') == '1' ? 'selected' : '' }}>%1</option>
                        <option value="8" {{ old('vat_rate') == '8' ? 'selected' : '' }}>%8</option>
                        <option value="10" {{ old('vat_rate') == '10' ? 'selected' : '' }}>%10</option>
                        <option value="18" {{ old('vat_rate') == '18' ? 'selected' : '' }}>%18</option>
                        <option value="20" {{ old('vat_rate', '20') == '20' ? 'selected' : '' }}>%20</option>
                    </select>
                </div>
            </div>

            {{-- Ek Vergiler --}}
            <div class="mt-4 border-t pt-4">
                <h4 class="text-sm font-medium text-gray-700 mb-2"><i class="fas fa-plus-circle mr-1 text-orange-500"></i>Ek Vergiler (ÖTV, ÖİV vb.)</h4>
                <p class="text-xs text-gray-400 mb-3">Ürüne uygulanacak KDV dışı ek vergileri seçin.</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3" x-data="{ taxes: [] }">
                    @foreach(\App\Models\TaxRate::active()->whereNot('code', 'KDV')->orderBy('sort_order')->orderBy('rate')->get()->groupBy('code') as $code => $rates)
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">{{ $code }}</label>
                        <select name="additional_taxes[{{ $code }}]" class="w-full rounded-lg border-gray-300 shadow-sm text-xs px-2 py-1.5 border">
                            <option value="">{{ $code }} uygulanmasın</option>
                            @foreach($rates as $rate)
                                <option value="{{ $rate->id }}">{{ $rate->name }} ({{ $rate->type === 'percentage' ? '%'.$rate->rate : '₺'.$rate->rate }})</option>
                            @endforeach
                        </select>
                    </div>
                    @endforeach
                </div>
            </div>
            </div>
        </div>

        {{-- Stok Bilgileri --}}
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="font-semibold text-gray-800 mb-4"><i class="fas fa-warehouse mr-2 text-purple-500"></i>Stok Bilgileri</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stok Miktarı</label>
                    <input type="number" step="0.01" name="stock_quantity" value="{{ old('stock_quantity', '0') }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kritik Stok Seviyesi</label>
                    <input type="number" step="0.01" name="critical_stock" value="{{ old('critical_stock', '0') }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Görsel URL</label>
                    <input type="url" name="image_url" value="{{ old('image_url') }}" placeholder="https://..."
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
                        <input type="checkbox" name="is_service" value="1" {{ old('is_service') ? 'checked' : '' }}
                            class="rounded border-gray-300 text-purple-600 w-5 h-5">
                        <span class="text-sm font-medium text-gray-700">Hizmet Ürünü</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-indigo-600 w-5 h-5">
                        <span class="text-sm font-medium text-gray-700">Aktif</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Kaydet --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('products.index') }}" class="bg-gray-100 text-gray-600 px-6 py-2.5 rounded-lg text-sm hover:bg-gray-200">
                İptal
            </a>
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg text-sm hover:bg-indigo-700 font-medium">
                <i class="fas fa-save mr-1"></i> Ürünü Kaydet
            </button>
        </div>
    </form>
</div>
@endsection
