@extends('layouts.app')

@section('title', '403 - Erişim Engellendi')

@section('content')
<div class="max-w-lg mx-auto text-center py-16">
    <div class="w-20 h-20 mx-auto bg-red-100 rounded-full flex items-center justify-center mb-6">
        <i class="fas fa-lock text-red-500 text-3xl"></i>
    </div>

    <div class="text-6xl font-bold text-gray-200 mb-4">403</div>
    <h2 class="text-2xl font-bold text-gray-900 mb-3">Erişim Engellendi</h2>

    <p class="text-gray-500 mb-6">
        {{ $exception->getMessage() ?: 'Bu sayfaya erişim yetkiniz bulunmamaktadır.' }}
    </p>

    @if(str_contains($exception->getMessage() ?? '', 'modülü aktif değildir'))
        <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-6 mb-6 text-left">
            <h3 class="text-sm font-semibold text-indigo-900 mb-2">
                <i class="fas fa-crown mr-1.5"></i>Bu özellik paketinize dahil değil
            </h3>
            <p class="text-sm text-indigo-700 mb-4">
                Bu modülü kullanmak için paketinizi yükseltin veya yöneticinizden modülü aktif etmesini isteyin.
            </p>
            @role('admin')
                <a href="{{ route('admin.modules.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                    <i class="fas fa-puzzle-piece mr-2"></i>Modül Yönetimine Git
                </a>
            @endrole
        </div>
    @endif

    <a href="{{ route('dashboard') }}"
       class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
        <i class="fas fa-arrow-left mr-2"></i>Dashboard'a Dön
    </a>
</div>
@endsection
