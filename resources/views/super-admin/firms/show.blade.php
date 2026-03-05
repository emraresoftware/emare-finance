@extends('super-admin.layout')

@section('title', $tenant->name)
@section('subtitle', 'Firma detayları ve yönetimi')

@section('content')
<div class="space-y-6">
    {{-- Üst bar --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('super-admin.firms.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-1"></i> Firma Listesine Dön
        </a>
        <div class="flex items-center space-x-2">
            <a href="{{ route('super-admin.firms.edit', $tenant) }}" class="px-4 py-2 bg-amber-500 text-white rounded-lg text-sm hover:bg-amber-600 transition">
                <i class="fas fa-pen mr-1"></i> Düzenle
            </a>
            <form method="POST" action="{{ route('super-admin.firms.toggle-status', $tenant) }}" class="inline">
                @csrf
                @method('PATCH')
                <button type="submit"
                        class="px-4 py-2 {{ $tenant->status === 'active' ? 'bg-red-500 hover:bg-red-600' : 'bg-green-500 hover:bg-green-600' }} text-white rounded-lg text-sm transition"
                        onclick="return confirm('Emin misiniz?')">
                    <i class="fas {{ $tenant->status === 'active' ? 'fa-pause' : 'fa-play' }} mr-1"></i>
                    {{ $tenant->status === 'active' ? 'Askıya Al' : 'Aktif Et' }}
                </button>
            </form>
        </div>
    </div>

    {{-- Firma Özet Kartları --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-building text-blue-600 text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $tenant->name }}</p>
                    <div class="flex items-center mt-1">
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
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 text-center">
            <p class="text-3xl font-bold text-indigo-600">{{ $stats['user_count'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Kullanıcı</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 text-center">
            <p class="text-3xl font-bold text-purple-600">{{ $stats['branch_count'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Şube</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 text-center">
            <p class="text-3xl font-bold text-amber-600">{{ $stats['module_count'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Aktif Modül</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Sol: Firma bilgileri --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Firma Bilgileri</h3>
            </div>
            <div class="p-6 space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Plan</span>
                    <span class="text-sm font-medium text-gray-800">{{ $tenant->plan?->name ?? '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">E-posta</span>
                    <span class="text-sm font-medium text-gray-800">{{ $tenant->billing_email }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Slug</span>
                    <span class="text-sm font-mono text-gray-600">{{ $tenant->slug ?? '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Deneme Süresi</span>
                    <span class="text-sm font-medium text-gray-800">
                        @if($tenant->trial_ends_at)
                            {{ $tenant->trial_ends_at->format('d.m.Y') }}
                            @if($tenant->trial_ends_at->isFuture())
                                <span class="text-green-600">({{ $tenant->trial_ends_at->locale('tr')->diffForHumans() }})</span>
                            @else
                                <span class="text-red-500">(Bitti)</span>
                            @endif
                        @else
                            —
                        @endif
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Sektör</span>
                    <span class="text-sm font-medium text-gray-800">{{ $tenant->meta['industry'] ?? '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Kayıt Tarihi</span>
                    <span class="text-sm font-medium text-gray-800">{{ $tenant->created_at->format('d.m.Y H:i') }}</span>
                </div>
            </div>
        </div>

        {{-- Orta: Kullanıcılar --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200" x-data="{ showAddUser: false }">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">Kullanıcılar</h3>
                <button @click="showAddUser = !showAddUser" class="text-sm text-red-600 hover:text-red-800">
                    <i class="fas fa-plus mr-1"></i> Ekle
                </button>
            </div>

            {{-- Kullanıcı ekleme formu --}}
            <div x-show="showAddUser" x-cloak class="p-4 border-b border-gray-200 bg-gray-50">
                <form method="POST" action="{{ route('super-admin.firms.add-user', $tenant) }}" class="space-y-3">
                    @csrf
                    <input type="text" name="name" placeholder="Ad Soyad" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500">
                    <input type="email" name="email" placeholder="E-posta" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500">
                    <input type="password" name="password" placeholder="Şifre" required minlength="6"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500">
                    <select name="role_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500">
                        <option value="">Rol Seçin</option>
                        @foreach(\App\Models\Role::all() as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                    <select name="branch_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500">
                        <option value="">Şube Seçin</option>
                        @foreach($tenant->branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700 transition">
                        <i class="fas fa-user-plus mr-1"></i> Kullanıcı Ekle
                    </button>
                </form>
            </div>

            <div class="divide-y divide-gray-200 max-h-80 overflow-y-auto">
                @forelse($tenant->users as $user)
                <div class="px-6 py-3 flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center mr-3">
                            <span class="text-indigo-600 text-xs font-bold">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $user->email }}</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">
                        {{ $user->primaryRole?->name ?? '—' }}
                    </span>
                </div>
                @empty
                <div class="px-6 py-8 text-center text-gray-500 text-sm">
                    <i class="fas fa-users text-2xl mb-2 text-gray-300"></i>
                    <p>Henüz kullanıcı yok</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Sağ: Şubeler + Modüller --}}
        <div class="space-y-6">
            {{-- Şubeler --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200" x-data="{ showAddBranch: false }">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">Şubeler</h3>
                    <button @click="showAddBranch = !showAddBranch" class="text-sm text-red-600 hover:text-red-800">
                        <i class="fas fa-plus mr-1"></i> Ekle
                    </button>
                </div>

                <div x-show="showAddBranch" x-cloak class="p-4 border-b border-gray-200 bg-gray-50">
                    <form method="POST" action="{{ route('super-admin.firms.add-branch', $tenant) }}" class="space-y-3">
                        @csrf
                        <input type="text" name="name" placeholder="Şube Adı" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500">
                        <input type="text" name="city" placeholder="Şehir"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500">
                        <input type="text" name="phone" placeholder="Telefon"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500">
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700 transition">
                            <i class="fas fa-store mr-1"></i> Şube Ekle
                        </button>
                    </form>
                </div>

                <div class="divide-y divide-gray-200">
                    @forelse($tenant->branches as $branch)
                    <div class="px-6 py-3 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $branch->name }}</p>
                            <p class="text-xs text-gray-500">{{ $branch->city ?? '—' }} {{ $branch->phone ? '• ' . $branch->phone : '' }}</p>
                        </div>
                        @if($branch->is_active)
                            <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                        @else
                            <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                        @endif
                    </div>
                    @empty
                    <div class="px-6 py-6 text-center text-gray-500 text-sm">Şube yok</div>
                    @endforelse
                </div>
            </div>

            {{-- Modüller --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Aktif Modüller</h3>
                </div>
                <div class="p-4 flex flex-wrap gap-2">
                    @foreach($tenant->tenantModules as $tm)
                        @if($tm->is_active && $tm->module)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $tm->module->is_core ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                            <i class="fas fa-puzzle-piece mr-1"></i> {{ $tm->module->name }}
                        </span>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
