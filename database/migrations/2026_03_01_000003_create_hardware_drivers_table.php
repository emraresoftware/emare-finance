<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Donanım sürücü arşivi tablosu.
     * Bilinen tüm cihaz modellerini ve özelliklerini tutar.
     * hardware_devices tablosundan farklı: bu tablo "referans/katalog",
     * hardware_devices ise "aktif kullanılan cihazlar".
     */
    public function up(): void
    {
        Schema::create('hardware_drivers', function (Blueprint $table) {
            $table->id();
            $table->string('device_type');         // receipt_printer, label_printer, a4_printer, barcode_scanner, scale, cash_drawer, customer_display
            $table->string('manufacturer');         // Epson, Zebra, HP, CAS vb.
            $table->string('model');                // TM-T20II, ZD220, LaserJet Pro M404dn vb.
            $table->string('vendor_id')->nullable(); // USB Vendor ID (hex)
            $table->string('product_id')->nullable(); // USB Product ID (hex)
            $table->string('protocol');              // escpos, zpl, tspl, ipp, hid, cas, pole_display vb.
            $table->json('connections');              // ["usb","network","serial","wifi","bluetooth"]
            $table->json('features');                // ["auto_cutter","duplex","2d","label_printing",...]
            $table->json('specs');                    // Tür-bazlı özellikler (paper_width, dpi, capacity_kg vb.)
            $table->text('notes')->nullable();        // Açıklama / notlar
            $table->timestamps();

            $table->index('device_type');
            $table->index('manufacturer');
            $table->index(['vendor_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hardware_drivers');
    }
};
