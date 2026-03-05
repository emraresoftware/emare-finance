@extends('layouts.app')
@section('title', 'Cihaz Ekle')

@section('content')
<div class="max-w-3xl mx-auto space-y-6" x-data="deviceForm()">

    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('hardware.index') }}" class="text-sm text-indigo-600 hover:underline"><i class="fas fa-arrow-left mr-1"></i> Donanım Yönetimi</a>
            <h2 class="text-lg font-bold text-gray-800 mt-1">Yeni Cihaz Ekle</h2>
        </div>
    </div>

    <form method="POST" action="{{ route('hardware.store') }}" class="bg-white rounded-xl shadow-sm border p-6 space-y-6">
        @csrf

        {{-- Cihaz Türü Seçimi --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">Cihaz Türü</label>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach($deviceTypes as $typeKey => $typeConfig)
                <label class="relative cursor-pointer">
                    <input type="radio" name="type" value="{{ $typeKey }}" x-model="form.type" class="peer sr-only" {{ $loop->first ? 'checked' : '' }}>
                    <div class="border-2 border-gray-200 rounded-xl p-4 text-center peer-checked:border-{{ $typeConfig['color'] }}-500 peer-checked:bg-{{ $typeConfig['color'] }}-50 hover:border-gray-300 transition-all">
                        <div class="w-10 h-10 bg-{{ $typeConfig['color'] }}-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                            <i class="{{ $typeConfig['icon'] }} text-{{ $typeConfig['color'] }}-600"></i>
                        </div>
                        <div class="text-xs font-medium text-gray-700">{{ $typeConfig['label'] }}</div>
                    </div>
                </label>
                @endforeach
            </div>
            @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Cihaz Adı --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cihaz Adı *</label>
                <input type="text" name="name" value="{{ old('name') }}" x-model="form.name" required
                    placeholder="örn: Kasa Yazıcısı, Etiket Yazıcı..."
                    class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Üretici / Model (Driver DB ile akıllı seçim) --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Üretici</label>
                <select x-model="form.manufacturer" @change="loadModels()" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                    <option value="">Üretici Seçin</option>
                    <template x-for="m in manufacturers" :key="m">
                        <option :value="m" x-text="m"></option>
                    </template>
                </select>
                <input type="hidden" name="manufacturer" :value="form.manufacturer">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Model Seçimi (Driver DB'den dinamik) --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Model</label>
                <select name="model" x-model="form.model" @change="autoFill()" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                    <option value="">Elle Girin veya Seçin</option>
                    <template x-if="driverModels.length > 0">
                        <template x-for="dm in driverModels" :key="dm.id">
                            <option :value="dm.full_name" x-text="dm.full_name + (dm.protocol ? ' (' + dm.protocol.toUpperCase() + ')' : '')"></option>
                        </template>
                    </template>
                    <template x-if="driverModels.length === 0">
                        <template x-for="kd in filteredKnownDevices" :key="kd.name">
                            <option :value="kd.name" x-text="kd.name"></option>
                        </template>
                    </template>
                </select>
                <p class="text-xs text-gray-400 mt-1" x-show="driverModels.length > 0" x-text="driverModels.length + ' model bulundu'"></p>
            </div>

            {{-- Bağlantı Türü --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bağlantı Türü *</label>
                <select name="connection" x-model="form.connection" required class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                    <option value="usb">USB</option>
                    <option value="serial">Seri Port (RS232/COM)</option>
                    <option value="network">Ağ (TCP/IP)</option>
                    <option value="bluetooth">Bluetooth</option>
                    <option value="printer">Yazıcı Üzerinden (RJ11)</option>
                </select>
            </div>
        </div>

        {{-- Protokol --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">İletişim Protokolü</label>
            <select name="protocol" x-model="form.protocol" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                <option value="">Otomatik Algıla</option>
                <template x-for="p in availableProtocols" :key="p">
                    <option :value="p" x-text="p.toUpperCase()"></option>
                </template>
            </select>
        </div>

        {{-- USB Bilgileri --}}
        <div x-show="form.connection === 'usb'" x-cloak class="grid grid-cols-2 gap-4 p-4 bg-blue-50 rounded-lg">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Vendor ID</label>
                <input type="text" name="vendor_id" x-model="form.vendor_id" placeholder="örn: 04b8"
                    class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border font-mono">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Product ID</label>
                <input type="text" name="product_id" x-model="form.product_id" placeholder="örn: 0202"
                    class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border font-mono">
            </div>
        </div>

        {{-- Ağ Bilgileri --}}
        <div x-show="form.connection === 'network'" x-cloak class="grid grid-cols-2 gap-4 p-4 bg-purple-50 rounded-lg">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">IP Adresi</label>
                <input type="text" name="ip_address" x-model="form.ip_address" placeholder="192.168.1.100"
                    class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border font-mono">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Port</label>
                <input type="number" name="port" x-model="form.port" value="9100" placeholder="9100"
                    class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
            </div>
        </div>

        {{-- Seri Port Bilgileri --}}
        <div x-show="form.connection === 'serial'" x-cloak class="p-4 bg-amber-50 rounded-lg space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Seri Port</label>
                    <input type="text" name="serial_port" x-model="form.serial_port" placeholder="/dev/ttyUSB0 veya COM3"
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border font-mono">
                    <p class="text-xs text-gray-400 mt-1">Web Serial ile otomatik algılanır</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Baud Rate</label>
                    <select name="baud_rate" x-model="form.baud_rate" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                        <option value="2400">2400</option>
                        <option value="4800">4800</option>
                        <option value="9600" selected>9600</option>
                        <option value="19200">19200</option>
                        <option value="38400">38400</option>
                        <option value="57600">57600</option>
                        <option value="115200">115200</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Bluetooth --}}
        <div x-show="form.connection === 'bluetooth'" x-cloak class="p-4 bg-cyan-50 rounded-lg">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">MAC Adresi</label>
                <input type="text" name="mac_address" x-model="form.mac_address" placeholder="AA:BB:CC:DD:EE:FF"
                    class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border font-mono">
            </div>
        </div>

        {{-- Şube --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Şube</label>
                <select name="branch_id" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                    <option value="">Tüm Şubeler</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-6">
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="is_default" value="1" class="rounded border-gray-300 text-indigo-600">
                    <span class="text-gray-700">Varsayılan Cihaz</span>
                </label>
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-indigo-600">
                    <span class="text-gray-700">Aktif</span>
                </label>
            </div>
        </div>

        {{-- Kaydet --}}
        <div class="flex items-center justify-end gap-3 pt-4 border-t">
            <a href="{{ route('hardware.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm hover:bg-gray-200">İptal</a>
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">
                <i class="fas fa-plus mr-1"></i> Cihaz Ekle
            </button>
        </div>
    </form>
</div>

<script>
function deviceForm() {
    const knownDevices = @json($knownDevices);

    return {
        knownDevices,
        manufacturers: [],
        driverModels: [],
        form: {
            type: 'receipt_printer',
            name: '',
            model: '',
            manufacturer: '',
            connection: 'usb',
            protocol: '',
            vendor_id: '',
            product_id: '',
            ip_address: '',
            port: 9100,
            serial_port: '',
            baud_rate: 9600,
            mac_address: '',
        },

        async init() {
            // Cihaz türü değiştiğinde üreticileri yükle
            this.$watch('form.type', () => {
                this.loadManufacturers();
                this.driverModels = [];
                this.form.manufacturer = '';
                this.form.model = '';
                // A4 yazıcı seçildiğinde bağlantıyı otomatik ayarla
                if (this.form.type === 'a4_printer') {
                    this.form.connection = 'network';
                    this.form.protocol = 'system';
                }
            });
            // İlk yükleme
            await this.loadManufacturers();
        },

        async loadManufacturers() {
            try {
                const res = await fetch(`/api/hardware/drivers/manufacturers?type=${this.form.type}`);
                const data = await res.json();
                this.manufacturers = data.manufacturers || [];
            } catch (e) {
                // Fallback — known_devices'tan üreticileri çıkar
                const names = [...new Set(this.filteredKnownDevices.map(d => d.name.split(' ')[0]))];
                this.manufacturers = names.sort();
            }
        },

        async loadModels() {
            if (!this.form.manufacturer) { this.driverModels = []; return; }
            try {
                const res = await fetch(`/api/hardware/drivers/models?manufacturer=${encodeURIComponent(this.form.manufacturer)}&type=${this.form.type}`);
                const data = await res.json();
                this.driverModels = data.models || [];
            } catch (e) {
                this.driverModels = [];
            }
        },

        get filteredKnownDevices() {
            return this.knownDevices.filter(d => d.type === this.form.type);
        },

        get availableProtocols() {
            const map = {
                receipt_printer:  ['escpos', 'star', 'citizen'],
                label_printer:    ['zpl', 'epl', 'tspl', 'escpos'],
                barcode_scanner:  ['keyboard_wedge', 'serial', 'hid'],
                scale:            ['cas', 'dibal', 'digi', 'mettler', 'custom'],
                cash_drawer:      ['escpos_kick', 'rj11', 'direct'],
                customer_display: ['pole_display', 'lcd', 'vfd'],
                a4_printer:       ['system'],
            };
            return map[this.form.type] || [];
        },

        autoFill() {
            // Önce driver DB'den dene
            const dm = this.driverModels.find(d => d.full_name === this.form.model);
            if (dm) {
                this.form.vendor_id = dm.vendor_id || '';
                this.form.product_id = dm.product_id || '';
                this.form.protocol = dm.protocol || '';
                this.form.name = this.form.name || dm.full_name;
                if (dm.connections && dm.connections.length > 0) {
                    this.form.connection = dm.connections[0];
                }
                return;
            }

            // Fallback: known_devices'tan
            const kd = this.knownDevices.find(d => d.name === this.form.model);
            if (kd) {
                this.form.vendor_id = kd.vendor_id || '';
                this.form.product_id = kd.product_id || '';
                this.form.protocol = kd.protocol || '';
                this.form.name = this.form.name || kd.name;
                this.form.connection = kd.vendor_id ? 'usb' : 'serial';
            }
        }
    }
}
</script>
@endsection
