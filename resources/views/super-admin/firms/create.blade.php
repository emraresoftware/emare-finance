@extends('super-admin.layout')

@section('title', 'Yeni Firma Oluştur')
@section('subtitle', 'Firma, şube ve yönetici bilgilerini girerek yeni firma açın')

@section('content')
<form method="POST" action="{{ route('super-admin.firms.store') }}" class="max-w-4xl">
    @csrf

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
                    <input type="text" name="firm_name" value="{{ old('firm_name') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent"
                           placeholder="Örn: ABC Market">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fatura E-posta <span class="text-red-500">*</span></label>
                    <input type="email" name="billing_email" value="{{ old('billing_email') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent"
                           placeholder="muhasebe@firma.com">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Plan</label>
                    <select name="plan_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500">
                        <option value="">Plan Seçiniz</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
                                {{ $plan->name }} — @money($plan->price_monthly)/ay
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deneme Süresi (gün)</label>
                    <input type="number" name="trial_days" value="{{ old('trial_days', 14) }}" min="0" max="365"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent"
                           placeholder="14">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sektör</label>
                    <select name="industry" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500">
                        <option value="">Sektör Seçiniz</option>
                        <option value="market" {{ old('industry') === 'market' ? 'selected' : '' }}>Market / Bakkal</option>
                        <option value="restaurant" {{ old('industry') === 'restaurant' ? 'selected' : '' }}>Restoran / Kafe</option>
                        <option value="retail" {{ old('industry') === 'retail' ? 'selected' : '' }}>Perakende Mağaza</option>
                        <option value="pharmacy" {{ old('industry') === 'pharmacy' ? 'selected' : '' }}>Eczane</option>
                        <option value="textile" {{ old('industry') === 'textile' ? 'selected' : '' }}>Tekstil / Giyim</option>
                        <option value="electronics" {{ old('industry') === 'electronics' ? 'selected' : '' }}>Elektronik</option>
                        <option value="wholesale" {{ old('industry') === 'wholesale' ? 'selected' : '' }}>Toptan Satış</option>
                        <option value="service" {{ old('industry') === 'service' ? 'selected' : '' }}>Hizmet Sektörü</option>
                        <option value="other" {{ old('industry') === 'other' ? 'selected' : '' }}>Diğer</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- 2) Şube Bilgileri --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-store mr-2 text-purple-500"></i> Merkez Şube
                </h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Şube Adı <span class="text-red-500">*</span></label>
                    <input type="text" name="branch_name" value="{{ old('branch_name', 'Merkez Şube') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Şehir</label>
                    <input type="text" name="branch_city" value="{{ old('branch_city') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent"
                           placeholder="İstanbul">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
                    <input type="text" name="branch_phone" value="{{ old('branch_phone') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent"
                           placeholder="0212 000 00 00">
                </div>
            </div>
        </div>

        {{-- 3) Yönetici Hesabı --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-user-shield mr-2 text-indigo-500"></i> Firma Yönetici Hesabı
                </h2>
                <p class="text-xs text-gray-500 mt-1">Bu bilgilerle firma yöneticisi giriş yapabilecek</p>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ad Soyad <span class="text-red-500">*</span></label>
                    <input type="text" name="admin_name" value="{{ old('admin_name') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent"
                           placeholder="Ahmet Yılmaz">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">E-posta <span class="text-red-500">*</span></label>
                    <input type="email" name="admin_email" value="{{ old('admin_email') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent"
                           placeholder="admin@firma.com">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Şifre <span class="text-red-500">*</span></label>
                    <div x-data="{ show: false }" class="relative">
                        <input :type="show ? 'text' : 'password'" name="admin_password" required minlength="6"
                               class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent"
                               placeholder="En az 6 karakter">
                        <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i :class="show ? 'fa-eye-slash' : 'fa-eye'" class="fas text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4) Modüller --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-puzzle-piece mr-2 text-amber-500"></i> Aktif Modüller
                </h2>
                <p class="text-xs text-gray-500 mt-1">Core modüller otomatik aktiftir</p>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($modules as $module)
                <label class="flex items-center p-3 rounded-lg border {{ $module->is_core ? 'border-green-200 bg-green-50' : 'border-gray-200 hover:bg-gray-50' }} transition cursor-pointer">
                    <input type="checkbox" name="modules[]" value="{{ $module->id }}"
                           {{ $module->is_core ? 'checked disabled' : '' }}
                           {{ in_array($module->id, old('modules', [])) ? 'checked' : '' }}
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
            <a href="{{ route('super-admin.firms.index') }}" class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm transition">
                <i class="fas fa-arrow-left mr-1"></i> Geri Dön
            </a>
            <button type="submit" class="px-6 py-2.5 bg-red-600 text-white font-medium rounded-lg text-sm hover:bg-red-700 transition shadow-sm">
                <i class="fas fa-building mr-2"></i> Firmayı Oluştur
            </button>
        </div>
    </div>
</form>
@endsection
