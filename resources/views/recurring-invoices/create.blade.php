@extends('layouts.app')
@section('title', 'Tekrarlayan Fatura Oluştur')

@section('content')
<div class="space-y-6" x-data="recurringInvoiceForm()">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Yeni Tekrarlayan Fatura</h2>
            <p class="text-sm text-gray-500">Düzenli kesilen fatura tanımı oluşturun.</p>
        </div>
        <a href="{{ route('recurring_invoices.index') }}" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-200">
            <i class="fas fa-arrow-left mr-1"></i> Listeye Dön
        </a>
    </div>

    <form action="{{ route('recurring_invoices.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Genel Bilgiler --}}
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="font-semibold text-gray-800 mb-4"><i class="fas fa-info-circle mr-2 text-blue-500"></i>Genel Bilgiler</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Başlık *</label>
                    <input type="text" name="title" value="{{ old('title') }}" required placeholder="Aylık Danışmanlık Hizmeti"
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                    @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Müşteri</label>
                    <select name="customer_id" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                        <option value="">Müşteri Seçin</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Şube</label>
                    <select name="branch_id" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                        <option value="">Şube Seçin</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ old('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hizmet Kategorisi</label>
                    <select name="service_category_id" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                        <option value="">Kategori Seçin</option>
                        @foreach($serviceCategories as $sc)
                            <option value="{{ $sc->id }}" {{ old('service_category_id') == $sc->id ? 'selected' : '' }}>{{ $sc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ödeme Yöntemi</label>
                    <select name="payment_method" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                        <option value="">Seçin</option>
                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Nakit</option>
                        <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Kart</option>
                        <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>Havale/EFT</option>
                        <option value="credit" {{ old('payment_method') == 'credit' ? 'selected' : '' }}>Veresiye</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
                    <textarea name="description" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Zamanlama --}}
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="font-semibold text-gray-800 mb-4"><i class="fas fa-clock mr-2 text-purple-500"></i>Zamanlama</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sıklık *</label>
                    <select name="frequency" required class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                        <option value="weekly" {{ old('frequency') == 'weekly' ? 'selected' : '' }}>Haftalık</option>
                        <option value="monthly" {{ old('frequency', 'monthly') == 'monthly' ? 'selected' : '' }}>Aylık</option>
                        <option value="bimonthly" {{ old('frequency') == 'bimonthly' ? 'selected' : '' }}>2 Aylık</option>
                        <option value="quarterly" {{ old('frequency') == 'quarterly' ? 'selected' : '' }}>3 Aylık</option>
                        <option value="semiannual" {{ old('frequency') == 'semiannual' ? 'selected' : '' }}>6 Aylık</option>
                        <option value="annual" {{ old('frequency') == 'annual' ? 'selected' : '' }}>Yıllık</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ayın Günü</label>
                    <input type="number" name="frequency_day" value="{{ old('frequency_day', 1) }}" min="1" max="31"
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Başlangıç Tarihi *</label>
                    <input type="date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bitiş Tarihi</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Maks. Fatura Sayısı</label>
                    <input type="number" name="max_invoices" value="{{ old('max_invoices') }}" min="1" placeholder="Sınırsız"
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Para Birimi</label>
                    <select name="currency" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                        <option value="TRY" {{ old('currency', 'TRY') == 'TRY' ? 'selected' : '' }}>₺ TRY</option>
                        <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>$ USD</option>
                        <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>€ EUR</option>
                        <option value="GBP" {{ old('currency') == 'GBP' ? 'selected' : '' }}>£ GBP</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <label class="flex items-center gap-2 pb-2">
                        <input type="checkbox" name="auto_send" value="1" {{ old('auto_send') ? 'checked' : '' }}
                            class="rounded border-gray-300 text-green-600 w-5 h-5">
                        <span class="text-sm text-gray-700">Otomatik Gönderim</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Fatura Kalemleri --}}
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800"><i class="fas fa-list mr-2 text-green-500"></i>Fatura Kalemleri</h3>
                <button type="button" @click="addItem()" class="bg-green-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-green-700">
                    <i class="fas fa-plus mr-1"></i> Kalem Ekle
                </button>
            </div>

            <template x-for="(item, index) in items" :key="index">
                <div class="border rounded-lg p-4 mb-3 bg-gray-50">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-gray-600">Kalem <span x-text="index + 1"></span></span>
                        <button type="button" @click="removeItem(index)" x-show="items.length > 1"
                            class="text-red-500 hover:text-red-700 text-sm">
                            <i class="fas fa-times"></i> Kaldır
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
                        <div class="md:col-span-2">
                            <label class="block text-xs text-gray-500 mb-1">Ürün/Hizmet Adı *</label>
                            <input type="text" :name="'items['+index+'][product_name]'" x-model="item.product_name" required
                                class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Birim</label>
                            <select :name="'items['+index+'][unit]'" x-model="item.unit"
                                class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                                <option value="Adet">Adet</option>
                                <option value="Saat">Saat</option>
                                <option value="Gün">Gün</option>
                                <option value="Ay">Ay</option>
                                <option value="Kg">Kg</option>
                                <option value="Paket">Paket</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Miktar *</label>
                            <input type="number" step="0.001" :name="'items['+index+'][quantity]'" x-model="item.quantity" required
                                @input="calculateItemTotal(index)"
                                class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Birim Fiyat (₺) *</label>
                            <input type="number" step="0.01" :name="'items['+index+'][unit_price]'" x-model="item.unit_price" required
                                @input="calculateItemTotal(index)"
                                class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">İskonto (₺)</label>
                            <input type="number" step="0.01" :name="'items['+index+'][discount]'" x-model="item.discount"
                                @input="calculateItemTotal(index)"
                                class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                        </div>
                    </div>

                    {{-- Vergi Seçimi --}}
                    <div class="mt-3 border-t pt-3">
                        <label class="block text-xs text-gray-500 mb-2">Vergiler</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach($taxRates as $code => $rates)
                                <div class="flex items-center gap-1">
                                    <select :name="'items['+index+'][taxes][{{ $loop->index }}][tax_rate_id]'"
                                        @change="calculateItemTotal(index)"
                                        class="rounded-lg border-gray-300 shadow-sm text-xs px-2 py-1.5 border">
                                        <option value="">{{ $code }} Seçin</option>
                                        @foreach($rates as $rate)
                                            <option value="{{ $rate->id }}" {{ $code === 'KDV' && $rate->is_default ? 'selected' : '' }}>
                                                {{ $rate->name }} ({{ $rate->type === 'percentage' ? '%'.$rate->rate : '₺'.$rate->rate }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Satır Toplamı --}}
                    <div class="mt-3 text-right">
                        <span class="text-sm text-gray-500">Satır Toplamı:</span>
                        <span class="text-sm font-semibold text-gray-800 ml-2">₺<span x-text="formatMoney(item.lineTotal)">0,00</span></span>
                    </div>
                </div>
            </template>
        </div>

        {{-- Notlar --}}
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="font-semibold text-gray-800 mb-4"><i class="fas fa-sticky-note mr-2 text-yellow-500"></i>Notlar</h3>
            <textarea name="notes" rows="3" placeholder="Fatura ile ilgili notlar..."
                class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">{{ old('notes') }}</textarea>
        </div>

        {{-- Kaydet --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('recurring_invoices.index') }}" class="bg-gray-100 text-gray-600 px-6 py-2.5 rounded-lg text-sm hover:bg-gray-200">İptal</a>
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg text-sm hover:bg-indigo-700 font-medium">
                <i class="fas fa-save mr-1"></i> Kaydet
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function recurringInvoiceForm() {
    return {
        items: [{
            product_name: '',
            unit: 'Adet',
            quantity: 1,
            unit_price: 0,
            discount: 0,
            lineTotal: 0,
        }],
        addItem() {
            this.items.push({
                product_name: '',
                unit: 'Adet',
                quantity: 1,
                unit_price: 0,
                discount: 0,
                lineTotal: 0,
            });
        },
        removeItem(index) {
            this.items.splice(index, 1);
        },
        calculateItemTotal(index) {
            const item = this.items[index];
            const sub = (parseFloat(item.quantity) || 0) * (parseFloat(item.unit_price) || 0);
            const disc = parseFloat(item.discount) || 0;
            item.lineTotal = Math.max(sub - disc, 0);
        },
        formatMoney(val) {
            return (val || 0).toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
    }
}
</script>
@endpush
