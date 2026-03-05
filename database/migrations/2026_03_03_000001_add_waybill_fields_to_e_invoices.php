<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // type ENUM'a waybill ekle — sadece MySQL/MariaDB (SQLite ENUM desteklemiyor)
        if (\Illuminate\Support\Facades\DB::connection()->getDriverName() !== 'sqlite') {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE e_invoices MODIFY COLUMN type ENUM('invoice','return','withholding','exception','special','waybill') NOT NULL DEFAULT 'invoice'");
        }

        // E-Fatura tablosuna irsaliye alanları ekle
        Schema::table('e_invoices', function (Blueprint $table) {
            $table->string('document_type')->default('fatura')->after('direction'); // fatura, irsaliye
            $table->string('waybill_no')->nullable()->after('invoice_no');
            $table->date('shipment_date')->nullable()->after('invoice_date');
            $table->string('delivery_address')->nullable()->after('receiver_address');
            $table->string('vehicle_plate')->nullable()->after('delivery_address');
            $table->string('driver_name')->nullable()->after('vehicle_plate');
            $table->string('driver_tc')->nullable()->after('driver_name');
            $table->string('shipping_company')->nullable()->after('driver_tc');
            $table->string('tracking_no')->nullable()->after('shipping_company');

            $table->index('document_type');
        });
    }

    public function down(): void
    {
        Schema::table('e_invoices', function (Blueprint $table) {
            $table->dropColumn([
                'document_type', 'waybill_no', 'shipment_date',
                'delivery_address', 'vehicle_plate', 'driver_name',
                'driver_tc', 'shipping_company', 'tracking_no',
            ]);
        });
    }
};
