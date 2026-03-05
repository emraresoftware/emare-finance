@extends('super-admin.layout')

@section('title', $tenant->name . ' — Düzenle')
@section('subtitle', 'Firma bilgilerini ve modüllerini düzenleyin')

@section('content')
<form method="POST" action="{{ route('super-admin.firms.update', $tenant) }}" class="max-w-4xl">
    @csrf
    @method('PUT')

    <div class="space-y-6">
        {{-- 1) Firma Bilgileri --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-building mr-2 text-red-500"></i> Firma Bilgileri
                </h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Firma Adı <span class="text-red-500">*</span></label>
                    <input type="text" name="firm_name" value="{{ old('firm_name', $tenant->name) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fatura E-posta <span class="text-red-500">*</span></label>
                    <input type="email" name="billing_email" value="{{ old('billing_email', $tenant->billing_email) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Plan</label>
                    <select name="plan_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500">
                        <option value="">Plan Seçiniz</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" {{ old('plan_id', $tenant->plan_id) == $plan->id ? 'selected' : '' }}>
                                {{ $plan->name }} — @money($plan->price_monthly)/ay
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Durum <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500">
                        <option value="active" {{ old('status', $tenant->status) === 'active' ? 'selected' : '' }}>✅ Aktif</option>
                        <option value="suspended" {{ old('status', $tenant->status) === 'suspended' ? 'selected' : '' }}>⏸️ Askıya Alınmış</option>
                        <option value="cancelled" {{ old('status', $tenant->status) === 'cancelled' ? 'selected' : '' }}>❌ İptal</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deneme Süresi Uzat (gün)</label>
                    <input type="number" name="trial_days" value="{{ old('trial_days') }}" min="0" max="365"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent"
                           placeholder="Boş bırakırsanız mevcut süre korunur">
                    @if($tenant->trial_ends_at)
                        <p class="text-xs text-gray-500 mt-1">
                            Mevcut: {{ $tenant->trial_ends_at->format('d.m.Y') }}
                            @if($tenant->trial_ends_at->isFuture())
                                ({{ $tenant->trial_ends_at->locale('tr')->diffForHumans() }})
                            @else
                                (Süresi dolmuş)
                            @endif
                        </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- 2) Modüller --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-puzzle-piece mr-2 text-amber-500"></i> Modüller
                </h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($modules as $module)
                <label class="flex items-center p-3 rounded-lg border {{ $module->is_core ? 'border-green-200 bg-green-50' : 'border-gray-200 hover:bg-gray-50' }} transition cursor-pointer">
                    <input type="checkbox" name="modules[]" value="{{ $module->id }}"
                           {{ $module->is_core ? 'checked disabled' : '' }}
                           {{ in_array($module->id, old('modules', $activeModuleIds)) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-red-600 focus:ring-red-500 mr-3">
                    @if($module->is_core)
                        <input type="hidden" name="modules[]" value="{{ $module->id }}">
                    @endif
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $module->name }}</p>
                        <p class="text-xs text-gray-500">{{ $module->code }}</p>
                    </div>
                    @if($module->is_core)
                        <span class="ml-auto text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Core</span>
                    @endif
                </label>
                @endforeach
            </div>
        </div>

        {{-- Butonlar --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('super-admin.firms.show', $tenant) }}" class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm transition">
                <i class="fas fa-arrow-left mr-1"></i> Geri Dön
            </a>
            <button type="submit" class="px-6 py-2.5 bg-red-600 text-white font-medium rounded-lg text-sm hover:bg-red-700 transition shadow-sm">
                <i class="fas fa-save mr-2"></i> Değişiklikleri Kaydet
            </button>
        </div>
    </div>
</form>
@endsection
