<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // scenario ENUM'a e_arsiv ekle — sadece MySQL/MariaDB
        if (\Illuminate\Support\Facades\DB::connection()->getDriverName() !== 'sqlite') {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE e_invoices MODIFY COLUMN scenario ENUM('basic','commercial','export','e_arsiv') NOT NULL DEFAULT 'basic'");
        }

        Schema::table('e_invoices', function (Blueprint $table) {
            // e-Arşiv spesifik alanlar
            $table->enum('recipient_type', ['individual', 'corporate'])->default('corporate')->after('document_type');
            $table->boolean('is_internet_sale')->default(false)->after('recipient_type');
            $table->string('internet_sale_platform')->nullable()->after('is_internet_sale');
            $table->string('internet_sale_url')->nullable()->after('internet_sale_platform');
            $table->date('payment_date')->nullable()->after('invoice_date');
            $table->string('payment_platform')->nullable()->after('payment_date');
            $table->string('tc_kimlik_no', 11)->nullable()->after('receiver_tax_number');
            $table->string('earsiv_report_no')->nullable()->after('tracking_no');

            $table->index('scenario');
            $table->index('recipient_type');
            $table->index('is_internet_sale');
        });
    }

    public function down(): void
    {
        Schema::table('e_invoices', function (Blueprint $table) {
            $table->dropIndex(['scenario']);
            $table->dropIndex(['recipient_type']);
            $table->dropIndex(['is_internet_sale']);
            $table->dropColumn([
                'recipient_type', 'is_internet_sale', 'internet_sale_platform',
                'internet_sale_url', 'payment_date', 'payment_platform',
                'tc_kimlik_no', 'earsiv_report_no',
            ]);
        });

        if (\Illuminate\Support\Facades\DB::connection()->getDriverName() !== 'sqlite') {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE e_invoices MODIFY COLUMN scenario ENUM('basic','commercial','export') NOT NULL DEFAULT 'basic'");
        }
    }
};
