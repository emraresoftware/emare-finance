@extends('layouts.app')
@section('title', 'Cihaz Düzenle — ' . $device->name)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('hardware.index') }}" class="text-sm text-indigo-600 hover:underline"><i class="fas fa-arrow-left mr-1"></i> Donanım Yönetimi</a>
            <h2 class="text-lg font-bold text-gray-800 mt-1">Cihaz Düzenle: {{ $device->name }}</h2>
        </div>
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $device->getStatusColor() }}-100 text-{{ $device->getStatusColor() }}-700">
            <i class="{{ $device->getTypeIcon() }} mr-1"></i>
            {{ $device->getTypeLabel() }}
        </span>
    </div>

    <form method="POST" action="{{ route('hardware.update', $device) }}" class="bg-white rounded-xl shadow-sm border p-6 space-y-6">
        @csrf @method('PUT')

        <input type="hidden" name="type" value="{{ $device->type }}">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cihaz Adı *</label>
                <input type="text" name="name" value="{{ old('name', $device->name) }}" required
                    class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Model</label>
                <input type="text" name="model" value="{{ old('model', $device->model) }}"
                    class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Üretici</label>
                <input type="text" name="manufacturer" value="{{ old('manufacturer', $device->manufacturer) }}"
                    class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bağlantı Türü *</label>
                <select name="connection" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                    <option value="usb" {{ $device->connection === 'usb' ? 'selected' : '' }}>USB</option>
                    <option value="serial" {{ $device->connection === 'serial' ? 'selected' : '' }}>Seri Port</option>
                    <option value="network" {{ $device->connection === 'network' ? 'selected' : '' }}>Ağ (TCP/IP)</option>
                    <option value="bluetooth" {{ $device->connection === 'bluetooth' ? 'selected' : '' }}>Bluetooth</option>
                    <option value="printer" {{ $device->connection === 'printer' ? 'selected' : '' }}>Yazıcı Üzerinden</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Protokol</label>
                <input type="text" name="protocol" value="{{ old('protocol', $device->protocol) }}"
                    class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
            </div>
        </div>

        {{-- Bağlantı Detayları --}}
        <div class="p-4 bg-gray-50 rounded-lg space-y-4">
            <h4 class="text-sm font-medium text-gray-700"><i class="fas fa-link mr-1"></i> Bağlantı Detayları</h4>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Vendor ID</label>
                    <input type="text" name="vendor_id" value="{{ $device->vendor_id }}" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border font-mono">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Product ID</label>
                    <input type="text" name="product_id" value="{{ $device->product_id }}" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border font-mono">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">IP Adresi</label>
                    <input type="text" name="ip_address" value="{{ $device->ip_address }}" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border font-mono">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Port</label>
                    <input type="number" name="port" value="{{ $device->port ?? 9100 }}" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Seri Port</label>
                    <input type="text" name="serial_port" value="{{ $device->serial_port }}" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border font-mono">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Baud Rate</label>
                    <select name="baud_rate" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                        @foreach([2400, 4800, 9600, 19200, 38400, 57600, 115200] as $rate)
                        <option value="{{ $rate }}" {{ ($device->baud_rate ?? 9600) == $rate ? 'selected' : '' }}>{{ $rate }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">MAC Adresi (Bluetooth)</label>
                <input type="text" name="mac_address" value="{{ $device->mac_address }}" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border font-mono">
            </div>
        </div>

        {{-- Şube & Seçenekler --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Şube</label>
                <select name="branch_id" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                    <option value="">Tüm Şubeler</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ $device->branch_id == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-6">
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="is_default" value="1" {{ $device->is_default ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                    <span class="text-gray-700">Varsayılan</span>
                </label>
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="is_active" value="1" {{ $device->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                    <span class="text-gray-700">Aktif</span>
                </label>
            </div>
        </div>

        {{-- Son Görülme --}}
        @if($device->last_seen_at)
        <div class="p-3 bg-green-50 rounded-lg text-sm text-green-700">
            <i class="fas fa-clock mr-1"></i> Son bağlantı: {{ $device->last_seen_at->diffForHumans() }}
            ({{ $device->last_seen_at->format('d.m.Y H:i') }})
        </div>
        @endif

        {{-- Butonlar --}}
        <div class="flex items-center justify-between pt-4 border-t">
            <form method="POST" action="{{ route('hardware.destroy', $device) }}" onsubmit="return confirm('Bu cihazı silmek istediğinize emin misiniz?')">
                @csrf @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-100 text-red-600 rounded-lg text-sm hover:bg-red-200">
                    <i class="fas fa-trash mr-1"></i> Cihazı Sil
                </button>
            </form>
            <div class="flex gap-3">
                <a href="{{ route('hardware.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm hover:bg-gray-200">İptal</a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">
                    <i class="fas fa-save mr-1"></i> Kaydet
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
