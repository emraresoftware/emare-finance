<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Firmalar (Tedarikçiler)
        Schema::create('firms', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->nullable()->index();
            $table->string('name');
            $table->string('tax_number')->nullable();
            $table->string('tax_office')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->decimal('balance', 14, 2)->default(0);
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Alış Faturaları
        Schema::create('purchase_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->nullable()->index();
            $table->string('invoice_type')->default('purchase'); // purchase, return
            $table->string('invoice_no')->nullable()->index();
            $table->foreignId('firm_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('waybill_no')->nullable();
            $table->string('document_no')->nullable();
            $table->string('payment_type')->default('cash');
            $table->integer('total_items')->default(0);
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->date('invoice_date')->nullable();
            $table->date('shipment_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Alış Faturası Kalemleri
        Schema::create('purchase_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name');
            $table->string('barcode')->nullable();
            $table->decimal('quantity', 12, 2)->default(1);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->timestamps();
        });

        // Gelir / Gider Türleri
        Schema::create('income_expense_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('direction'); // income, expense
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Gelirler
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->nullable()->index();
            $table->foreignId('income_expense_type_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type_name')->nullable();
            $table->text('note')->nullable();
            $table->decimal('amount', 14, 2)->default(0);
            $table->string('payment_type')->default('cash');
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->timestamps();
        });

        // Giderler
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->nullable()->index();
            $table->foreignId('income_expense_type_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type_name')->nullable();
            $table->text('note')->nullable();
            $table->decimal('amount', 14, 2)->default(0);
            $table->string('payment_type')->default('cash');
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->timestamps();
        });

        // Stok Hareketleri
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // in, out, sale, refund, transfer, count
            $table->string('barcode')->nullable();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name')->nullable();
            $table->string('transaction_code')->nullable();
            $table->text('note')->nullable();
            $table->string('firm_customer')->nullable();
            $table->string('payment_type')->nullable();
            $table->decimal('quantity', 12, 2)->default(0);
            $table->decimal('remaining', 12, 2)->default(0);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->timestamp('movement_date')->nullable();
            $table->timestamps();
        });

        // Stok Sayımları
        Schema::create('stock_counts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('draft'); // draft, completed
            $table->integer('total_items')->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('counted_at')->nullable();
            $table->timestamps();
        });

        // Stok Sayım Kalemleri
        Schema::create('stock_count_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_count_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('barcode')->nullable();
            $table->string('product_name');
            $table->decimal('system_quantity', 12, 2)->default(0);
            $table->decimal('counted_quantity', 12, 2)->default(0);
            $table->decimal('difference', 12, 2)->default(0);
            $table->timestamps();
        });

        // Personel Hareketleri
        Schema::create('staff_motions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->nullable()->constrained()->nullOnDelete();
            $table->string('staff_name')->nullable();
            $table->string('action'); // delete_item, delete_receipt, leave_page
            $table->text('description')->nullable();
            $table->string('application')->nullable();
            $table->text('detail')->nullable();
            $table->timestamp('action_date')->nullable();
            $table->timestamps();
        });

        // Ödeme Tipleri
        Schema::create('payment_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Görevler
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->default('pending'); // pending, in_progress, completed
            $table->string('priority')->default('normal'); // low, normal, high
            $table->foreignId('assigned_to')->nullable()->constrained('staff')->nullOnDelete();
            $table->date('due_date')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // Sales tablosuna eksik sütunları ekle
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'total_items')) {
                $table->integer('total_items')->default(0)->after('payment_method');
            }
            if (!Schema::hasColumn('sales', 'staff_name')) {
                $table->string('staff_name')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('sales', 'application')) {
                $table->string('application')->nullable()->after('staff_name');
            }
            if (!Schema::hasColumn('sales', 'note')) {
                $table->text('note')->nullable()->after('application');
            }
            if (!Schema::hasColumn('sales', 'discount')) {
                $table->decimal('discount', 14, 2)->default(0)->after('grand_total');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $columns = ['total_items', 'staff_name', 'application', 'note', 'discount'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('sales', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::dropIfExists('tasks');
        Schema::dropIfExists('payment_types');
        Schema::dropIfExists('staff_motions');
        Schema::dropIfExists('stock_count_items');
        Schema::dropIfExists('stock_counts');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('incomes');
        Schema::dropIfExists('income_expense_types');
        Schema::dropIfExists('purchase_invoice_items');
        Schema::dropIfExists('purchase_invoices');
        Schema::dropIfExists('firms');
    }
};
