@extends('layouts.app')

@section('title', 'Rol Yönetimi')

@section('content')
<div class="max-w-5xl mx-auto">

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Rol Yönetimi</h2>
        <p class="mt-1 text-sm text-gray-500">Sistem rollerini görüntüleyin ve izinlerini düzenleyin.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kod</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kapsam</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">İzin Sayısı</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tür</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">İşlem</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($roles as $role)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center
                                {{ $role->code === 'admin' ? 'bg-red-100 text-red-600' :
                                   ($role->code === 'manager' ? 'bg-blue-100 text-blue-600' :
                                   ($role->code === 'cashier' ? 'bg-green-100 text-green-600' :
                                   ($role->code === 'accounting' ? 'bg-yellow-100 text-yellow-600' : 'bg-gray-100 text-gray-600'))) }}">
                                <i class="fas fa-user-shield text-xs"></i>
                            </div>
                            <span class="ml-3 text-sm font-medium text-gray-900">{{ $role->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <code class="text-xs bg-gray-100 px-2 py-1 rounded text-gray-600">{{ $role->code }}</code>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ucfirst($role->scope) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                            {{ $role->permissions_count }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($role->is_system)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700">
                                <i class="fas fa-shield-halved mr-1"></i>Sistem
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-50 text-gray-600">Özel</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <a href="{{ route('admin.roles.show', $role) }}"
                           class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                            <i class="fas fa-key mr-1"></i>İzinler
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
