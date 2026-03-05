<?php

namespace App\Http\Controllers;

use App\Models\HardwareDevice;
use App\Models\HardwareDriver;
use Illuminate\Http\Request;

class HardwareController extends Controller
{
    /**
     * Donanım Yönetimi — Ana sayfa
     */
    public function index()
    {
        $devices = HardwareDevice::with('branch')
            ->orderBy('type')
            ->orderByDesc('is_default')
            ->get();

        $deviceTypes = config('hardware.device_types', []);
        $knownDevices = config('hardware.known_devices', []);

        // Tür bazında grupla
        $grouped = $devices->groupBy('type');

        // İstatistikler
        $stats = [
            'total'     => $devices->count(),
            'active'    => $devices->where('is_active', true)->count(),
            'connected' => $devices->where('status', 'connected')->count(),
            'types'     => $grouped->count(),
        ];

        return view('hardware.index', compact('devices', 'grouped', 'deviceTypes', 'knownDevices', 'stats'));
    }

    /**
     * Yeni cihaz ekleme formu
     */
    public function create()
    {
        $deviceTypes = config('hardware.device_types', []);
        $knownDevices = config('hardware.known_devices', []);
        $branches = \App\Models\Branch::orderBy('name')->get();
        $serialDefaults = config('hardware.serial_defaults', []);

        return view('hardware.create', compact('deviceTypes', 'knownDevices', 'branches', 'serialDefaults'));
    }

    /**
     * Cihaz kaydet
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'type'         => 'required|string',
            'connection'   => 'required|string',
            'protocol'     => 'nullable|string',
            'model'        => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'vendor_id'    => 'nullable|string|max:10',
            'product_id'   => 'nullable|string|max:10',
            'ip_address'   => 'nullable|ip',
            'port'         => 'nullable|integer|min:1|max:65535',
            'serial_port'  => 'nullable|string|max:255',
            'baud_rate'    => 'nullable|integer',
            'mac_address'  => 'nullable|string|max:17',
            'is_default'   => 'boolean',
            'is_active'    => 'boolean',
            'branch_id'    => 'nullable|exists:branches,id',
        ]);

        // Varsayılan yapılıyorsa diğerlerini kaldır
        if ($request->boolean('is_default')) {
            HardwareDevice::where('type', $validated['type'])
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        // Cihaz türüne göre varsayılan ayarları ekle
        $settings = $this->getDefaultSettings($validated['type'], $validated['protocol'] ?? null);
        $validated['settings'] = $settings;

        HardwareDevice::create($validated);

        return redirect()->route('hardware.index')
            ->with('success', '✅ Cihaz başarıyla eklendi: ' . $validated['name']);
    }

    /**
     * Cihaz düzenleme formu
     */
    public function edit(HardwareDevice $device)
    {
        $deviceTypes = config('hardware.device_types', []);
        $branches = \App\Models\Branch::orderBy('name')->get();

        return view('hardware.edit', compact('device', 'deviceTypes', 'branches'));
    }

    /**
     * Cihaz güncelle
     */
    public function update(Request $request, HardwareDevice $device)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'type'         => 'required|string',
            'connection'   => 'required|string',
            'protocol'     => 'nullable|string',
            'model'        => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'vendor_id'    => 'nullable|string|max:10',
            'product_id'   => 'nullable|string|max:10',
            'ip_address'   => 'nullable|ip',
            'port'         => 'nullable|integer|min:1|max:65535',
            'serial_port'  => 'nullable|string|max:255',
            'baud_rate'    => 'nullable|integer',
            'mac_address'  => 'nullable|string|max:17',
            'is_default'   => 'boolean',
            'is_active'    => 'boolean',
            'branch_id'    => 'nullable|exists:branches,id',
        ]);

        // Varsayılan yapılıyorsa diğerlerini kaldır
        if ($request->boolean('is_default')) {
            HardwareDevice::where('type', $validated['type'])
                ->where('id', '!=', $device->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        $device->update($validated);

        return redirect()->route('hardware.index')
            ->with('success', '✅ Cihaz güncellendi: ' . $device->name);
    }

    /**
     * Cihaz sil
     */
    public function destroy(HardwareDevice $device)
    {
        $name = $device->name;
        $device->delete();

        return redirect()->route('hardware.index')
            ->with('success', '🗑️ Cihaz silindi: ' . $name);
    }

    /**
     * Cihaz durumunu güncelle (AJAX)
     */
    public function updateStatus(Request $request, HardwareDevice $device)
    {
        $device->update([
            'status'       => $request->input('status', 'disconnected'),
            'last_seen_at' => $request->input('status') === 'connected' ? now() : $device->last_seen_at,
        ]);

        return response()->json(['success' => true, 'status' => $device->status]);
    }

    /**
     * Varsayılan yap (AJAX)
     */
    public function setDefault(HardwareDevice $device)
    {
        HardwareDevice::where('type', $device->type)
            ->where('is_default', true)
            ->update(['is_default' => false]);

        $device->update(['is_default' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * API: Cihaz listesi (JavaScript driver'ı için)
     */
    public function apiDevices()
    {
        $devices = HardwareDevice::active()->get()->map(fn ($d) => $d->toDriverConfig());

        return response()->json([
            'devices' => $devices,
            'config'  => [
                'escpos'          => config('hardware.escpos'),
                'label'           => config('hardware.label'),
                'serial_defaults' => config('hardware.serial_defaults'),
                'receipt'         => config('hardware.receipt'),
                'known_devices'   => config('hardware.known_devices'),
            ],
        ]);
    }

    /**
     * API: Ağ yazıcısına veri gönder (proxy)
     */
    public function apiPrintNetwork(Request $request)
    {
        $ip   = $request->input('ip');
        $port = $request->input('port', 9100);
        $data = $request->input('data', []);

        try {
            $socket = @fsockopen($ip, $port, $errno, $errstr, 5);
            if (!$socket) {
                return response()->json(['success' => false, 'error' => "Bağlantı hatası: $errstr ($errno)"], 500);
            }

            fwrite($socket, pack('C*', ...$data));
            fclose($socket);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Varsayılan ayarları oluştur.
     */
    private function getDefaultSettings(string $type, ?string $protocol): array
    {
        return match($type) {
            'receipt_printer' => config('hardware.escpos', []),
            'label_printer'   => config('hardware.label', []),
            'scale'           => config("hardware.scale_protocols.{$protocol}", config('hardware.scale_protocols.custom', [])),
            default           => [],
        };
    }

    // ─────────────────────────────────────────────────────────────
    //  Driver Kataloğu API Endpoint'leri
    // ─────────────────────────────────────────────────────────────

    /**
     * API: Sürücü kataloğu — tüm cihazlar veya tür bazında filtreleme
     */
    public function apiDrivers(Request $request)
    {
        $query = HardwareDriver::query();

        if ($type = $request->input('type')) {
            $query->ofType($type);
        }

        if ($manufacturer = $request->input('manufacturer')) {
            $query->byManufacturer($manufacturer);
        }

        if ($search = $request->input('q')) {
            $query->search($search);
        }

        if ($request->boolean('usb_only')) {
            $query->withUsb();
        }

        $drivers = $query->orderBy('device_type')
            ->orderBy('manufacturer')
            ->orderBy('model')
            ->get();

        return response()->json([
            'drivers' => $drivers->map(fn ($d) => $d->toDriverConfig()),
            'count'   => $drivers->count(),
        ]);
    }

    /**
     * API: Sürücü istatistikleri
     */
    public function apiDriverStats()
    {
        return response()->json([
            'stats'         => HardwareDriver::getStats(),
            'manufacturers' => HardwareDriver::getManufacturers(),
            'device_types'  => config('hardware.device_types', []),
        ]);
    }

    /**
     * API: Tek sürücü detayı
     */
    public function apiDriverShow(HardwareDriver $driver)
    {
        return response()->json([
            'driver' => array_merge($driver->toDriverConfig(), [
                'specs'       => $driver->specs,
                'features'    => $driver->features,
                'notes'       => $driver->notes,
                'connections' => $driver->connections,
            ]),
        ]);
    }

    /**
     * API: Cihaz türüne göre üreticileri getir
     */
    public function apiDriverManufacturers(Request $request)
    {
        $type = $request->input('type');

        $query = HardwareDriver::query();
        if ($type) {
            $query->ofType($type);
        }

        $manufacturers = $query->select('manufacturer')
            ->distinct()
            ->orderBy('manufacturer')
            ->pluck('manufacturer');

        return response()->json(['manufacturers' => $manufacturers]);
    }

    /**
     * API: Üreticiye göre modelleri getir
     */
    public function apiDriverModels(Request $request)
    {
        $manufacturer = $request->input('manufacturer');
        $type = $request->input('type');

        $query = HardwareDriver::query();

        if ($manufacturer) {
            $query->byManufacturer($manufacturer);
        }
        if ($type) {
            $query->ofType($type);
        }

        $models = $query->orderBy('model')
            ->get()
            ->map(fn ($d) => [
                'id'         => $d->id,
                'model'      => $d->model,
                'full_name'  => $d->full_name,
                'protocol'   => $d->protocol,
                'vendor_id'  => $d->vendor_id,
                'product_id' => $d->product_id,
                'connections' => $d->connections,
            ]);

        return response()->json(['models' => $models]);
    }
}
