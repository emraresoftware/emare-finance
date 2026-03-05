@extends('layouts.app')
@section('title', 'Yeni Müşteri Ekle')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('customers.index') }}" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-arrow-left text-lg"></i>
            </a>
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Yeni Müşteri Ekle</h2>
                <p class="text-sm text-gray-500">Yeni bir cari hesap oluşturun</p>
            </div>
        </div>
    </div>

    <form action="{{ route('customers.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Temel Bilgiler --}}
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="font-semibold text-gray-800 mb-4"><i class="fas fa-user mr-2 text-blue-500"></i>Müşteri Bilgileri</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Müşteri Adı <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border @error('name') border-red-500 @enderror"
                        placeholder="Müşteri adını girin">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tür <span class="text-red-500">*</span></label>
                    <select name="type" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                        <option value="individual" {{ old('type') === 'individual' ? 'selected' : '' }}>Bireysel</option>
                        <option value="company" {{ old('type') === 'company' ? 'selected' : '' }}>Firma</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border"
                        placeholder="05XX XXX XX XX">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border @error('email') border-red-500 @enderror"
                        placeholder="ornek@email.com">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Vergi Bilgileri --}}
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="font-semibold text-gray-800 mb-4"><i class="fas fa-file-invoice mr-2 text-green-500"></i>Vergi Bilgileri</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vergi Numarası</label>
                    <input type="text" name="tax_number" value="{{ old('tax_number') }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vergi Dairesi</label>
                    <input type="text" name="tax_office" value="{{ old('tax_office') }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
            </div>
        </div>

        {{-- Adres Bilgileri --}}
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="font-semibold text-gray-800 mb-4"><i class="fas fa-map-marker-alt mr-2 text-red-500"></i>Adres Bilgileri</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Adres</label>
                    <textarea name="address" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border"
                        placeholder="Açık adres">{{ old('address') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">İlçe</label>
                    <input type="text" name="district" value="{{ old('district') }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">İl</label>
                    <input type="text" name="city" value="{{ old('city') }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
            </div>
        </div>

        {{-- Notlar --}}
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="font-semibold text-gray-800 mb-4"><i class="fas fa-sticky-note mr-2 text-yellow-500"></i>Notlar</h3>
            <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border"
                placeholder="Müşteri hakkında notlar...">{{ old('notes') }}</textarea>
        </div>

        {{-- Kaydet --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('customers.index') }}" class="bg-gray-100 text-gray-600 px-6 py-2.5 rounded-lg text-sm hover:bg-gray-200">
                İptal
            </a>
            <button type="submit" class="bg-green-600 text-white px-6 py-2.5 rounded-lg text-sm hover:bg-green-700 font-medium">
                <i class="fas fa-plus mr-1"></i> Müşteri Ekle
            </button>
        </div>
    </form>
</div>
@endsection
