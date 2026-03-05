@extends('layouts.app')
@section('title', 'Yeni Kampanya')

@section('content')
<div class="space-y-6">
    {{-- Başlık --}}
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('marketing.campaigns.index') }}" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-arrow-left text-lg"></i>
            </a>
            <div>
                <h1 class="text-lg font-bold text-gray-800">Yeni Kampanya Oluştur</h1>
                <p class="text-sm text-gray-500">İndirim veya promosyon kampanyası tanımlayın</p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('marketing.campaigns.store') }}" x-data="campaignForm()">
        @csrf

        {{-- Temel Bilgiler --}}
        <div class="bg-white rounded-xl shadow-sm border p-6 mb-6">
            <h3 class="text-md font-semibold text-gray-800 mb-4"><i class="fas fa-info-circle text-green-500 mr-2"></i>Kampanya Bilgileri</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kampanya Adı <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Ör: Yaz İndirimi %20"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('name') border-red-500 @enderror">
                    @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
                    <textarea name="description" rows="2" placeholder="Kampanya hakkında kısa açıklama..."
                              class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">{{ old('description') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kampanya Türü <span class="text-red-500">*</span></label>
                    <select name="type" x-model="type" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('type') border-red-500 @enderror">
                        <option value="">Seçin...</option>
                        <option value="discount" {{ old('type') == 'discount' ? 'selected' : '' }}>İndirim</option>
                        <option value="coupon" {{ old('type') == 'coupon' ? 'selected' : '' }}>Kupon</option>
                        <option value="bogo" {{ old('type') == 'bogo' ? 'selected' : '' }}>Al-Öde (BOGO)</option>
                        <option value="free_shipping" {{ old('type') == 'free_shipping' ? 'selected' : '' }}>Ücretsiz Kargo</option>
                    </select>
                    @error('type') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Durum</label>
                    <select name="status" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ old('status', 'inactive') == 'inactive' ? 'selected' : '' }}>Pasif</option>
                        <option value="scheduled" {{ old('status') == 'scheduled' ? 'selected' : '' }}>Planlanmış</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- İndirim Ayarları --}}
        <div class="bg-white rounded-xl shadow-sm border p-6 mb-6">
            <h3 class="text-md font-semibold text-gray-800 mb-4"><i class="fas fa-percent text-green-500 mr-2"></i>İndirim Ayarları</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">İndirim Türü <span class="text-red-500">*</span></label>
                    <select name="discount_type" x-model="discountType" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="percentage" {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>Yüzde (%)</option>
                        <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>Sabit Tutar (₺)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">İndirim Değeri <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="number" name="discount_value" value="{{ old('discount_value') }}" required min="0" step="0.01"
                               class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('discount_value') border-red-500 @enderror"
                               :max="discountType === 'percentage' ? 100 : 999999">
                        <span class="absolute right-3 top-2 text-sm text-gray-400" x-text="discountType === 'percentage' ? '%' : '₺'"></span>
                    </div>
                    @error('discount_value') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div x-show="type === 'coupon'">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kupon Kodu</label>
                    <input type="text" name="coupon_code" value="{{ old('coupon_code') }}" placeholder="Ör: YAZ2026"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 uppercase font-mono">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kullanım Limiti</label>
                    <input type="number" name="usage_limit" value="{{ old('usage_limit') }}" min="0" placeholder="Sınırsız için boş bırakın"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        {{-- Tarih & Hedefleme --}}
        <div class="bg-white rounded-xl shadow-sm border p-6 mb-6">
            <h3 class="text-md font-semibold text-gray-800 mb-4"><i class="fas fa-calendar text-green-500 mr-2"></i>Tarih & Hedefleme</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Başlangıç Tarihi</label>
                    <input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bitiş Tarihi</label>
                    <input type="datetime-local" name="ends_at" value="{{ old('ends_at') }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hedef Segment</label>
                    <select name="segment_id" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="">Tüm müşteriler</option>
                        @foreach($segments as $segment)
                            <option value="{{ $segment->id }}" {{ old('segment_id') == $segment->id ? 'selected' : '' }}>
                                {{ $segment->name }} ({{ $segment->customer_count }} müşteri)
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Hedef Ürünler --}}
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Hedef Ürünler (isteğe bağlı)</label>
                <div class="max-h-48 overflow-y-auto border rounded-lg p-3 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                    @foreach($products as $product)
                        <label class="flex items-center gap-2 text-sm p-1 rounded hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" name="product_ids[]" value="{{ $product->id }}" {{ in_array($product->id, old('product_ids', [])) ? 'checked' : '' }}
                                   class="rounded text-indigo-600 focus:ring-indigo-500">
                            <span class="truncate">{{ $product->name }}</span>
                        </label>
                    @endforeach
                </div>
                <p class="text-xs text-gray-400 mt-1">Hiçbiri seçilmezse tüm ürünlere uygulanır</p>
            </div>
        </div>

        {{-- Kaydet --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('marketing.campaigns.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-lg text-sm hover:bg-gray-200 font-medium">İptal</a>
            <button type="submit" class="px-6 py-2.5 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700 font-medium">
                <i class="fas fa-save mr-1"></i> Kampanya Oluştur
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function campaignForm() {
    return {
        type: '{{ old('type', '') }}',
        discountType: '{{ old('discount_type', 'percentage') }}'
    }
}
</script>
@endpush
@endsection
