@extends('layouts.guest')

@section('title', 'Giriş Yap')
@section('subtitle', 'Hesabınıza giriş yapın')

@section('content')
<form method="POST" action="{{ route('login') }}" class="space-y-5">
    @csrf

    {{-- Kullanıcı adı / E-posta --}}
    <div>
        <label for="login" class="block text-sm font-medium text-gray-700">Kullanıcı Adı veya E-posta</label>
        <div class="mt-1 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-envelope text-gray-400 text-sm"></i>
            </div>
            <input id="login" name="login" type="text" autocomplete="username" required
                   value="{{ old('login') }}"
                   class="block w-full pl-10 pr-3 py-2.5 border {{ $errors->has('login') ? 'border-red-300 ring-red-500' : 'border-gray-300' }} rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                   placeholder="emre veya ornek@sirket.com">
        </div>
        @error('login')
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
            <input id="password" name="password" :type="show ? 'text' : 'password'" autocomplete="current-password" required
                   class="block w-full pl-10 pr-10 py-2.5 border {{ $errors->has('password') ? 'border-red-300' : 'border-gray-300' }} rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                   placeholder="••••••••">
            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                <i :class="show ? 'fa-eye-slash' : 'fa-eye'" class="fas text-gray-400 text-sm hover:text-gray-600"></i>
            </button>
        </div>
        @error('password')
            <p class="mt-1 text-xs text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
        @enderror
    </div>

    {{-- Beni hatırla + Şifremi unuttum --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <input id="remember" name="remember" type="checkbox" {{ old('remember') ? 'checked' : '' }}
                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
            <label for="remember" class="ml-2 block text-sm text-gray-700">Beni hatırla</label>
        </div>

        <a href="{{ route('password.request') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
            Şifremi unuttum
        </a>
    </div>

    {{-- Giriş butonu --}}
    <div>
        <button type="submit"
                class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
            <i class="fas fa-sign-in-alt mr-2"></i>
            Giriş Yap
        </button>
    </div>
</form>

{{-- Kayıt linki --}}
<div class="mt-6">
    <div class="relative">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-200"></div>
        </div>
        <div class="relative flex justify-center text-sm">
            <span class="px-2 bg-white text-gray-500">veya</span>
        </div>
    </div>

    <div class="mt-4 text-center">
        <a href="{{ route('register') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
            Hesabınız yok mu? <span class="font-semibold">Ücretsiz kayıt olun</span>
        </a>
    </div>
</div>
@endsection
