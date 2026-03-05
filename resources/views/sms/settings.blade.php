@extends('layouts.app')
@section('title', 'SMS Ayarları')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">SMS Ayarları</h1>
        <p class="text-sm text-gray-500 mt-1">SMS sağlayıcı yapılandırması ve test işlemleri</p>
    </div>
    <a href="{{ route('sms.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
        <i class="fas fa-arrow-left mr-2"></i> SMS Paneli
    </a>
</div>

@if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4 text-sm text-green-700">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">
        <i class="fas fa-times-circle mr-2"></i>{{ session('error') }}
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="smsSettings()">
    {{-- Sol: Ayar Formu --}}
    <div class="lg:col-span-2">
        <form action="{{ route('sms.settings.update') }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-100">
            @csrf
            @method('PUT')

            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800">Sağlayıcı Bilgileri</h3>
                <p class="text-sm text-gray-500 mt-1">SMS sağlayıcınızın API bilgilerini girin</p>
            </div>

            <div class="p-6 space-y-5">
                {{-- Sağlayıcı Seçimi --}}
                <div>
                    <label for="provider" class="block text-sm font-medium text-gray-700 mb-1">SMS Sağlayıcı <span class="text-red-500">*</span></label>
                    <select name="provider" id="provider" x-model="provider"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Sağlayıcı Seçin</option>
                        @foreach($providers as $key => $label)
                            <option value="{{ $key }}" {{ old('provider', $settings?->provider) == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('provider') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- API Key --}}
                <div x-show="needsField('api_key')" x-cloak>
                    <label for="api_key" class="block text-sm font-medium text-gray-700 mb-1">API Anahtarı</label>
                    <input type="text" name="api_key" id="api_key" value="{{ old('api_key', $settings?->api_key) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="API anahtarınızı girin">
                    @error('api_key') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- API Secret --}}
                <div x-show="needsField('api_secret')" x-cloak>
                    <label for="api_secret" class="block text-sm font-medium text-gray-700 mb-1">API Secret</label>
                    <input type="password" name="api_secret" id="api_secret" value="{{ old('api_secret', $settings?->api_secret) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="API secret anahtarınızı girin">
                    @error('api_secret') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Kullanıcı Adı --}}
                <div x-show="needsField('username')" x-cloak>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Kullanıcı Adı</label>
                    <input type="text" name="username" id="username" value="{{ old('username', $settings?->username) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Kullanıcı adınızı girin">
                    @error('username') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Şifre --}}
                <div x-show="needsField('password')" x-cloak>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Şifre</label>
                    <input type="password" name="password" id="password" value="{{ old('password', $settings?->password) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Şifrenizi girin">
                    @error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Gönderici Adı --}}
                <div>
                    <label for="sender_id" class="block text-sm font-medium text-gray-700 mb-1">Gönderici Adı (Başlık) <span class="text-red-500">*</span></label>
                    <input type="text" name="sender_id" id="sender_id" value="{{ old('sender_id', $settings?->sender_id) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Örn: EMAREFIN" maxlength="11">
                    <p class="text-xs text-gray-400 mt-1">Maksimum 11 karakter</p>
                    @error('sender_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- API URL --}}
                <div x-show="needsField('api_url')" x-cloak>
                    <label for="api_url" class="block text-sm font-medium text-gray-700 mb-1">API URL</label>
                    <input type="url" name="api_url" id="api_url" value="{{ old('api_url', $settings?->api_url) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="https://api.smsservisi.com/v1">
                    @error('api_url') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Aktif/Pasif --}}
                <div class="flex items-center gap-3">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                               {{ old('is_active', $settings?->is_active) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-indigo-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                    <span class="text-sm font-medium text-gray-700">SMS Gönderimi Aktif</span>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 rounded-b-xl flex justify-end">
                <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                    <i class="fas fa-save mr-2"></i> Ayarları Kaydet
                </button>
            </div>
        </form>
    </div>

    {{-- Sağ: Test & Bakiye --}}
    <div class="space-y-6">
        {{-- Test SMS --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800">Test SMS Gönder</h3>
                <p class="text-sm text-gray-500 mt-1">Yapılandırmanızı test edin</p>
            </div>
            <form action="{{ route('sms.test') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label for="test_phone" class="block text-sm font-medium text-gray-700 mb-1">Telefon Numarası</label>
                    <input type="text" name="phone" id="test_phone"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="05XX XXX XX XX">
                    @error('phone') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition"
                        {{ !$settings || !$settings->is_active ? 'disabled' : '' }}>
                    <i class="fas fa-paper-plane mr-2"></i> Test SMS Gönder
                </button>
            </form>
        </div>

        {{-- Bakiye Sorgulama --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800">Bakiye Sorgula</h3>
                <p class="text-sm text-gray-500 mt-1">SMS kredi bakiyenizi kontrol edin</p>
            </div>
            <div class="p-6">
                <div x-data="{ balance: null, loading: false, error: null }" class="space-y-4">
                    <div x-show="balance !== null" x-cloak class="text-center py-4">
                        <p class="text-sm text-gray-500">Mevcut Bakiye</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1" x-text="'₺' + parseFloat(balance).toLocaleString('tr-TR', {minimumFractionDigits: 2})"></p>
                    </div>
                    <div x-show="error" x-cloak class="bg-red-50 border border-red-200 rounded-lg p-3 text-sm text-red-700" x-text="error"></div>
                    <button @click="loading = true; error = null;
                        fetch('{{ route('sms.balance') }}', { headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'} })
                        .then(r => r.json())
                        .then(d => { balance = d.balance; loading = false; })
                        .catch(e => { error = 'Bakiye sorgulanamadı'; loading = false; })"
                        class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition"
                        :disabled="loading"
                        {{ !$settings || !$settings->is_active ? 'disabled' : '' }}>
                        <i class="fas fa-sync-alt mr-2" :class="loading && 'fa-spin'"></i>
                        <span x-text="loading ? 'Sorgulanıyor...' : 'Bakiye Sorgula'"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Sağlayıcı Bilgisi --}}
        @if($settings && $settings->provider)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-800 mb-3">Aktif Sağlayıcı</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Sağlayıcı:</span>
                    <span class="font-medium text-gray-900">{{ $providers[$settings->provider] ?? $settings->provider }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Gönderici:</span>
                    <span class="font-medium text-gray-900">{{ $settings->sender_id ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Durum:</span>
                    @if($settings->is_active)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="fas fa-circle text-[6px] mr-1"></i> Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            <i class="fas fa-circle text-[6px] mr-1"></i> Pasif
                        </span>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function smsSettings() {
    return {
        provider: '{{ old('provider', $settings?->provider ?? '') }}',
        fieldMap: {
            'netgsm':     ['api_key', 'username', 'password'],
            'iletimerkezi': ['api_key', 'api_secret'],
            'mutlucell':  ['api_key', 'username', 'password'],
            'turatel':    ['api_key', 'api_secret', 'username'],
            'twilio':     ['api_key', 'api_secret'],
            'vonage':     ['api_key', 'api_secret'],
            'custom':     ['api_key', 'api_secret', 'username', 'password', 'api_url'],
        },
        needsField(field) {
            if (!this.provider) return false;
            return (this.fieldMap[this.provider] || []).includes(field);
        }
    }
}
</script>
@endpush
@endsection
