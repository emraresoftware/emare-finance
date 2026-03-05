<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Vergi Oranları ──
        Schema::create('tax_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name');                    // KDV %20, ÖTV %25 vs.
            $table->string('code');                    // KDV, OTV, OIV, DAMGA, CEVRE, KONAKLAMA
            $table->decimal('rate', 8, 4)->default(0); // Oran (yüzde)
            $table->enum('type', ['percentage', 'fixed'])->default('percentage');
            $table->string('description')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['code', 'is_active']);
        });

        // ── Hizmet Kategorileri ──
        Schema::create('service_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('service_categories')->nullOnDelete();
            $table->string('color')->nullable();       // UI renk kodu
            $table->string('icon')->nullable();        // FA icon adı
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ── Tekrarlayan Faturalar ──
        Schema::create('recurring_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('service_category_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('frequency', ['weekly', 'monthly', 'bimonthly', 'quarterly', 'semiannual', 'annual'])->default('monthly');
            $table->integer('frequency_day')->default(1);      // Ayın kaçıncı günü
            $table->string('currency')->default('TRY');
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('tax_total', 14, 2)->default(0);
            $table->decimal('discount_total', 14, 2)->default(0);
            $table->decimal('grand_total', 14, 2)->default(0);
            $table->string('payment_method')->nullable();
            $table->enum('status', ['active', 'paused', 'cancelled', 'completed'])->default('active');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('next_invoice_date')->nullable();
            $table->date('last_invoice_date')->nullable();
            $table->integer('invoices_generated')->default(0);
            $table->integer('max_invoices')->nullable();       // null = sınırsız
            $table->boolean('auto_send')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'next_invoice_date']);
        });

        // ── Tekrarlayan Fatura Kalemleri ──
        Schema::create('recurring_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recurring_invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name');
            $table->string('product_code')->nullable();
            $table->string('unit')->default('Adet');
            $table->decimal('quantity', 12, 3)->default(1);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->json('taxes')->nullable();             // [{"tax_rate_id":1,"code":"KDV","rate":20,"amount":100},...]
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->timestamps();
        });

        // ── Ürünlere ek vergi desteği ──
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'additional_taxes')) {
                $table->json('additional_taxes')->nullable()->after('vat_rate');
                // [{"tax_rate_id":5,"code":"OTV","rate":25}]
            }
            if (!Schema::hasColumn('products', 'service_category_id')) {
                $table->foreignId('service_category_id')->nullable()->after('category_id');
            }
            if (!Schema::hasColumn('products', 'is_service')) {
                $table->boolean('is_service')->default(false)->after('is_active');
            }
        });

        // ── Satış kalemlerine ek vergi desteği ──
        Schema::table('sale_items', function (Blueprint $table) {
            if (!Schema::hasColumn('sale_items', 'additional_taxes')) {
                $table->json('additional_taxes')->nullable()->after('vat_amount');
                // [{"code":"OTV","rate":25,"amount":50}]
            }
            if (!Schema::hasColumn('sale_items', 'additional_tax_amount')) {
                $table->decimal('additional_tax_amount', 12, 2)->default(0)->after('additional_taxes');
            }
        });

        // ── E-Fatura kalemlerine ek vergi desteği ──
        Schema::table('e_invoice_items', function (Blueprint $table) {
            if (!Schema::hasColumn('e_invoice_items', 'additional_taxes')) {
                $table->json('additional_taxes')->nullable()->after('vat_amount');
            }
            if (!Schema::hasColumn('e_invoice_items', 'additional_tax_amount')) {
                $table->decimal('additional_tax_amount', 12, 2)->default(0)->after('additional_taxes');
            }
        });

        // ── Satışlara ek vergi toplamı ──
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'additional_tax_total')) {
                $table->decimal('additional_tax_total', 14, 2)->default(0)->after('vat_total');
            }
        });

        // ── E-Faturalara ek vergi toplamı ──
        Schema::table('e_invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('e_invoices', 'additional_tax_total')) {
                $table->decimal('additional_tax_total', 14, 2)->default(0)->after('vat_total');
            }
        });
    }

    public function down(): void
    {
        // Ek sütunları kaldır
        Schema::table('e_invoices', function (Blueprint $table) {
            if (Schema::hasColumn('e_invoices', 'additional_tax_total')) {
                $table->dropColumn('additional_tax_total');
            }
        });

        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'additional_tax_total')) {
                $table->dropColumn('additional_tax_total');
            }
        });

        Schema::table('e_invoice_items', function (Blueprint $table) {
            $cols = ['additional_taxes', 'additional_tax_amount'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('e_invoice_items', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $cols = ['additional_taxes', 'additional_tax_amount'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('sale_items', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('products', function (Blueprint $table) {
            $cols = ['additional_taxes', 'service_category_id', 'is_service'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('products', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::dropIfExists('recurring_invoice_items');
        Schema::dropIfExists('recurring_invoices');
        Schema::dropIfExists('service_categories');
        Schema::dropIfExists('tax_rates');
    }
};
