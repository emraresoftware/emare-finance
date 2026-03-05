@extends('layouts.app')
@section('title', 'E-Fatura Ayarları')

@section('content')
<div class="space-y-6">
    {{-- Başlık --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">E-Fatura Ayarları</h1>
            <p class="text-sm text-gray-500 mt-1">E-fatura entegrasyon ve firma bilgilerini yapılandırın</p>
        </div>
        <a href="{{ route('einvoices.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-1"></i> E-Faturalar
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center text-green-700">
        <i class="fas fa-check-circle mr-2"></i>
        {{ session('success') }}
    </div>
    @endif

    <form method="POST" action="{{ route('einvoices.settings.update') }}">
        @csrf

        {{-- Durum Kartı --}}
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl flex items-center justify-center {{ $settings->is_active ? 'bg-green-100' : 'bg-gray-100' }}">
                        <i class="fas fa-file-invoice-dollar text-2xl {{ $settings->is_active ? 'text-green-600' : 'text-gray-400' }}"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">E-Fatura Entegrasyonu</h3>
                        <p class="text-sm {{ $settings->is_active ? 'text-green-600' : 'text-gray-500' }}">
                            {{ $settings->is_active ? 'Aktif' : 'Pasif - Henüz yapılandırılmamış' }}
                        </p>
                    </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ $settings->is_active ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                </label>
            </div>
        </div>

        {{-- Firma Bilgileri --}}
        <div class="bg-white rounded-xl shadow-sm border p-6 mt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-building text-indigo-500 mr-2"></i>Firma Bilgileri
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Firma / Şahıs Adı</label>
                    <input type="text" name="company_name" value="{{ old('company_name', $settings->company_name) }}" placeholder="Şirket ünvanı" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vergi / TC Kimlik No</label>
                    <input type="text" name="tax_number" value="{{ old('tax_number', $settings->tax_number) }}" placeholder="10 veya 11 haneli" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vergi Dairesi</label>
                    <input type="text" name="tax_office" value="{{ old('tax_office', $settings->tax_office) }}" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
                    <input type="text" name="phone" value="{{ old('phone', $settings->phone) }}" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">E-posta</label>
                    <input type="email" name="email" value="{{ old('email', $settings->email) }}" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Web Sitesi</label>
                    <input type="text" name="web" value="{{ old('web', $settings->web) }}" placeholder="https://..." class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Adres</label>
                    <input type="text" name="address" value="{{ old('address', $settings->address) }}" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">İlçe</label>
                    <input type="text" name="district" value="{{ old('district', $settings->district) }}" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">İl</label>
                    <input type="text" name="city" value="{{ old('city', $settings->city) }}" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
            </div>
        </div>

        {{-- Entegrasyon Ayarları --}}
        <div class="bg-white rounded-xl shadow-sm border p-6 mt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-plug text-orange-500 mr-2"></i>Entegrasyon Ayarları
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Entegratör</label>
                    <select name="integrator" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                        <option value="">Seçiniz</option>
                        <option value="foriba" {{ $settings->integrator === 'foriba' ? 'selected' : '' }}>Foriba (Fit Solutions)</option>
                        <option value="edm" {{ $settings->integrator === 'edm' ? 'selected' : '' }}>EDM Bilişim</option>
                        <option value="uyumsoft" {{ $settings->integrator === 'uyumsoft' ? 'selected' : '' }}>Uyumsoft</option>
                        <option value="logo" {{ $settings->integrator === 'logo' ? 'selected' : '' }}>Logo İzibiz</option>
                        <option value="parasut" {{ $settings->integrator === 'parasut' ? 'selected' : '' }}>Paraşüt</option>
                        <option value="kolaybi" {{ $settings->integrator === 'kolaybi' ? 'selected' : '' }}>KolayBi</option>
                        <option value="other" {{ $settings->integrator === 'other' ? 'selected' : '' }}>Diğer</option>
                    </select>
                </div>
                <div></div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">API Key</label>
                    <input type="password" name="api_key" placeholder="{{ $settings->api_key ? '••••••••••••' : 'API anahtarınızı girin' }}" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                    @if($settings->api_key)
                    <p class="text-xs text-green-600 mt-1"><i class="fas fa-check-circle"></i> API Key ayarlanmış</p>
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">API Secret</label>
                    <input type="password" name="api_secret" placeholder="{{ $settings->api_secret ? '••••••••••••' : 'API secret girin' }}" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                    @if($settings->api_secret)
                    <p class="text-xs text-green-600 mt-1"><i class="fas fa-check-circle"></i> API Secret ayarlanmış</p>
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gönderici Posta Kutusu (Alias)</label>
                    <input type="text" name="sender_alias" value="{{ old('sender_alias', $settings->sender_alias) }}" placeholder="urn:mail:defaultpk@..." class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alıcı Posta Kutusu (Alias)</label>
                    <input type="text" name="receiver_alias" value="{{ old('receiver_alias', $settings->receiver_alias) }}" placeholder="urn:mail:defaultpk@..." class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
            </div>
        </div>

        {{-- Varsayılan Ayarlar --}}
        <div class="bg-white rounded-xl shadow-sm border p-6 mt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-cog text-gray-500 mr-2"></i>Varsayılan Değerler
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Varsayılan Senaryo</label>
                    <select name="default_scenario" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                        <option value="basic" {{ $settings->default_scenario === 'basic' ? 'selected' : '' }}>Temel Fatura</option>
                        <option value="commercial" {{ $settings->default_scenario === 'commercial' ? 'selected' : '' }}>Ticari Fatura</option>
                        <option value="export" {{ $settings->default_scenario === 'export' ? 'selected' : '' }}>İhracat Faturası</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Varsayılan Para Birimi</label>
                    <select name="default_currency" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                        <option value="TRY" {{ $settings->default_currency === 'TRY' ? 'selected' : '' }}>TRY - Türk Lirası</option>
                        <option value="USD" {{ $settings->default_currency === 'USD' ? 'selected' : '' }}>USD - Amerikan Doları</option>
                        <option value="EUR" {{ $settings->default_currency === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Varsayılan KDV Oranı</label>
                    <select name="default_vat_rate" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                        <option value="0" {{ $settings->default_vat_rate === 0 ? 'selected' : '' }}>%0</option>
                        <option value="1" {{ $settings->default_vat_rate === 1 ? 'selected' : '' }}>%1</option>
                        <option value="10" {{ $settings->default_vat_rate === 10 ? 'selected' : '' }}>%10</option>
                        <option value="20" {{ $settings->default_vat_rate === 20 ? 'selected' : '' }}>%20</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fatura No Ön Eki</label>
                    <input type="text" name="invoice_prefix" value="{{ old('invoice_prefix', $settings->invoice_prefix) }}" placeholder="EMR" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mevcut Sayaç</label>
                    <input type="text" value="{{ $settings->invoice_counter }}" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border bg-gray-50" disabled>
                    <p class="text-xs text-gray-500 mt-1">Sonraki fatura: {{ ($settings->invoice_prefix ?? 'EMR') . str_pad($settings->invoice_counter, 9, '0', STR_PAD_LEFT) }}</p>
                </div>
                <div class="flex items-center pt-6">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="auto_send" value="1" class="rounded border-gray-300 text-indigo-600 mr-2" {{ $settings->auto_send ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700">Otomatik Gönder</span>
                    </label>
                    <p class="text-xs text-gray-500 ml-2">(Taslak oluşturmadan doğrudan gönder)</p>
                </div>
            </div>
        </div>

        {{-- Kaydet --}}
        <div class="flex items-center justify-end gap-3 mt-6">
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-indigo-700 shadow-sm">
                <i class="fas fa-save mr-2"></i> Ayarları Kaydet
            </button>
        </div>
    </form>
</div>
@endsection
