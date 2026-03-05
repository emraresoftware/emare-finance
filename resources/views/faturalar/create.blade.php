@extends('layouts.app')
@section('title', $documentType === 'irsaliye' ? 'Yeni İrsaliye Oluştur' : 'Yeni Fatura Kes')

@section('content')
<div x-data="faturaForm()" class="space-y-6">
    {{-- Başlık --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900" x-text="docType === 'irsaliye' ? 'Yeni İrsaliye Oluştur' : 'Yeni Fatura Kes'"></h1>
            <p class="text-sm text-gray-500 mt-1" x-text="docType === 'irsaliye' ? 'Yeni sevk irsaliyesi taslağı oluşturun' : 'Yeni fatura taslağı oluşturun'"></p>
        </div>
        <a href="{{ route('faturalar.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-1"></i> Fatura Paneli
        </a>
    </div>

    <form method="POST" action="{{ route('faturalar.store') }}">
        @csrf

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-4">
            <div class="flex items-center text-red-700">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span class="font-medium">Lütfen hataları düzeltin:</span>
            </div>
            <ul class="mt-2 ml-6 list-disc text-sm text-red-600">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Belge Türü Seçici --}}
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-file-alt text-indigo-500 mr-2"></i>Belge Türü
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <label class="relative cursor-pointer">
                    <input type="radio" name="document_type" value="fatura" x-model="docType" @change="onDocTypeChange()" class="sr-only peer">
                    <div class="border-2 rounded-xl p-5 transition peer-checked:border-indigo-500 peer-checked:bg-indigo-50 hover:bg-gray-50">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center">
                                <i class="fas fa-file-invoice text-indigo-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">e-Fatura</p>
                                <p class="text-sm text-gray-500">Temel / Ticari / İhracat</p>
                            </div>
                        </div>
                    </div>
                </label>
                <label class="relative cursor-pointer">
                    <input type="radio" name="document_type" value="fatura" x-model="docType" @click="docType = 'earsiv'; onDocTypeChange()" class="sr-only peer" :checked="docType === 'earsiv'">
                    <div class="border-2 rounded-xl p-5 transition hover:bg-gray-50 cursor-pointer"
                         :class="docType === 'earsiv' ? 'border-amber-500 bg-amber-50' : ''"
                         @click="docType = 'earsiv'; onDocTypeChange()">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center">
                                <i class="fas fa-archive text-amber-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">e-Arşiv Fatura</p>
                                <p class="text-sm text-gray-500">Bireysel / Kayıtsız firma</p>
                            </div>
                        </div>
                    </div>
                </label>
                <label class="relative cursor-pointer">
                    <input type="radio" name="document_type" value="irsaliye" x-model="docType" @change="onDocTypeChange()" class="sr-only peer">
                    <div class="border-2 rounded-xl p-5 transition peer-checked:border-emerald-500 peer-checked:bg-emerald-50 hover:bg-gray-50">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center">
                                <i class="fas fa-truck text-emerald-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">İrsaliye</p>
                                <p class="text-sm text-gray-500">Sevk irsaliyesi (mal taşıma)</p>
                            </div>
                        </div>
                    </div>
                </label>
            </div>
            {{-- Hidden input: gerçek document_type (e-arsiv = fatura) --}}
            <input type="hidden" name="document_type" :value="docType === 'earsiv' ? 'fatura' : docType">
        </div>

        {{-- Fatura / İrsaliye Bilgileri --}}
        <div class="bg-white rounded-xl shadow-sm border p-6 mt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-info-circle text-indigo-500 mr-2"></i>
                <span x-text="docType === 'irsaliye' ? 'İrsaliye Bilgileri' : 'Fatura Bilgileri'"></span>
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Yön <span class="text-red-500">*</span></label>
                    <select name="direction" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                        <option value="outgoing" selected>Giden</option>
                        <option value="incoming">Gelen</option>
                    </select>
                </div>
                <div x-show="docType === 'fatura'">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fatura Türü <span class="text-red-500">*</span></label>
                    <select name="type" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                        <option value="invoice">Satış Faturası</option>
                        <option value="return">İade Faturası</option>
                        <option value="withholding">Tevkifatlı Fatura</option>
                        <option value="exception">İstisna Faturası</option>
                        <option value="special">Özel Matrah Faturası</option>
                    </select>
                </div>
                <div x-show="docType === 'earsiv'" style="display:none">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fatura Türü</label>
                    <select class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border bg-amber-50">
                        <option value="invoice">e-Arşiv Satış Faturası</option>
                    </select>
                    <input type="hidden" name="type" :value="docType === 'earsiv' ? 'invoice' : ''" x-bind:disabled="docType !== 'earsiv'">
                </div>
                <div x-show="docType === 'irsaliye'" style="display:none">
                    <label class="block text-sm font-medium text-gray-700 mb-1">İrsaliye Türü</label>
                    <select name="" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border bg-gray-50">
                        <option value="waybill">Sevk İrsaliyesi</option>
                    </select>
                    <input type="hidden" name="type" :value="docType === 'irsaliye' ? 'waybill' : ''" x-bind:disabled="docType !== 'irsaliye'">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Senaryo <span class="text-red-500">*</span></label>
                    <select name="scenario" x-model="scenarioVal" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border" :class="docType === 'earsiv' ? 'bg-amber-50' : ''">
                        <template x-if="docType === 'earsiv'">
                            <option value="e_arsiv" selected>e-Arşiv Fatura</option>
                        </template>
                        <template x-if="docType !== 'earsiv'">
                            <option value="basic" {{ ($settings->default_scenario ?? '') === 'basic' ? 'selected' : '' }}>Temel</option>
                        </template>
                        <template x-if="docType !== 'earsiv'">
                            <option value="commercial" {{ ($settings->default_scenario ?? '') === 'commercial' ? 'selected' : '' }}>Ticari</option>
                        </template>
                        <template x-if="docType !== 'earsiv'">
                            <option value="export">İhracat</option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <span x-text="docType === 'irsaliye' ? 'İrsaliye Tarihi' : 'Fatura Tarihi'"></span>
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="invoice_date" value="{{ old('invoice_date', date('Y-m-d')) }}" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Para Birimi</label>
                    <select name="currency" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                        <option value="TRY">TRY - Türk Lirası</option>
                        <option value="USD">USD - Amerikan Doları</option>
                        <option value="EUR">EUR - Euro</option>
                        <option value="GBP">GBP - İngiliz Sterlini</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ödeme Yöntemi</label>
                    <select name="payment_method" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                        <option value="">Seçiniz</option>
                        <option value="cash">Nakit</option>
                        <option value="card">Kredi Kartı</option>
                        <option value="transfer">Havale / EFT</option>
                        <option value="check">Çek</option>
                        <option value="other">Diğer</option>
                    </select>
                </div>
            </div>

            @if($sale)
            <input type="hidden" name="sale_id" value="{{ $sale->id }}">
            <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-700">
                <i class="fas fa-info-circle mr-1"></i>
                Bu belge <strong>{{ $sale->receipt_no }}</strong> numaralı satıştan oluşturuluyor.
            </div>
            @endif
        </div>

        {{-- Sevkiyat Bilgileri (İrsaliye) --}}
        <div x-show="docType === 'irsaliye'" x-transition class="bg-white rounded-xl shadow-sm border p-6 mt-6" style="display:none">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-shipping-fast text-emerald-500 mr-2"></i>Sevkiyat Bilgileri
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">İrsaliye No</label>
                    <input type="text" name="waybill_no" value="{{ old('waybill_no') }}" placeholder="Otomatik veya manuel" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sevk Tarihi</label>
                    <input type="date" name="shipment_date" value="{{ old('shipment_date', date('Y-m-d')) }}" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kargo / Nakliye Firması</label>
                    <input type="text" name="shipping_company" value="{{ old('shipping_company') }}" placeholder="Ör: Aras Kargo" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teslimat Adresi</label>
                    <input type="text" name="delivery_address" value="{{ old('delivery_address') }}" placeholder="Malın teslim edileceği adres" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Araç Plakası</label>
                    <input type="text" name="vehicle_plate" value="{{ old('vehicle_plate') }}" placeholder="Ör: 34 ABC 123" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border uppercase">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Şoför Adı</label>
                    <input type="text" name="driver_name" value="{{ old('driver_name') }}" placeholder="Taşıyıcı şoförün adı" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Şoför TC No</label>
                    <input type="text" name="driver_tc" value="{{ old('driver_tc') }}" placeholder="11 haneli TC kimlik no" maxlength="11" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Takip Numarası</label>
                    <input type="text" name="tracking_no" value="{{ old('tracking_no') }}" placeholder="Kargo takip numarası" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
            </div>
        </div>

        {{-- Alıcı Bilgileri --}}
        <div class="bg-white rounded-xl shadow-sm border p-6 mt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-user text-green-500 mr-2"></i>Alıcı Bilgileri
            </h3>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Kayıtlı Müşteri Seç</label>
                <select name="customer_id" x-model="selectedCustomer" @change="fillCustomer()" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                    <option value="">-- Manuel giriş yapın veya müşteri seçin --</option>
                    @foreach($customers as $customer)
                    <option value="{{ $customer->id }}"
                            data-name="{{ $customer->name }}"
                            data-tax="{{ $customer->tax_number }}"
                            data-office="{{ $customer->tax_office }}"
                            data-address="{{ $customer->address }} {{ $customer->district }} {{ $customer->city }}"
                            {{ $sale && $sale->customer_id == $customer->id ? 'selected' : '' }}>
                        {{ $customer->name }} {{ $customer->tax_number ? '(' . $customer->tax_number . ')' : '' }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alıcı Adı / Ünvanı <span class="text-red-500">*</span></label>
                    <input type="text" name="receiver_name" x-model="receiverName" value="{{ old('receiver_name', $sale?->customer?->name) }}" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vergi / TC Kimlik No</label>
                    <input type="text" name="receiver_tax_number" x-model="receiverTax" value="{{ old('receiver_tax_number', $sale?->customer?->tax_number) }}" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vergi Dairesi</label>
                    <input type="text" name="receiver_tax_office" x-model="receiverOffice" value="{{ old('receiver_tax_office', $sale?->customer?->tax_office) }}" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Adres</label>
                    <input type="text" name="receiver_address" x-model="receiverAddress" value="{{ old('receiver_address') }}" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
            </div>
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Şube</label>
                <select name="branch_id" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                    <option value="">Tüm Şubeler</option>
                    @foreach($branches as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Kalem Bilgileri --}}
        <div class="bg-white rounded-xl shadow-sm border p-6 mt-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-list text-purple-500 mr-2"></i>
                    <span x-text="docType === 'irsaliye' ? 'İrsaliye Kalemleri' : 'Fatura Kalemleri'"></span>
                </h3>
                <button type="button" @click="addItem()" class="bg-indigo-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-indigo-700">
                    <i class="fas fa-plus mr-1"></i> Kalem Ekle
                </button>
            </div>

            {{-- Hızlı Ürün Ekle --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Hızlı Ürün Ekle</label>
                <select @change="addProductItem($event)" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                    <option value="">-- Ürün seçerek hızlıca ekleyin --</option>
                    @foreach($products as $product)
                    <option value="{{ $product->id }}"
                            data-name="{{ $product->name }}"
                            data-code="{{ $product->barcode }}"
                            data-unit="{{ $product->unit ?? 'Adet' }}"
                            data-price="{{ $product->sale_price }}"
                            data-vat="{{ $product->vat_rate ?? 20 }}">
                        {{ $product->name }} - ₺{{ number_format($product->sale_price, 2, ',', '.') }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Kalem Tablosu --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-3 py-2 text-left w-8">#</th>
                            <th class="px-3 py-2 text-left">Ürün / Hizmet Adı</th>
                            <th class="px-3 py-2 text-left w-24">Kod</th>
                            <th class="px-3 py-2 text-left w-20">Birim</th>
                            <th class="px-3 py-2 text-right w-20">Miktar</th>
                            <th class="px-3 py-2 text-right w-28">Birim Fiyat</th>
                            <th class="px-3 py-2 text-right w-24">İskonto</th>
                            <th class="px-3 py-2 text-right w-20">KDV %</th>
                            <th class="px-3 py-2 text-right w-28">Toplam</th>
                            <th class="px-3 py-2 w-10"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, index) in items" :key="index">
                            <tr class="border-t">
                                <td class="px-3 py-2 text-gray-400" x-text="index + 1"></td>
                                <td class="px-3 py-2">
                                    <input type="text" :name="'items['+index+'][product_name]'" x-model="item.product_name" class="w-full border-gray-300 rounded text-sm px-2 py-1 border" required>
                                    <input type="hidden" :name="'items['+index+'][product_id]'" x-model="item.product_id">
                                </td>
                                <td class="px-3 py-2">
                                    <input type="text" :name="'items['+index+'][product_code]'" x-model="item.product_code" class="w-full border-gray-300 rounded text-sm px-2 py-1 border">
                                </td>
                                <td class="px-3 py-2">
                                    <select :name="'items['+index+'][unit]'" x-model="item.unit" class="w-full border-gray-300 rounded text-sm px-2 py-1 border">
                                        <option value="Adet">Adet</option>
                                        <option value="Kg">Kg</option>
                                        <option value="Lt">Lt</option>
                                        <option value="Mt">Mt</option>
                                        <option value="Paket">Paket</option>
                                        <option value="Koli">Koli</option>
                                        <option value="Saat">Saat</option>
                                    </select>
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" :name="'items['+index+'][quantity]'" x-model="item.quantity" @input="calcLine(index)" step="0.001" min="0.001" class="w-full border-gray-300 rounded text-sm px-2 py-1 border text-right" required>
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" :name="'items['+index+'][unit_price]'" x-model="item.unit_price" @input="calcLine(index)" step="0.01" min="0" class="w-full border-gray-300 rounded text-sm px-2 py-1 border text-right" required>
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" :name="'items['+index+'][discount]'" x-model="item.discount" @input="calcLine(index)" step="0.01" min="0" class="w-full border-gray-300 rounded text-sm px-2 py-1 border text-right">
                                </td>
                                <td class="px-3 py-2">
                                    <select :name="'items['+index+'][vat_rate]'" x-model="item.vat_rate" @change="calcLine(index)" class="w-full border-gray-300 rounded text-sm px-2 py-1 border text-right">
                                        <option value="0">%0</option>
                                        <option value="1">%1</option>
                                        <option value="10">%10</option>
                                        <option value="20">%20</option>
                                    </select>
                                </td>
                                <td class="px-3 py-2 text-right font-medium" x-text="formatMoney(item.total)"></td>
                                <td class="px-3 py-2">
                                    <button type="button" @click="removeItem(index)" class="text-red-400 hover:text-red-600" x-show="items.length > 1">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            {{-- Toplamlar --}}
            <div class="mt-4 border-t pt-4">
                <div class="flex justify-end">
                    <div class="w-72 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Ara Toplam:</span>
                            <span class="font-medium" x-text="formatMoney(totals.subtotal)"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">İskonto Toplamı:</span>
                            <span class="font-medium text-red-600" x-text="'-' + formatMoney(totals.discount)"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">KDV Toplamı:</span>
                            <span class="font-medium" x-text="formatMoney(totals.vat)"></span>
                        </div>
                        <div class="flex justify-between text-base font-bold border-t pt-2">
                            <span>Genel Toplam:</span>
                            <span class="text-indigo-600" x-text="formatMoney(totals.grand)"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Notlar --}}
        <div class="bg-white rounded-xl shadow-sm border p-6 mt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-sticky-note text-yellow-500 mr-2"></i>Notlar
            </h3>
            <textarea name="notes" rows="3" placeholder="Belge ile ilgili not ekleyin..." class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">{{ old('notes') }}</textarea>
        </div>

        {{-- Kaydet --}}
        <div class="flex items-center justify-end gap-3 mt-6">
            <a href="{{ route('faturalar.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">İptal</a>
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-indigo-700 shadow-sm">
                <i class="fas fa-save mr-2"></i>
                <span x-text="docType === 'irsaliye' ? 'İrsaliye Taslağı Kaydet' : 'Fatura Taslağı Kaydet'"></span>
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function faturaForm() {
    return {
        docType: '{{ $documentType }}',
        selectedCustomer: '{{ $sale?->customer_id }}',
        receiverName: '{{ old("receiver_name", addslashes($sale?->customer?->name ?? "")) }}',
        receiverTax: '{{ old("receiver_tax_number", $sale?->customer?->tax_number ?? "") }}',
        receiverOffice: '{{ old("receiver_tax_office", $sale?->customer?->tax_office ?? "") }}',
        receiverAddress: '{{ old("receiver_address", "") }}',
        items: [
            @if($sale && $sale->items->count())
                @foreach($sale->items as $sItem)
                {
                    product_id: '{{ $sItem->product_id }}',
                    product_name: '{{ addslashes($sItem->product?->name ?? $sItem->product_name ?? "") }}',
                    product_code: '{{ $sItem->product?->barcode ?? "" }}',
                    unit: '{{ $sItem->unit ?? "Adet" }}',
                    quantity: {{ $sItem->quantity }},
                    unit_price: {{ $sItem->unit_price }},
                    discount: {{ $sItem->discount ?? 0 }},
                    vat_rate: {{ $sItem->vat_rate ?? 20 }},
                    vat_amount: 0,
                    total: 0
                },
                @endforeach
            @else
            {
                product_id: '', product_name: '', product_code: '', unit: 'Adet',
                quantity: 1, unit_price: 0, discount: 0, vat_rate: 20,
                vat_amount: 0, total: 0
            }
            @endif
        ],
        totals: { subtotal: 0, discount: 0, vat: 0, grand: 0 },

        init() {
            this.items.forEach((_, i) => this.calcLine(i));
        },

        fillCustomer() {
            const sel = document.querySelector('select[name="customer_id"]');
            const opt = sel.options[sel.selectedIndex];
            if (opt.value) {
                this.receiverName = opt.dataset.name || '';
                this.receiverTax = opt.dataset.tax || '';
                this.receiverOffice = opt.dataset.office || '';
                this.receiverAddress = opt.dataset.address || '';
            }
        },

        addItem() {
            this.items.push({
                product_id: '', product_name: '', product_code: '', unit: 'Adet',
                quantity: 1, unit_price: 0, discount: 0, vat_rate: 20,
                vat_amount: 0, total: 0
            });
        },

        addProductItem(event) {
            const opt = event.target.options[event.target.selectedIndex];
            if (!opt.value) return;
            this.items.push({
                product_id: opt.value,
                product_name: opt.dataset.name || '',
                product_code: opt.dataset.code || '',
                unit: opt.dataset.unit || 'Adet',
                quantity: 1,
                unit_price: parseFloat(opt.dataset.price) || 0,
                discount: 0,
                vat_rate: parseInt(opt.dataset.vat) || 20,
                vat_amount: 0,
                total: 0
            });
            this.calcLine(this.items.length - 1);
            event.target.selectedIndex = 0;
        },

        removeItem(index) {
            this.items.splice(index, 1);
            this.calcTotals();
        },

        calcLine(index) {
            const item = this.items[index];
            const lineSubtotal = item.quantity * item.unit_price;
            const afterDiscount = lineSubtotal - (item.discount || 0);
            item.vat_amount = Math.round(afterDiscount * item.vat_rate / 100 * 100) / 100;
            item.total = Math.round((afterDiscount + item.vat_amount) * 100) / 100;
            this.calcTotals();
        },

        calcTotals() {
            let subtotal = 0, discount = 0, vat = 0;
            this.items.forEach(item => {
                subtotal += item.quantity * item.unit_price;
                discount += parseFloat(item.discount) || 0;
                vat += item.vat_amount || 0;
            });
            this.totals.subtotal = Math.round(subtotal * 100) / 100;
            this.totals.discount = Math.round(discount * 100) / 100;
            this.totals.vat = Math.round(vat * 100) / 100;
            this.totals.grand = Math.round((subtotal - discount + vat) * 100) / 100;
        },

        formatMoney(val) {
            return '₺' + parseFloat(val || 0).toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
    };
}
</script>
@endpush
@endsection
