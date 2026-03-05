@extends('layouts.app')
@section('title', 'Varyantlı Ürün Ekle')

@section('content')
<div class="space-y-6" x-data="variantForm()">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Varyantlı Ürün Ekle</h2>
            <p class="text-sm text-gray-500">Bir ana ürün seçerek varyantlarını tanımlayın (renk, beden, boyut vb.)</p>
        </div>
        <a href="{{ route('products.variants') }}" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-200">
            <i class="fas fa-arrow-left mr-1"></i> Varyant Listesi
        </a>
    </div>

    <form action="{{ route('products.store_variant') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Ana Ürün Seçimi --}}
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="font-semibold text-gray-800 mb-4"><i class="fas fa-box mr-2 text-blue-500"></i>Ana Ürün Seçimi</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ana Ürün <span class="text-red-500">*</span></label>
                    <select name="parent_id" required class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border @error('parent_id') border-red-500 @enderror">
                        <option value="">Ana ürün seçin...</option>
                        @foreach($parentProducts as $parent)
                            <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                {{ $parent->name }} {{ $parent->barcode ? '('.$parent->barcode.')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('parent_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Varyant Tipi <span class="text-red-500">*</span></label>
                    <div class="flex gap-2">
                        <input type="text" name="variant_type" value="{{ old('variant_type') }}" required
                            list="variant-types-list"
                            placeholder="Renk, Beden, Boyut..."
                            class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border @error('variant_type') border-red-500 @enderror">
                        <datalist id="variant-types-list">
                            @foreach($variantTypes as $type)
                                <option value="{{ $type }}">
                            @endforeach
                            <option value="Renk">
                            <option value="Beden">
                            <option value="Boyut">
                            <option value="Ağırlık">
                            <option value="Hacim">
                        </datalist>
                    </div>
                    @error('variant_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Varyant Listesi --}}
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800"><i class="fas fa-layer-group mr-2 text-purple-500"></i>Varyantlar</h3>
                <button type="button" @click="addVariant()" class="bg-green-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-green-700">
                    <i class="fas fa-plus mr-1"></i> Varyant Ekle
                </button>
            </div>

            @error('variants') <p class="text-red-500 text-sm mb-3">{{ $message }}</p> @enderror

            <div class="space-y-3">
                <template x-for="(variant, index) in variants" :key="index">
                    <div class="border rounded-lg p-4 bg-gray-50 relative">
                        <button type="button" @click="removeVariant(index)" x-show="variants.length > 1"
                            class="absolute top-2 right-2 text-red-400 hover:text-red-600">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Varyant Adı *</label>
                                <input type="text" :name="'variants['+index+'][name]'" x-model="variant.name" required
                                    placeholder="Örn: Kırmızı - L"
                                    class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Barkod</label>
                                <input type="text" :name="'variants['+index+'][barcode]'" x-model="variant.barcode"
                                    placeholder="Barkod"
                                    class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Satış Fiyatı (₺)</label>
                                <input type="number" step="0.01" :name="'variants['+index+'][sale_price]'" x-model="variant.sale_price"
                                    placeholder="0.00"
                                    class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Stok</label>
                                <input type="number" step="0.01" :name="'variants['+index+'][stock_quantity]'" x-model="variant.stock_quantity"
                                    placeholder="0"
                                    class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <div class="mt-3 text-sm text-gray-400">
                <i class="fas fa-info-circle mr-1"></i>
                Belirtilmeyen fiyatlar ve stok değerleri ana üründen alınacaktır.
            </div>
        </div>

        {{-- Kaydet --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('products.variants') }}" class="bg-gray-100 text-gray-600 px-6 py-2.5 rounded-lg text-sm hover:bg-gray-200">
                İptal
            </a>
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg text-sm hover:bg-indigo-700 font-medium">
                <i class="fas fa-save mr-1"></i> Varyantları Kaydet
            </button>
        </div>
    </form>
</div>

<script>
function variantForm() {
    return {
        variants: [{ name: '', barcode: '', sale_price: '', purchase_price: '', stock_quantity: '' }],
        addVariant() {
            this.variants.push({ name: '', barcode: '', sale_price: '', purchase_price: '', stock_quantity: '' });
        },
        removeVariant(index) {
            this.variants.splice(index, 1);
        }
    }
}
</script>
@endsection
