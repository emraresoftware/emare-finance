@extends('layouts.app')

@section('title', $role->name . ' — İzin Yönetimi')

@section('content')
<div class="max-w-5xl mx-auto">

    <div class="mb-6">
        <a href="{{ route('admin.roles.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
            <i class="fas fa-arrow-left mr-1"></i>Rol Listesine Dön
        </a>
        <h2 class="text-2xl font-bold text-gray-900 mt-2">{{ $role->name }} — İzinler</h2>
        <p class="mt-1 text-sm text-gray-500">
            Kod: <code class="bg-gray-100 px-1 py-0.5 rounded text-xs">{{ $role->code }}</code> —
            Kapsam: {{ ucfirst($role->scope) }}
            @if($role->is_system)
                — <span class="text-blue-600">Sistem rolü</span>
            @endif
        </p>
    </div>

    @if($role->code === 'admin')
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <i class="fas fa-info-circle text-blue-500 mr-3"></i>
                <p class="text-sm text-blue-700">Admin rolü tüm izinlere otomatik olarak sahiptir ve düzenlenemez.</p>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.roles.permissions.update', $role) }}">
        @csrf

        <div class="space-y-6">
            @foreach($allPermissions as $group => $permissions)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gray-50 px-6 py-3 border-b border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">
                            <i class="fas fa-folder mr-1.5 text-gray-400"></i>{{ ucfirst($group) }}
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($permissions as $permission)
                                <label class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox"
                                           name="permissions[]"
                                           value="{{ $permission->id }}"
                                           {{ in_array($permission->id, $rolePermissionIds) ? 'checked' : '' }}
                                           {{ $role->code === 'admin' ? 'disabled' : '' }}
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <div>
                                        <span class="text-sm text-gray-700">{{ $permission->name }}</span>
                                        <span class="block text-xs text-gray-400">{{ $permission->code }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @unless($role->code === 'admin')
            <div class="mt-6 flex justify-end">
                <button type="submit"
                        class="inline-flex items-center px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                    <i class="fas fa-save mr-2"></i>İzinleri Kaydet
                </button>
            </div>
        @endunless
    </form>

</div>
@endsection
