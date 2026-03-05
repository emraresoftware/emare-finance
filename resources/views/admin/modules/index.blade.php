@extends('layouts.app')

@section('title', 'Modül Yönetimi')

@section('content')
<div class="max-w-5xl mx-auto">

    {{-- Başlık --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Modül Yönetimi</h2>
            <p class="mt-1 text-sm text-gray-500">
                @if($tenant)
                    Tenant: <strong>{{ $tenant->name }}</strong> — Modülleri aktif/pasif edebilirsiniz.
                @else
                    <strong>Süper Admin</strong> — Tüm modüller görüntüleniyor.
                @endif
            </p>
        </div>
        @if($tenant?->plan)
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                <i class="fas fa-crown mr-1.5"></i>{{ $tenant->plan->name ?? 'Paket Yok' }}
            </span>
        @endif
    </div>

    {{-- Modül Kartları --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($modules as $module)
            @php
                $isActive = $module->is_core ? true : ($tenantModules[$module->id] ?? false);
            @endphp
            <div class="bg-white rounded-xl border {{ $isActive ? 'border-green-200' : 'border-gray-200' }} shadow-sm p-5 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center {{ $isActive ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}">
                                @switch($module->code)
                                    @case('core_pos') <i class="fas fa-cash-register"></i> @break
                                    @case('hardware') <i class="fas fa-plug"></i> @break
                                    @case('einvoice') <i class="fas fa-file-invoice-dollar"></i> @break
                                    @case('income_expense') <i class="fas fa-exchange-alt"></i> @break
                                    @case('staff') <i class="fas fa-users-gear"></i> @break
                                    @case('advanced_reports') <i class="fas fa-chart-pie"></i> @break
                                    @case('api_access') <i class="fas fa-code"></i> @break
                                    @case('mobile_premium') <i class="fas fa-mobile-screen"></i> @break
                                    @default <i class="fas fa-puzzle-piece"></i>
                                @endswitch
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900">{{ $module->name }}</h3>
                                <span class="text-xs {{ $module->is_core ? 'text-blue-600' : 'text-gray-400' }}">
                                    {{ $module->is_core ? 'Çekirdek' : 'Opsiyonel' }}
                                </span>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $isActive ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $isActive ? 'Aktif' : 'Pasif' }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 mb-4">{{ $module->description }}</p>
                </div>

                @unless($module->is_core)
                    <form method="POST" action="{{ route('admin.modules.toggle', $module) }}">
                        @csrf
                        <button type="submit"
                                class="w-full py-2 px-3 rounded-lg text-sm font-medium border transition {{ $isActive ? 'border-red-200 text-red-600 hover:bg-red-50' : 'border-green-200 text-green-600 hover:bg-green-50' }}">
                            <i class="fas {{ $isActive ? 'fa-toggle-off' : 'fa-toggle-on' }} mr-1.5"></i>
                            {{ $isActive ? 'Devre Dışı Bırak' : 'Aktif Et' }}
                        </button>
                    </form>
                @else
                    <div class="w-full py-2 px-3 rounded-lg text-sm font-medium border border-gray-100 text-gray-400 text-center">
                        <i class="fas fa-lock mr-1.5"></i>Her zaman aktif
                    </div>
                @endunless
            </div>
        @endforeach
    </div>

</div>
@endsection
