@extends('layouts.guest')

@section('title', 'Şifremi Unuttum')
@section('subtitle', 'Şifre sıfırlama bağlantısı alın')

@section('content')
<form method="POST" action="{{ route('password.email') }}" class="space-y-5">
    @csrf

    <p class="text-sm text-gray-600 mb-4">
        E-posta adresinizi girin, size şifre sıfırlama bağlantısı göndereceğiz.
    </p>

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

    {{-- Gönder butonu --}}
    <div>
        <button type="submit"
                class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
            <i class="fas fa-paper-plane mr-2"></i>
            Sıfırlama Bağlantısı Gönder
        </button>
    </div>
</form>

{{-- Giriş linki --}}
<div class="mt-6 text-center">
    <a href="{{ route('login') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
        <i class="fas fa-arrow-left mr-1"></i> Giriş sayfasına dön
    </a>
</div>
@endsection
