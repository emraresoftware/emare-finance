@extends('layouts.app')
@section('title', 'Raporlar')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <a href="{{ route('reports.sales') }}" class="bg-white rounded-xl shadow-sm p-8 border hover:border-indigo-300 hover:shadow-md transition text-center">
        <div class="w-16 h-16 bg-indigo-100 rounded-2xl mx-auto flex items-center justify-center mb-4">
            <i class="fas fa-chart-bar text-indigo-600 text-2xl"></i>
        </div>
        <h3 class="text-lg font-semibold text-gray-800">Satış Raporu</h3>
        <p class="text-sm text-gray-500 mt-2">Günlük, haftalık ve aylık satış performansı</p>
    </a>

    <a href="{{ route('reports.products') }}" class="bg-white rounded-xl shadow-sm p-8 border hover:border-green-300 hover:shadow-md transition text-center">
        <div class="w-16 h-16 bg-green-100 rounded-2xl mx-auto flex items-center justify-center mb-4">
            <i class="fas fa-ranking-star text-green-600 text-2xl"></i>
        </div>
        <h3 class="text-lg font-semibold text-gray-800">Ürün Raporu</h3>
        <p class="text-sm text-gray-500 mt-2">En çok satan ürünler ve ürün performansı</p>
    </a>

    <a href="{{ route('reports.profit') }}" class="bg-white rounded-xl shadow-sm p-8 border hover:border-yellow-300 hover:shadow-md transition text-center">
        <div class="w-16 h-16 bg-yellow-100 rounded-2xl mx-auto flex items-center justify-center mb-4">
            <i class="fas fa-coins text-yellow-600 text-2xl"></i>
        </div>
        <h3 class="text-lg font-semibold text-gray-800">Kâr Analizi</h3>
        <p class="text-sm text-gray-500 mt-2">Brüt kâr, maliyet ve kârlılık analizi</p>
    </a>
</div>
@endsection
