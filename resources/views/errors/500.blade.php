@extends('layouts.app')

@section('title', '500 - Sunucu Hatası')

@section('content')
<div class="flex items-center justify-center min-h-[60vh]">
    <div class="text-center">
        <div class="text-8xl font-bold text-gray-200 mb-4">500</div>
        <h2 class="text-2xl font-semibold text-gray-700 mb-2">Sunucu Hatası</h2>
        <p class="text-gray-500 mb-6">Bir şeyler ters gitti. Lütfen daha sonra tekrar deneyin.</p>
        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
            <i class="fas fa-home mr-2"></i>
            Ana Sayfaya Dön
        </a>
    </div>
</div>
@endsection
