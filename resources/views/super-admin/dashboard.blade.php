@extends('super-admin.layout')

@section('title', 'Süper Admin Dashboard')
@section('subtitle', 'Tüm firmaların genel görünümü')

@section('content')
{{-- İstatistik kartları --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Toplam Firma</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['total_tenants'] }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-building text-blue-600 text-lg"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Aktif Firma</p>
                <p class="text-3xl font-bold text-green-600 mt-1">{{ $stats['active_tenants'] }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-check-circle text-green-600 text-lg"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Deneme Süreci</p>
                <p class="text-3xl font-bold text-amber-600 mt-1">{{ $stats['trial_tenants'] }}</p>
            </div>
            <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-clock text-amber-600 text-lg"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Askıda</p>
                <p class="text-3xl font-bold text-red-600 mt-1">{{ $stats['suspended_tenants'] }}</p>
            </div>
            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-pause-circle text-red-600 text-lg"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Toplam Kullanıcı</p>
                <p class="text-3xl font-bold text-indigo-600 mt-1">{{ $stats['total_users'] }}</p>
            </div>
            <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-users text-indigo-600 text-lg"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Toplam Şube</p>
                <p class="text-3xl font-bold text-purple-600 mt-1">{{ $stats['total_branches'] }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-store text-purple-600 text-lg"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Son eklenen firmalar --}}
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Son Eklenen Firmalar</h2>
            <a href="{{ route('super-admin.firms.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                Tümünü Gör <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Firma</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plan</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Kullanıcı</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Şube</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Durum</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Tarih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($recentTenants as $tenant)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <a href="{{ route('super-admin.firms.show', $tenant) }}" class="text-sm font-medium text-gray-900 hover:text-indigo-600">
                                {{ $tenant->name }}
                            </a>
                            <p class="text-xs text-gray-500">{{ $tenant->billing_email }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600">{{ $tenant->plan?->name ?? 'Plan Yok' }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                {{ $tenant->users->count() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                {{ $tenant->branches->count() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($tenant->status === 'active')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-circle text-[6px] mr-1"></i> Aktif
                                </span>
                            @elseif($tenant->status === 'suspended')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-circle text-[6px] mr-1"></i> Askıda
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <i class="fas fa-circle text-[6px] mr-1"></i> İptal
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right text-sm text-gray-500">
                            {{ $tenant->created_at->locale('tr')->diffForHumans() }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-building text-4xl mb-3 text-gray-300"></i>
                            <p>Henüz firma eklenmemiş.</p>
                            <a href="{{ route('super-admin.firms.create') }}" class="mt-2 inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800">
                                <i class="fas fa-plus mr-1"></i> İlk firmayı oluştur
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Plan dağılımı --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Plan Dağılımı</h2>
        </div>
        <div class="p-6 space-y-4">
            @foreach($plans as $plan)
            <div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-sm font-medium text-gray-700">{{ $plan->name }}</span>
                    <span class="text-sm text-gray-500">{{ $plan->tenants_count }} firma</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    @php
                        $percentage = $stats['total_tenants'] > 0 ? ($plan->tenants_count / $stats['total_tenants']) * 100 : 0;
                    @endphp
                    <div class="bg-indigo-600 h-2.5 rounded-full transition-all" style="width: {{ $percentage }}%"></div>
                </div>
            </div>
            @endforeach

            @if($plans->isEmpty())
                <p class="text-sm text-gray-500 text-center py-4">Henüz plan tanımlanmamış.</p>
            @endif
        </div>

        {{-- Hızlı aksiyonlar --}}
        <div class="px-6 py-4 border-t border-gray-200">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Hızlı İşlemler</h3>
            <div class="space-y-2">
                <a href="{{ route('super-admin.firms.create') }}"
                   class="w-full flex items-center px-3 py-2 text-sm text-white bg-red-600 hover:bg-red-700 rounded-lg transition">
                    <i class="fas fa-plus mr-2"></i> Yeni Firma Oluştur
                </a>
                <a href="{{ route('super-admin.firms.index') }}?status=suspended"
                   class="w-full flex items-center px-3 py-2 text-sm text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                    <i class="fas fa-pause mr-2"></i> Askıdaki Firmalar
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
