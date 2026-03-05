<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hardware_devices', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // Kullanıcının verdiği ad
            $table->string('type');                           // receipt_printer, label_printer, barcode_scanner, scale, cash_drawer, customer_display, a4_printer
            $table->string('connection');                     // usb, serial, network, bluetooth, printer
            $table->string('protocol')->nullable();           // escpos, star, zpl, tspl, cas, keyboard_wedge, vb.
            $table->string('model')->nullable();              // Cihaz modeli
            $table->string('manufacturer')->nullable();       // Üretici

            // Bağlantı bilgileri
            $table->string('vendor_id')->nullable();          // USB Vendor ID (0x04b8 gibi)
            $table->string('product_id')->nullable();         // USB Product ID
            $table->string('ip_address')->nullable();         // Ağ bağlantısı için
            $table->integer('port')->nullable();              // TCP port (varsayılan 9100)
            $table->string('serial_port')->nullable();        // /dev/ttyUSB0 veya COM3
            $table->integer('baud_rate')->default(9600);      // Seri port baud rate
            $table->string('mac_address')->nullable();        // Bluetooth MAC adresi

            // Özel ayarlar
            $table->json('settings')->nullable();             // { paper_width: 80, char_width: 48, ... }
            $table->boolean('is_default')->default(false);    // Bu türdeki varsayılan cihaz mı?
            $table->boolean('is_active')->default(true);      // Aktif mi?
            $table->timestamp('last_seen_at')->nullable();    // Son bağlantı zamanı
            $table->string('status')->default('disconnected'); // connected, disconnected, error

            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->timestamps();

            $table->index(['type', 'is_default']);
            $table->index(['type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hardware_devices');
    }
};
