@extends('layouts.app')
@section('title', 'Müşteri Güncelle - ' . $customer->name)

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('customers.show', $customer) }}" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-arrow-left text-lg"></i>
            </a>
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Müşteriyi Güncelle</h2>
                <p class="text-sm text-gray-500">{{ $customer->name }}</p>
            </div>
        </div>
    </div>

    <form action="{{ route('customers.update', $customer) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Temel Bilgiler --}}
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="font-semibold text-gray-800 mb-4"><i class="fas fa-user mr-2 text-blue-500"></i>Müşteri Bilgileri</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Müşteri Adı <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $customer->name) }}" required
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border @error('name') border-red-500 @enderror">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tür <span class="text-red-500">*</span></label>
                    <select name="type" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                        <option value="individual" {{ old('type', $customer->type) === 'individual' ? 'selected' : '' }}>Bireysel</option>
                        <option value="company" {{ old('type', $customer->type) === 'company' ? 'selected' : '' }}>Firma</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
                    <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $customer->email) }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border @error('email') border-red-500 @enderror">
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
                    <input type="text" name="tax_number" value="{{ old('tax_number', $customer->tax_number) }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vergi Dairesi</label>
                    <input type="text" name="tax_office" value="{{ old('tax_office', $customer->tax_office) }}"
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
                    <textarea name="address" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">{{ old('address', $customer->address) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">İlçe</label>
                    <input type="text" name="district" value="{{ old('district', $customer->district) }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">İl</label>
                    <input type="text" name="city" value="{{ old('city', $customer->city) }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
            </div>
        </div>

        {{-- Notlar --}}
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="font-semibold text-gray-800 mb-4"><i class="fas fa-sticky-note mr-2 text-yellow-500"></i>Notlar</h3>
            <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border"
                placeholder="Müşteri hakkında notlar...">{{ old('notes', $customer->notes) }}</textarea>
        </div>

        {{-- Bakiye Bilgisi (salt okunur) --}}
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-gray-800"><i class="fas fa-wallet mr-2 text-indigo-500"></i>Bakiye Durumu</h3>
                    <p class="text-sm text-gray-400 mt-1">Bakiye sistem tarafından otomatik hesaplanır.</p>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-bold {{ $customer->balance < 0 ? 'text-red-600' : 'text-green-600' }}">
                        ₺{{ number_format(abs($customer->balance), 2, ',', '.') }}
                    </p>
                    <p class="text-xs text-gray-500">{{ $customer->balance >= 0 ? 'Alacak' : 'Borç' }}</p>
                </div>
            </div>
        </div>

        {{-- Kaydet --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('customers.show', $customer) }}" class="bg-gray-100 text-gray-600 px-6 py-2.5 rounded-lg text-sm hover:bg-gray-200">
                İptal
            </a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-lg text-sm hover:bg-blue-700 font-medium">
                <i class="fas fa-save mr-1"></i> Güncelle
            </button>
        </div>
    </form>
</div>
@endsection
