@extends('layouts.guest')

@section('title', 'Kayıt Ol')
@section('subtitle', 'Ücretsiz hesap oluşturun')

@section('content')
<form method="POST" action="{{ route('register') }}" class="space-y-5">
    @csrf

    {{-- Ad Soyad --}}
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Ad Soyad</label>
        <div class="mt-1 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-user text-gray-400 text-sm"></i>
            </div>
            <input id="name" name="name" type="text" autocomplete="name" required
                   value="{{ old('name') }}"
                   class="block w-full pl-10 pr-3 py-2.5 border {{ $errors->has('name') ? 'border-red-300' : 'border-gray-300' }} rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                   placeholder="Adınız Soyadınız">
        </div>
        @error('name')
            <p class="mt-1 text-xs text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
        @enderror
    </div>

    {{-- İşletme Adı --}}
    <div>
        <label for="business_name" class="block text-sm font-medium text-gray-700">İşletme Adı</label>
        <div class="mt-1 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-building text-gray-400 text-sm"></i>
            </div>
            <input id="business_name" name="business_name" type="text" required
                   value="{{ old('business_name') }}"
                   class="block w-full pl-10 pr-3 py-2.5 border {{ $errors->has('business_name') ? 'border-red-300' : 'border-gray-300' }} rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                   placeholder="İşletme veya firma adınız">
        </div>
        @error('business_name')
            <p class="mt-1 text-xs text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
        @enderror
    </div>

    {{-- E-posta --}}
    <div>
        <label for="email" class="block text-sm font-medium text-gray-700">E-posta Adresi</label>
        <div class="mt-1 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-envelope text-gray-400 text-sm"></i>
            </div>
            <input id="email" name="email" type="email" autocomplete="email" required
                   value="{{ old('email') }}"
                   class="block w-full pl-10 pr-3 py-2.5 border {{ $errors->has('email') ? 'border-red-300' : 'border-gray-300' }} rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                   placeholder="ornek@sirket.com">
        </div>
        @error('email')
            <p class="mt-1 text-xs text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
        @enderror
    </div>

    {{-- Şifre --}}
    <div>
        <label for="password" class="block text-sm font-medium text-gray-700">Şifre</label>
        <div class="mt-1 relative" x-data="{ show: false }">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-lock text-gray-400 text-sm"></i>
            </div>
            <input id="password" name="password" :type="show ? 'text' : 'password'" autocomplete="new-password" required
                   class="block w-full pl-10 pr-10 py-2.5 border {{ $errors->has('password') ? 'border-red-300' : 'border-gray-300' }} rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                   placeholder="En az 8 karakter">
            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                <i :class="show ? 'fa-eye-slash' : 'fa-eye'" class="fas text-gray-400 text-sm hover:text-gray-600"></i>
            </button>
        </div>
        @error('password')
            <p class="mt-1 text-xs text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
        @enderror
    </div>

    {{-- Şifre Tekrar --}}
    <div>
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Şifre Tekrar</label>
        <div class="mt-1 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-lock text-gray-400 text-sm"></i>
            </div>
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                   class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                   placeholder="Şifrenizi tekrar girin">
        </div>
    </div>

    {{-- Sektör Seçimi (opsiyonel) --}}
    <div>
        <label for="industry" class="block text-sm font-medium text-gray-700">Sektör <span class="text-gray-400">(opsiyonel)</span></label>
        <div class="mt-1 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-industry text-gray-400 text-sm"></i>
            </div>
            <select id="industry" name="industry"
                    class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm text-gray-700">
                <option value="">— Seçiniz —</option>
                <option value="market" {{ old('industry') === 'market' ? 'selected' : '' }}>Market / Bakkal</option>
                <option value="cafe" {{ old('industry') === 'cafe' ? 'selected' : '' }}>Kafe / Restoran</option>
                <option value="boutique" {{ old('industry') === 'boutique' ? 'selected' : '' }}>Butik / Giyim</option>
                <option value="wholesale" {{ old('industry') === 'wholesale' ? 'selected' : '' }}>Toptan Satış</option>
                <option value="service" {{ old('industry') === 'service' ? 'selected' : '' }}>Hizmet Sektörü</option>
            </select>
        </div>
    </div>

    {{-- Kayıt butonu --}}
    <div>
        <button type="submit"
                class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
            <i class="fas fa-user-plus mr-2"></i>
            Ücretsiz Hesap Oluştur
        </button>
    </div>

    <p class="text-xs text-gray-500 text-center mt-2">
        Kayıt olarak <a href="#" class="text-indigo-600 hover:underline">Kullanım Şartları</a>'nı kabul etmiş olursunuz.
    </p>
</form>

{{-- Giriş linki --}}
<div class="mt-6 text-center">
    <a href="{{ route('login') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
        Zaten hesabınız var mı? <span class="font-semibold">Giriş yapın</span>
    </a>
</div>
@endsection
