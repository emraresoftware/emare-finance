@extends('layouts.guest')

@section('title', 'Şifre Sıfırla')
@section('subtitle', 'Yeni şifrenizi belirleyin')

@section('content')
<form method="POST" action="{{ route('password.update') }}" class="space-y-5">
    @csrf

    <input type="hidden" name="token" value="{{ $token }}">

    {{-- E-posta --}}
    <div>
        <label for="email" class="block text-sm font-medium text-gray-700">E-posta Adresi</label>
        <div class="mt-1 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-envelope text-gray-400 text-sm"></i>
            </div>
            <input id="email" name="email" type="email" autocomplete="email" required
                   value="{{ $email ?? old('email') }}"
                   class="block w-full pl-10 pr-3 py-2.5 border {{ $errors->has('email') ? 'border-red-300' : 'border-gray-300' }} rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
        </div>
        @error('email')
            <p class="mt-1 text-xs text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
        @enderror
    </div>

    {{-- Yeni Şifre --}}
    <div>
        <label for="password" class="block text-sm font-medium text-gray-700">Yeni Şifre</label>
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

    {{-- Kaydet butonu --}}
    <div>
        <button type="submit"
                class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
            <i class="fas fa-key mr-2"></i>
            Şifremi Sıfırla
        </button>
    </div>
</form>
@endsection
