@extends('layouts.app')

@section('title', 'Şube Modülleri — ' . $branch->name)

@section('content')
<div class="max-w-5xl mx-auto">

    <div class="mb-6">
        <a href="{{ route('admin.modules.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
            <i class="fas fa-arrow-left mr-1"></i>Modül Yönetimine Dön
        </a>
        <h2 class="text-2xl font-bold text-gray-900 mt-2">{{ $branch->name }} — Şube Modülleri</h2>
        <p class="mt-1 text-sm text-gray-500">Bu şube için modülleri ayrı ayrı yönetebilirsiniz.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($modules as $module)
            @php
                $tenantActive = $module->is_core ? true : ($tenantModules[$module->id] ?? false);
                $branchActive = $module->is_core ? true : ($branchModules[$module->id] ?? $tenantActive);
            @endphp
            <div class="bg-white rounded-xl border {{ $branchActive ? 'border-green-200' : 'border-gray-200' }} shadow-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-900">{{ $module->name }}</h3>
                    <div class="flex items-center space-x-2">
                        @unless($tenantActive)
                            <span class="text-xs text-orange-600 bg-orange-50 px-2 py-0.5 rounded">Tenant'ta pasif</span>
                        @endunless
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $branchActive ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $branchActive ? 'Aktif' : 'Pasif' }}
                        </span>
                    </div>
                </div>

                @if(!$module->is_core && $tenantActive)
                    <form method="POST" action="{{ route('admin.modules.branch.toggle', [$branch, $module]) }}">
                        @csrf
                        <button type="submit"
                                class="w-full py-2 px-3 rounded-lg text-sm font-medium border transition {{ $branchActive ? 'border-red-200 text-red-600 hover:bg-red-50' : 'border-green-200 text-green-600 hover:bg-green-50' }}">
                            {{ $branchActive ? 'Devre Dışı Bırak' : 'Aktif Et' }}
                        </button>
                    </form>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endsection
