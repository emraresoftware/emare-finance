<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HardwareDriverSeeder extends Seeder
{
    /**
     * Donanım sürücü veritabanını yükle.
     *
     * database/data/hardware-drivers.json dosyasındaki
     * kapsamlı cihaz arşivini hardware_drivers tablosuna aktarır.
     *
     * Kullanım:
     *   php artisan db:seed --class=HardwareDriverSeeder
     */
    public function run(): void
    {
        $jsonPath = database_path('data/hardware-drivers.json');

        if (!file_exists($jsonPath)) {
            $this->command->error('hardware-drivers.json dosyası bulunamadı!');
            return;
        }

        $data = json_decode(file_get_contents($jsonPath), true);

        if (!$data) {
            $this->command->error('JSON parse hatası!');
            return;
        }

        // Tabloyu temizle ve yeniden yükle
        DB::table('hardware_drivers')->truncate();

        $count = 0;

        // Fiş Yazıcılar
        foreach ($data['receipt_printers'] ?? [] as $device) {
            DB::table('hardware_drivers')->insert([
                'device_type'   => 'receipt_printer',
                'manufacturer'  => $device['manufacturer'],
                'model'         => $device['model'],
                'vendor_id'     => $device['vendor_id'] ?? null,
                'product_id'    => $device['product_id'] ?? null,
                'protocol'      => $device['protocol'] ?? 'escpos',
                'connections'   => json_encode($device['connections'] ?? []),
                'features'      => json_encode($device['features'] ?? []),
                'specs'         => json_encode([
                    'paper_width'   => $device['paper_width'] ?? 80,
                    'char_per_line' => $device['char_per_line'] ?? 48,
                    'dpi'           => $device['dpi'] ?? 203,
                    'speed_mm_s'    => $device['speed_mm_s'] ?? 200,
                    'cutter'        => $device['cutter'] ?? 'none',
                    'drawer_ports'  => $device['drawer_ports'] ?? 0,
                    'encoding'      => $device['encoding'] ?? 'WPC1254',
                ]),
                'notes'         => $device['notes'] ?? null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
            $count++;
        }

        // Etiket Yazıcılar
        foreach ($data['label_printers'] ?? [] as $device) {
            DB::table('hardware_drivers')->insert([
                'device_type'   => 'label_printer',
                'manufacturer'  => $device['manufacturer'],
                'model'         => $device['model'],
                'vendor_id'     => $device['vendor_id'] ?? null,
                'product_id'    => $device['product_id'] ?? null,
                'protocol'      => $device['protocol'] ?? 'tspl',
                'connections'   => json_encode($device['connections'] ?? []),
                'features'      => json_encode($device['features'] ?? []),
                'specs'         => json_encode([
                    'dpi'           => $device['dpi'] ?? 203,
                    'max_width_mm'  => $device['max_width_mm'] ?? 108,
                    'speed_mm_s'    => $device['speed_mm_s'] ?? 127,
                ]),
                'notes'         => $device['notes'] ?? null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
            $count++;
        }

        // A4 Yazıcılar
        foreach ($data['a4_printers'] ?? [] as $device) {
            DB::table('hardware_drivers')->insert([
                'device_type'   => 'a4_printer',
                'manufacturer'  => $device['manufacturer'],
                'model'         => $device['model'],
                'vendor_id'     => null,
                'product_id'    => null,
                'protocol'      => $device['protocol'] ?? 'ipp',
                'connections'   => json_encode($device['connections'] ?? []),
                'features'      => json_encode($device['features'] ?? []),
                'specs'         => json_encode([
                    'type'          => $device['type'] ?? 'laser_mono',
                    'dpi'           => $device['dpi'] ?? 600,
                    'speed_ppm'     => $device['speed_ppm'] ?? 20,
                    'duplex'        => $device['duplex'] ?? false,
                    'paper_sizes'   => $device['paper_sizes'] ?? ['A4'],
                    'driver_class'  => $device['driver_class'] ?? 'PCL6',
                ]),
                'notes'         => $device['notes'] ?? null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
            $count++;
        }

        // Barkod Okuyucular
        foreach ($data['barcode_scanners'] ?? [] as $device) {
            DB::table('hardware_drivers')->insert([
                'device_type'   => 'barcode_scanner',
                'manufacturer'  => $device['manufacturer'],
                'model'         => $device['model'],
                'vendor_id'     => $device['vendor_id'] ?? null,
                'product_id'    => $device['product_id'] ?? null,
                'protocol'      => $device['protocol'] ?? 'hid',
                'connections'   => json_encode($device['connections'] ?? []),
                'features'      => json_encode($device['features'] ?? []),
                'specs'         => json_encode([
                    'type_2d'       => $device['type_2d'] ?? false,
                    'decode_types'  => $device['decode_types'] ?? [],
                ]),
                'notes'         => $device['notes'] ?? null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
            $count++;
        }

        // Teraziler
        foreach ($data['scales'] ?? [] as $device) {
            DB::table('hardware_drivers')->insert([
                'device_type'   => 'scale',
                'manufacturer'  => $device['manufacturer'],
                'model'         => $device['model'],
                'vendor_id'     => null,
                'product_id'    => null,
                'protocol'      => $device['protocol'] ?? 'custom',
                'connections'   => json_encode($device['connections'] ?? []),
                'features'      => json_encode($device['features'] ?? []),
                'specs'         => json_encode([
                    'capacity_kg'     => $device['capacity_kg'] ?? 30,
                    'division_g'      => $device['division_g'] ?? 10,
                    'baud_rate'       => $device['baud_rate'] ?? 9600,
                    'data_bits'       => $device['data_bits'] ?? 8,
                    'stop_bits'       => $device['stop_bits'] ?? 1,
                    'parity'          => $device['parity'] ?? 'none',
                    'request_cmd'     => $device['request_cmd'] ?? '',
                    'response_format' => $device['response_format'] ?? '',
                ]),
                'notes'         => $device['notes'] ?? null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
            $count++;
        }

        // Kasa Çekmeceleri
        foreach ($data['cash_drawers'] ?? [] as $device) {
            DB::table('hardware_drivers')->insert([
                'device_type'   => 'cash_drawer',
                'manufacturer'  => $device['manufacturer'],
                'model'         => $device['model'],
                'vendor_id'     => null,
                'product_id'    => null,
                'protocol'      => $device['trigger'] ?? 'escpos_kick',
                'connections'   => json_encode([$device['connection'] ?? 'rj11']),
                'features'      => json_encode($device['features'] ?? []),
                'specs'         => json_encode([
                    'kick_pin'      => $device['kick_pin'] ?? 2,
                    'compartments'  => $device['compartments'] ?? [],
                    'dimensions_mm' => $device['dimensions_mm'] ?? [],
                    'interface'     => $device['interface'] ?? 'RJ11',
                ]),
                'notes'         => $device['notes'] ?? null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
            $count++;
        }

        // Müşteri Ekranları
        foreach ($data['customer_displays'] ?? [] as $device) {
            DB::table('hardware_drivers')->insert([
                'device_type'   => 'customer_display',
                'manufacturer'  => $device['manufacturer'],
                'model'         => $device['model'],
                'vendor_id'     => $device['vendor_id'] ?? null,
                'product_id'    => $device['product_id'] ?? null,
                'protocol'      => $device['protocol'] ?? 'pole_display',
                'connections'   => json_encode($device['connections'] ?? []),
                'features'      => json_encode($device['features'] ?? []),
                'specs'         => json_encode([
                    'display_type'   => $device['display_type'] ?? 'VFD',
                    'lines'          => $device['lines'] ?? 2,
                    'chars_per_line' => $device['chars_per_line'] ?? 20,
                    'encoding'       => $device['encoding'] ?? 'WPC1254',
                ]),
                'notes'         => $device['notes'] ?? null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
            $count++;
        }

        $this->command->info("✅ {$count} donanım sürücüsü yüklendi.");
    }
}
