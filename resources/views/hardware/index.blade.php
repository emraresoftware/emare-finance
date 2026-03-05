@extends('layouts.app')
@section('title', 'Donanım Yönetimi')

@section('content')
<div class="space-y-6" x-data="hardwarePanel()">

    {{-- Üst Bilgi --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-gray-800">🔌 Donanım Yönetimi</h2>
            <p class="text-sm text-gray-500 mt-1">Yazıcı, terazi, barkod okuyucu ve kasa çekmecesi — Tak & Çalıştır</p>
        </div>
        <div class="flex gap-2">
            <button @click="scanUSB()" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 inline-flex items-center gap-2">
                <i class="fas fa-usb"></i> USB Tara
            </button>
            <a href="{{ route('hardware.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 inline-flex items-center gap-2">
                <i class="fas fa-plus"></i> Cihaz Ekle
            </a>
        </div>
    </div>

    {{-- İstatistik Kartları --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Toplam Cihaz</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
                </div>
                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-plug text-indigo-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Aktif</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['active'] }}</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Bağlı</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['connected'] }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-link text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Cihaz Türü</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $stats['types'] }}</p>
                </div>
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-layer-group text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- API Uyumluluk Kontrolü --}}
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h3 class="font-semibold text-gray-800 mb-4"><i class="fas fa-microchip mr-2 text-cyan-500"></i>Tarayıcı Uyumluluk</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex items-center gap-3 p-3 rounded-lg" :class="capabilities.webusb ? 'bg-green-50' : 'bg-red-50'">
                <i class="fas fa-usb text-lg" :class="capabilities.webusb ? 'text-green-600' : 'text-red-400'"></i>
                <div>
                    <div class="text-sm font-medium" :class="capabilities.webusb ? 'text-green-800' : 'text-red-700'">WebUSB API</div>
                    <div class="text-xs" :class="capabilities.webusb ? 'text-green-600' : 'text-red-500'" x-text="capabilities.webusb ? 'Destekleniyor ✓' : 'Desteklenmiyor ✗'"></div>
                </div>
            </div>
            <div class="flex items-center gap-3 p-3 rounded-lg" :class="capabilities.serial ? 'bg-green-50' : 'bg-red-50'">
                <i class="fas fa-plug text-lg" :class="capabilities.serial ? 'text-green-600' : 'text-red-400'"></i>
                <div>
                    <div class="text-sm font-medium" :class="capabilities.serial ? 'text-green-800' : 'text-red-700'">Web Serial API</div>
                    <div class="text-xs" :class="capabilities.serial ? 'text-green-600' : 'text-red-500'" x-text="capabilities.serial ? 'Destekleniyor ✓' : 'Desteklenmiyor ✗'"></div>
                </div>
            </div>
            <div class="flex items-center gap-3 p-3 rounded-lg" :class="capabilities.bluetooth ? 'bg-green-50' : 'bg-yellow-50'">
                <i class="fas fa-bluetooth-b text-lg" :class="capabilities.bluetooth ? 'text-green-600' : 'text-yellow-500'"></i>
                <div>
                    <div class="text-sm font-medium" :class="capabilities.bluetooth ? 'text-green-800' : 'text-yellow-700'">Web Bluetooth</div>
                    <div class="text-xs" :class="capabilities.bluetooth ? 'text-green-600' : 'text-yellow-500'" x-text="capabilities.bluetooth ? 'Destekleniyor ✓' : 'Kısmen Destekleniyor'"></div>
                </div>
            </div>
        </div>
        <p class="text-xs text-gray-400 mt-3"><i class="fas fa-info-circle mr-1"></i> WebUSB ve Web Serial için Chrome veya Edge tarayıcı gereklidir. HTTPS veya localhost üzerinde çalışmalıdır.</p>
    </div>

    {{-- Cihaz Listesi — Tür Bazında --}}
    @forelse($grouped as $type => $typeDevices)
    @php
        $typeConfig = $deviceTypes[$type] ?? ['label' => $type, 'icon' => 'fas fa-plug', 'color' => 'gray'];
    @endphp
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="px-6 py-4 border-b bg-{{ $typeConfig['color'] }}-50 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-{{ $typeConfig['color'] }}-100 rounded-lg flex items-center justify-center">
                    <i class="{{ $typeConfig['icon'] }} text-{{ $typeConfig['color'] }}-600"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800">{{ $typeConfig['label'] }}</h3>
                    <p class="text-xs text-gray-500">{{ $typeConfig['description'] ?? '' }}</p>
                </div>
            </div>
            <span class="text-sm text-gray-500">{{ $typeDevices->count() }} cihaz</span>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($typeDevices as $device)
            <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50">
                <div class="flex items-center gap-4">
                    {{-- Durum göstergesi --}}
                    <div class="relative">
                        <div class="w-3 h-3 rounded-full bg-{{ $device->getStatusColor() }}-400"></div>
                        @if($device->status === 'connected')
                        <div class="w-3 h-3 rounded-full bg-green-400 absolute top-0 animate-ping"></div>
                        @endif
                    </div>
                    <div>
                        <div class="font-medium text-gray-800">
                            {{ $device->name }}
                            @if($device->is_default)
                            <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                <i class="fas fa-star mr-0.5"></i> Varsayılan
                            </span>
                            @endif
                        </div>
                        <div class="text-xs text-gray-500 mt-0.5">
                            {{ $device->model ?? $device->manufacturer ?? '' }}
                            · {{ $device->getConnectionLabel() }}
                            @if($device->protocol) · {{ strtoupper($device->protocol) }} @endif
                            @if($device->ip_address) · {{ $device->ip_address }}:{{ $device->port ?? 9100 }} @endif
                            @if($device->serial_port) · {{ $device->serial_port }} ({{ $device->baud_rate }} bps) @endif
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    {{-- Durum badge --}}
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-{{ $device->getStatusColor() }}-100 text-{{ $device->getStatusColor() }}-700">
                        {{ $device->getStatusLabel() }}
                    </span>

                    {{-- Bağlan butonu --}}
                    @if($device->connection === 'usb')
                    <button @click="connectDevice({{ $device->id }}, 'usb', '{{ $device->type }}')" class="px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg text-xs hover:bg-blue-200" title="USB Bağlan">
                        <i class="fas fa-usb mr-1"></i> Bağlan
                    </button>
                    @elseif($device->connection === 'serial')
                    <button @click="connectDevice({{ $device->id }}, 'serial', '{{ $device->type }}')" class="px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg text-xs hover:bg-blue-200" title="Seri Port Bağlan">
                        <i class="fas fa-plug mr-1"></i> Bağlan
                    </button>
                    @endif

                    {{-- Test butonu --}}
                    @if(in_array($device->type, ['receipt_printer', 'label_printer']))
                    <button @click="testPrinter({{ $device->id }})" class="px-3 py-1.5 bg-green-100 text-green-700 rounded-lg text-xs hover:bg-green-200" title="Test Yazdır">
                        <i class="fas fa-print mr-1"></i> Test
                    </button>
                    @elseif($device->type === 'cash_drawer')
                    <button @click="testDrawer({{ $device->id }})" class="px-3 py-1.5 bg-emerald-100 text-emerald-700 rounded-lg text-xs hover:bg-emerald-200" title="Çekmece Aç">
                        <i class="fas fa-cash-register mr-1"></i> Aç
                    </button>
                    @endif

                    {{-- Varsayılan yap --}}
                    @if(!$device->is_default)
                    <form method="POST" action="{{ route('hardware.set_default', $device) }}" class="inline">
                        @csrf
                        <button type="submit" class="px-2 py-1.5 bg-yellow-100 text-yellow-700 rounded-lg text-xs hover:bg-yellow-200" title="Varsayılan Yap">
                            <i class="fas fa-star"></i>
                        </button>
                    </form>
                    @endif

                    {{-- Düzenle --}}
                    <a href="{{ route('hardware.edit', $device) }}" class="px-2 py-1.5 bg-gray-100 text-gray-600 rounded-lg text-xs hover:bg-gray-200" title="Düzenle">
                        <i class="fas fa-edit"></i>
                    </a>

                    {{-- Sil --}}
                    <form method="POST" action="{{ route('hardware.destroy', $device) }}" class="inline" onsubmit="return confirm('Bu cihazı silmek istediğinize emin misiniz?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-2 py-1.5 bg-red-100 text-red-600 rounded-lg text-xs hover:bg-red-200" title="Sil">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @empty
    {{-- Boş Durum --}}
    <div class="bg-white rounded-xl shadow-sm border p-12 text-center">
        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-plug text-3xl text-gray-400"></i>
        </div>
        <h3 class="text-lg font-semibold text-gray-700 mb-2">Henüz Cihaz Eklenmedi</h3>
        <p class="text-sm text-gray-500 mb-6">Yazıcı, terazi, barkod okuyucu veya kasa çekmecenizi ekleyin.</p>
        <div class="flex items-center justify-center gap-3">
            <button @click="scanUSB()" class="bg-blue-600 text-white px-6 py-3 rounded-lg text-sm hover:bg-blue-700">
                <i class="fas fa-usb mr-2"></i> USB Cihaz Tara
            </button>
            <a href="{{ route('hardware.create') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg text-sm hover:bg-indigo-700">
                <i class="fas fa-plus mr-2"></i> Manuel Ekle
            </a>
        </div>
    </div>
    @endforelse

    {{-- Desteklenen Cihazlar --}}
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="font-semibold text-gray-800"><i class="fas fa-microchip mr-2 text-indigo-500"></i>Desteklenen Cihaz Modelleri (Tak & Çalıştır)</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Fiş Yazıcıları --}}
                <div>
                    <h4 class="font-medium text-sm text-gray-700 mb-3 flex items-center gap-2">
                        <i class="fas fa-receipt text-indigo-500"></i> Fiş Yazıcıları
                    </h4>
                    <ul class="space-y-1.5 text-sm text-gray-600">
                        @foreach(collect($knownDevices)->where('type', 'receipt_printer') as $kd)
                        <li class="flex items-center gap-2">
                            <i class="fas fa-check text-green-500 text-xs"></i>
                            {{ $kd['name'] }}
                            <span class="text-xs text-gray-400">{{ strtoupper($kd['protocol']) }}</span>
                        </li>
                        @endforeach
                        <li class="text-xs text-gray-400 mt-2"><i class="fas fa-info-circle mr-1"></i> Tüm ESC/POS uyumlu yazıcılar desteklenir</li>
                    </ul>
                </div>

                {{-- Etiket Yazıcıları --}}
                <div>
                    <h4 class="font-medium text-sm text-gray-700 mb-3 flex items-center gap-2">
                        <i class="fas fa-tags text-purple-500"></i> Etiket Yazıcıları
                    </h4>
                    <ul class="space-y-1.5 text-sm text-gray-600">
                        @foreach(collect($knownDevices)->where('type', 'label_printer') as $kd)
                        <li class="flex items-center gap-2">
                            <i class="fas fa-check text-green-500 text-xs"></i>
                            {{ $kd['name'] }}
                            <span class="text-xs text-gray-400">{{ strtoupper($kd['protocol']) }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Barkod Okuyucular --}}
                <div>
                    <h4 class="font-medium text-sm text-gray-700 mb-3 flex items-center gap-2">
                        <i class="fas fa-barcode text-green-500"></i> Barkod Okuyucular
                    </h4>
                    <ul class="space-y-1.5 text-sm text-gray-600">
                        @foreach(collect($knownDevices)->where('type', 'barcode_scanner') as $kd)
                        <li class="flex items-center gap-2">
                            <i class="fas fa-check text-green-500 text-xs"></i>
                            {{ $kd['name'] }}
                        </li>
                        @endforeach
                        <li class="text-xs text-gray-400 mt-2"><i class="fas fa-info-circle mr-1"></i> Klavye wedge modlu tüm okuyucular otomatik çalışır</li>
                    </ul>
                </div>
            </div>

            <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                <h4 class="text-sm font-medium text-blue-800 mb-2"><i class="fas fa-weight-scale mr-1"></i> Terazi Desteği</h4>
                <p class="text-xs text-blue-700">
                    CAS, Dibal, DIGI, Mettler Toledo ve genel seri port teraziler desteklenir.
                    Seri port üzerinden bağlantı gerekir (USB-Serial dönüştürücü kullanılabilir).
                </p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="/js/hardware-drivers.js"></script>
<script>
function hardwarePanel() {
    return {
        hw: null,
        capabilities: {
            webusb: !!navigator.usb,
            serial: !!navigator.serial,
            bluetooth: !!navigator.bluetooth,
        },

        async init() {
            this.hw = new HardwareManager();
            await this.hw.init();
        },

        async scanUSB() {
            try {
                const info = await this.hw.connectUSB();
                // Bağlandıysa sayfayı yenile
                setTimeout(() => location.reload(), 1500);
            } catch (e) {
                if (e.name !== 'NotFoundError') {
                    alert('USB tarama hatası: ' + e.message);
                }
            }
        },

        async connectDevice(id, method, type) {
            try {
                if (method === 'usb') {
                    await this.hw.connectUSB(type);
                } else if (method === 'serial') {
                    await this.hw.connectSerial();
                }

                // Durumu sunucuya bildir
                await fetch(`/donanim/${id}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ status: 'connected' }),
                });
                setTimeout(() => location.reload(), 1000);
            } catch (e) {
                if (e.name !== 'NotFoundError') {
                    alert('Bağlantı hatası: ' + e.message);
                }
            }
        },

        async testPrinter(id) {
            try {
                await this.hw.testPrinter();
            } catch (e) {
                // Bağlantı yoksa önce bağlan
                try {
                    await this.hw.connectUSB('receipt_printer');
                    await this.hw.testPrinter();
                } catch (e2) {
                    alert('Test yazdırma hatası: ' + e2.message);
                }
            }
        },

        async testDrawer(id) {
            try {
                await this.hw.openCashDrawer();
            } catch (e) {
                alert('Çekmece açma hatası: ' + e.message);
            }
        }
    }
}
</script>
@endpush
@endsection
