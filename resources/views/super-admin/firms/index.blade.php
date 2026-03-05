@extends('super-admin.layout')

@section('title', 'Firma Yönetimi')
@section('subtitle', 'Tüm firmaları yönetin')

@section('content')
{{-- Üst bar: Arama + Filtre + Yeni Firma --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
    <form method="GET" class="flex flex-wrap items-center gap-3">
        <div class="flex-1 min-w-[200px]">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Firma adı, e-posta veya slug ara..."
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
            </div>
        </div>

        <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500">
            <option value="">Tüm Durumlar</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
            <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Askıda</option>
            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>İptal</option>
        </select>

        <select name="plan_id" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500">
            <option value="">Tüm Planlar</option>
            @foreach($plans as $plan)
                <option value="{{ $plan->id }}" {{ request('plan_id') == $plan->id ? 'selected' : '' }}>{{ $plan->name }}</option>
            @endforeach
        </select>

        <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg text-sm hover:bg-gray-700 transition">
            <i class="fas fa-filter mr-1"></i> Filtrele
        </button>

        @if(request()->hasAny(['search', 'status', 'plan_id']))
            <a href="{{ route('super-admin.firms.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200 transition">
                <i class="fas fa-times mr-1"></i> Temizle
            </a>
        @endif

        <a href="{{ route('super-admin.firms.create') }}" class="ml-auto px-4 py-2 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700 transition">
            <i class="fas fa-plus mr-1"></i> Yeni Firma Aç
        </a>
    </form>
</div>

{{-- Firma Tablosu --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Firma</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plan</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Kullanıcı</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Şube</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Durum</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Deneme</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kayıt</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">İşlemler</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($tenants as $tenant)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-sm text-gray-500 font-mono">{{ $tenant->id }}</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('super-admin.firms.show', $tenant) }}" class="text-sm font-semibold text-gray-900 hover:text-red-600 transition">
                            {{ $tenant->name }}
                        </a>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $tenant->billing_email }}</p>
                    </td>
                    <td class="px-6 py-4">
                        @if($tenant->plan)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $tenant->plan->name }}
                            </span>
                        @else
                            <span class="text-xs text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-sm font-medium text-gray-700">{{ $tenant->users->count() }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-sm font-medium text-gray-700">{{ $tenant->branches->count() }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($tenant->status === 'active')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-circle text-[6px] mr-1.5"></i> Aktif
                            </span>
                        @elseif($tenant->status === 'suspended')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-circle text-[6px] mr-1.5"></i> Askıda
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                <i class="fas fa-circle text-[6px] mr-1.5"></i> İptal
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($tenant->trial_ends_at)
                            @if($tenant->trial_ends_at->isFuture())
                                <span class="text-xs text-amber-600 font-medium">
                                    {{ $tenant->trial_ends_at->locale('tr')->diffForHumans() }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400">Bitti</span>
                            @endif
                        @else
                            <span class="text-xs text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $tenant->created_at->format('d.m.Y') }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('super-admin.firms.show', $tenant) }}"
                               class="p-1.5 text-gray-400 hover:text-blue-600 transition" title="Detay">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('super-admin.firms.edit', $tenant) }}"
                               class="p-1.5 text-gray-400 hover:text-amber-600 transition" title="Düzenle">
                                <i class="fas fa-pen"></i>
                            </a>
                            <form method="POST" action="{{ route('super-admin.firms.toggle-status', $tenant) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="p-1.5 {{ $tenant->status === 'active' ? 'text-gray-400 hover:text-red-600' : 'text-gray-400 hover:text-green-600' }} transition"
                                        title="{{ $tenant->status === 'active' ? 'Askıya Al' : 'Aktif Et' }}"
                                        onclick="return confirm('{{ $tenant->status === 'active' ? 'Firmayı askıya almak istediğinize emin misiniz?' : 'Firmayı aktif etmek istediğinize emin misiniz?' }}')">
                                    <i class="fas {{ $tenant->status === 'active' ? 'fa-pause' : 'fa-play' }}"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-16 text-center">
                        <div class="text-gray-400">
                            <i class="fas fa-building text-5xl mb-4"></i>
                            <p class="text-lg font-medium text-gray-600">Henüz firma yok</p>
                            <p class="text-sm text-gray-500 mt-1">İlk firmanızı oluşturarak başlayın.</p>
                            <a href="{{ route('super-admin.firms.create') }}"
                               class="mt-4 inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition">
                                <i class="fas fa-plus mr-2"></i> Yeni Firma Oluştur
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($tenants->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $tenants->links() }}
    </div>
    @endif
</div>
@endsection
