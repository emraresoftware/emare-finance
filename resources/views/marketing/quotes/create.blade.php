@extends('layouts.app')
@section('title', 'Yeni Teklif Oluştur')

@section('content')
<div x-data="quoteForm()" class="space-y-6">
    {{-- Başlık --}}
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('marketing.quotes.index') }}" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>
                <div>
                    <h1 class="text-lg font-bold text-gray-800">Yeni Teklif Oluştur</h1>
                    <p class="text-sm text-gray-500">Müşteriye özel teklif hazırlayın</p>
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('marketing.quotes.store') }}">
        @csrf

        {{-- Teklif Bilgileri --}}
        <div class="bg-white rounded-xl shadow-sm border p-6 mb-6">
            <h3 class="text-md font-semibold text-gray-800 mb-4"><i class="fas fa-info-circle text-indigo-500 mr-2"></i>Teklif Bilgileri</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Müşteri <span class="text-red-500">*</span></label>
                    <select name="customer_id" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('customer_id') border-red-500 @enderror">
                        <option value="">Müşteri seçin...</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                        @endforeach
                    </select>
                    @error('customer_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teklif Başlığı <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required placeholder="Teklif başlığı..."
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('title') border-red-500 @enderror">
                    @error('title') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Düzenleme Tarihi <span class="text-red-500">*</span></label>
                    <input type="date" name="issue_date" value="{{ old('issue_date', date('Y-m-d')) }}" required
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('issue_date') border-red-500 @enderror">
                    @error('issue_date') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Geçerlilik Tarihi</label>
                    <input type="date" name="valid_until" value="{{ old('valid_until') }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notlar</label>
                    <textarea name="notes" rows="2" placeholder="Müşteriye özel notlar..."
                              class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Kalemler --}}
        <div class="bg-white rounded-xl shadow-sm border p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-md font-semibold text-gray-800"><i class="fas fa-list text-indigo-500 mr-2"></i>Teklif Kalemleri</h3>
                <button type="button" @click="addItem()" class="bg-indigo-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-indigo-700">
                    <i class="fas fa-plus mr-1"></i> Kalem Ekle
                </button>
            </div>

            {{-- Ürün Seçici --}}
            <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                <label class="block text-xs text-gray-500 mb-1">Hızlı Ürün Ekle</label>
                <select @change="addProductItem($event.target.value); $event.target.value=''" class="w-full border rounded-lg px-3 py-2 text-sm">
                    <option value="">Ürün listesinden seçin...</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->sale_price }}" data-tax="{{ $product->tax_rate ?? 20 }}" data-unit="{{ $product->unit ?? 'adet' }}">
                            {{ $product->name }} — ₺{{ number_format($product->sale_price, 2, ',', '.') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-1/4">Ürün/Hizmet</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Açıklama</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-20">Miktar</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-20">Birim</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase w-28">Birim Fiyat</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-20">KDV %</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-24">İndirim %</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase w-28">Toplam</th>
                            <th class="px-3 py-2 w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <template x-for="(item, index) in items" :key="index">
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2">
                                    <input type="text" x-model="item.name" :name="'items['+index+'][name]'" required placeholder="Ürün adı"
                                           class="w-full border rounded px-2 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500">
                                </td>
                                <td class="px-3 py-2">
                                    <input type="text" x-model="item.description" :name="'items['+index+'][description]'" placeholder="Açıklama"
                                           class="w-full border rounded px-2 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500">
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" x-model.number="item.quantity" :name="'items['+index+'][quantity]'" min="0.01" step="0.01" required
                                           class="w-full border rounded px-2 py-1.5 text-sm text-center focus:ring-2 focus:ring-indigo-500" @input="calcItem(index)">
                                </td>
                                <td class="px-3 py-2">
                                    <select x-model="item.unit" :name="'items['+index+'][unit]'" class="w-full border rounded px-2 py-1.5 text-sm">
                                        <option value="adet">Adet</option>
                                        <option value="kg">Kg</option>
                                        <option value="lt">Lt</option>
                                        <option value="m">Metre</option>
                                        <option value="saat">Saat</option>
                                        <option value="paket">Paket</option>
                                    </select>
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" x-model.number="item.unit_price" :name="'items['+index+'][unit_price]'" min="0" step="0.01" required
                                           class="w-full border rounded px-2 py-1.5 text-sm text-right focus:ring-2 focus:ring-indigo-500" @input="calcItem(index)">
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" x-model.number="item.tax_rate" :name="'items['+index+'][tax_rate]'" min="0" max="100" step="1"
                                           class="w-full border rounded px-2 py-1.5 text-sm text-center focus:ring-2 focus:ring-indigo-500" @input="calcItem(index)">
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" x-model.number="item.discount_rate" :name="'items['+index+'][discount_rate]'" min="0" max="100" step="0.01"
                                           class="w-full border rounded px-2 py-1.5 text-sm text-center focus:ring-2 focus:ring-indigo-500" @input="calcItem(index)">
                                </td>
                                <td class="px-3 py-2 text-right font-semibold text-gray-800" x-text="formatMoney(item.total)"></td>
                                <td class="px-3 py-2">
                                    <button type="button" @click="removeItem(index)" class="text-red-400 hover:text-red-600">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div x-show="items.length === 0" class="text-center py-8 text-gray-400">
                <i class="fas fa-list text-3xl mb-2"></i>
                <p>Henüz kalem eklenmedi. Yukarıdan ürün seçin veya "Kalem Ekle" butonuna tıklayın.</p>
            </div>

            {{-- Toplamlar --}}
            <div class="mt-4 flex justify-end">
                <div class="w-72 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Ara Toplam:</span>
                        <span class="font-medium" x-text="formatMoney(subtotal())"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Toplam İndirim:</span>
                        <span class="font-medium text-red-600" x-text="'-' + formatMoney(totalDiscount())"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">KDV:</span>
                        <span class="font-medium" x-text="formatMoney(totalTax())"></span>
                    </div>
                    <div class="flex justify-between border-t pt-2">
                        <span class="font-semibold text-gray-800">Genel Toplam:</span>
                        <span class="font-bold text-lg text-indigo-600" x-text="formatMoney(grandTotal())"></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Şartlar & Koşullar --}}
        <div class="bg-white rounded-xl shadow-sm border p-6 mb-6">
            <h3 class="text-md font-semibold text-gray-800 mb-4"><i class="fas fa-gavel text-indigo-500 mr-2"></i>Şartlar & Koşullar</h3>
            <textarea name="terms" rows="3" placeholder="Ödeme koşulları, teslimat bilgileri vb..."
                      class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">{{ old('terms') }}</textarea>
        </div>

        {{-- Kaydet --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('marketing.quotes.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-lg text-sm hover:bg-gray-200 font-medium">İptal</a>
            <button type="submit" name="action" value="draft" class="px-6 py-2.5 bg-gray-700 text-white rounded-lg text-sm hover:bg-gray-800 font-medium">
                <i class="fas fa-save mr-1"></i> Taslak Kaydet
            </button>
            <button type="submit" name="action" value="send" class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700 font-medium">
                <i class="fas fa-paper-plane mr-1"></i> Kaydet & Gönder
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function quoteForm() {
    return {
        items: [],
        addItem() {
            this.items.push({ name: '', description: '', quantity: 1, unit: 'adet', unit_price: 0, tax_rate: 20, discount_rate: 0, total: 0 });
        },
        addProductItem(productId) {
            if (!productId) return;
            const opt = document.querySelector(`select option[value="${productId}"]`);
            if (!opt) return;
            this.items.push({
                name: opt.dataset.name,
                description: '',
                quantity: 1,
                unit: opt.dataset.unit || 'adet',
                unit_price: parseFloat(opt.dataset.price) || 0,
                tax_rate: parseFloat(opt.dataset.tax) || 20,
                discount_rate: 0,
                total: 0
            });
            this.calcItem(this.items.length - 1);
        },
        removeItem(index) {
            this.items.splice(index, 1);
        },
        calcItem(index) {
            const item = this.items[index];
            const base = item.quantity * item.unit_price;
            const discount = base * (item.discount_rate / 100);
            const afterDiscount = base - discount;
            const tax = afterDiscount * (item.tax_rate / 100);
            item.total = afterDiscount + tax;
        },
        subtotal() {
            return this.items.reduce((sum, i) => sum + (i.quantity * i.unit_price), 0);
        },
        totalDiscount() {
            return this.items.reduce((sum, i) => sum + (i.quantity * i.unit_price * (i.discount_rate / 100)), 0);
        },
        totalTax() {
            return this.items.reduce((sum, i) => {
                const base = i.quantity * i.unit_price;
                const afterDiscount = base - (base * (i.discount_rate / 100));
                return sum + (afterDiscount * (i.tax_rate / 100));
            }, 0);
        },
        grandTotal() {
            return this.subtotal() - this.totalDiscount() + this.totalTax();
        },
        formatMoney(val) {
            return '₺' + val.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&.').replace('.', ',').replace(/\.(?=\d{2}$)/, ',').replace(/,(\d{2})$/, ',$1');
        }
    }
}
</script>
@endpush
@endsection
