<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('e_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->nullable()->unique();
            $table->string('invoice_no')->nullable();
            $table->string('uuid')->nullable()->unique();
            $table->enum('direction', ['outgoing', 'incoming'])->default('outgoing');
            $table->enum('type', ['invoice', 'return', 'withholding', 'exception', 'special'])->default('invoice');
            $table->enum('scenario', ['basic', 'commercial', 'export'])->default('basic');
            $table->string('status')->default('draft'); // draft, sent, accepted, rejected, cancelled
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('receiver_name')->nullable();
            $table->string('receiver_tax_number')->nullable();
            $table->string('receiver_tax_office')->nullable();
            $table->string('receiver_address')->nullable();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sale_id')->nullable()->constrained()->nullOnDelete();
            $table->string('currency')->default('TRY');
            $table->decimal('exchange_rate', 12, 4)->default(1);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('vat_total', 12, 2)->default(0);
            $table->decimal('discount_total', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);
            $table->decimal('withholding_total', 12, 2)->default(0);
            $table->integer('vat_rate')->default(20);
            $table->text('notes')->nullable();
            $table->string('payment_method')->nullable();
            $table->date('invoice_date')->nullable();
            $table->datetime('sent_at')->nullable();
            $table->datetime('received_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('e_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('e_invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name');
            $table->string('product_code')->nullable();
            $table->string('unit')->default('Adet');
            $table->decimal('quantity', 12, 3)->default(1);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->integer('vat_rate')->default(20);
            $table->decimal('vat_amount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('e_invoice_settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('tax_office')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('web')->nullable();
            $table->string('integrator')->nullable(); // foriba, edm, etc.
            $table->string('api_key')->nullable();
            $table->string('api_secret')->nullable();
            $table->string('sender_alias')->nullable();
            $table->string('receiver_alias')->nullable();
            $table->boolean('auto_send')->default(false);
            $table->boolean('is_active')->default(false);
            $table->string('default_scenario')->default('basic');
            $table->string('default_currency')->default('TRY');
            $table->integer('default_vat_rate')->default(20);
            $table->string('invoice_prefix')->nullable();
            $table->integer('invoice_counter')->default(1);
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('e_invoice_items');
        Schema::dropIfExists('e_invoices');
        Schema::dropIfExists('e_invoice_settings');
    }
};
