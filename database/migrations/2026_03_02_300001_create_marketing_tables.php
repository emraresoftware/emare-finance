<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── Teklifler ───
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->unsignedBigInteger('branch_id')->nullable()->index();
            $table->string('quote_number')->unique();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('customer_company')->nullable();
            $table->string('customer_tax_number')->nullable();
            $table->string('customer_address')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['draft', 'sent', 'viewed', 'accepted', 'rejected', 'expired', 'converted'])->default('draft');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax_total', 12, 2)->default(0);
            $table->decimal('discount_total', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);
            $table->string('currency', 3)->default('TRY');
            $table->date('issue_date');
            $table->date('valid_until');
            $table->text('notes')->nullable();
            $table->text('terms')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('viewed_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        // ─── Teklif Kalemleri ───
        Schema::create('quote_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quote_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('quantity', 10, 2)->default(1);
            $table->string('unit')->default('Adet');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('discount_rate', 5, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('quote_id')->references('id')->on('quotes')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
        });

        // ─── Kampanyalar ───
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->unsignedBigInteger('branch_id')->nullable()->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['discount', 'bogo', 'bundle', 'loyalty_bonus', 'free_shipping', 'gift', 'seasonal', 'flash_sale'])->default('discount');
            $table->enum('status', ['draft', 'scheduled', 'active', 'paused', 'ended', 'cancelled'])->default('draft');
            $table->enum('discount_type', ['percentage', 'fixed_amount', 'buy_x_get_y'])->nullable();
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->decimal('min_purchase_amount', 12, 2)->nullable();
            $table->decimal('max_discount_amount', 12, 2)->nullable();
            $table->integer('usage_limit')->nullable();
            $table->integer('usage_count')->default(0);
            $table->integer('per_customer_limit')->nullable();
            $table->string('coupon_code')->nullable()->unique();
            $table->json('target_products')->nullable();
            $table->json('target_categories')->nullable();
            $table->json('target_segments')->nullable();
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        // ─── Kampanya Kullanım Kaydı ───
        Schema::create('campaign_usages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('campaign_id');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('sale_id')->nullable();
            $table->decimal('discount_applied', 12, 2)->default(0);
            $table->timestamps();

            $table->foreign('campaign_id')->references('id')->on('campaigns')->cascadeOnDelete();
            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
            $table->foreign('sale_id')->references('id')->on('sales')->nullOnDelete();
        });

        // ─── Müşteri Segmentleri ───
        Schema::create('customer_segments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('color', 7)->default('#6366f1');
            $table->string('icon')->default('fa-users');
            $table->enum('type', ['manual', 'auto'])->default('manual');
            $table->json('conditions')->nullable(); // otomatik segment koşulları
            $table->integer('customer_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ─── Segment-Müşteri pivot ───
        Schema::create('customer_segment_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('segment_id');
            $table->unsignedBigInteger('customer_id');
            $table->timestamp('added_at')->useCurrent();

            $table->foreign('segment_id')->references('id')->on('customer_segments')->cascadeOnDelete();
            $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
            $table->unique(['segment_id', 'customer_id']);
        });

        // ─── Pazarlama Mesajları (E-posta / SMS) ───
        Schema::create('marketing_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->string('title');
            $table->text('content');
            $table->enum('channel', ['email', 'sms', 'whatsapp', 'push'])->default('email');
            $table->enum('status', ['draft', 'scheduled', 'sending', 'sent', 'failed', 'cancelled'])->default('draft');
            $table->unsignedBigInteger('segment_id')->nullable();
            $table->unsignedBigInteger('campaign_id')->nullable();
            $table->json('recipient_filters')->nullable();
            $table->integer('total_recipients')->default(0);
            $table->integer('sent_count')->default(0);
            $table->integer('delivered_count')->default(0);
            $table->integer('opened_count')->default(0);
            $table->integer('clicked_count')->default(0);
            $table->integer('bounced_count')->default(0);
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('segment_id')->references('id')->on('customer_segments')->nullOnDelete();
            $table->foreign('campaign_id')->references('id')->on('campaigns')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        // ─── Mesaj Gönderim Kaydı ───
        Schema::create('marketing_message_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('message_id');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('recipient');
            $table->enum('status', ['pending', 'sent', 'delivered', 'opened', 'clicked', 'bounced', 'failed'])->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->foreign('message_id')->references('id')->on('marketing_messages')->cascadeOnDelete();
            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
        });

        // ─── Sadakat Programı ───
        Schema::create('loyalty_programs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('points_per_currency', 10, 2)->default(1); // Her 1 TL = X puan
            $table->decimal('currency_per_point', 10, 4)->default(0.01); // Her puan = X TL
            $table->integer('min_redeem_points')->default(100);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ─── Müşteri Sadakat Puanları ───
        Schema::create('loyalty_points', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('loyalty_program_id')->nullable();
            $table->integer('points');
            $table->enum('type', ['earn', 'redeem', 'expire', 'bonus', 'adjustment'])->default('earn');
            $table->string('description')->nullable();
            $table->unsignedBigInteger('sale_id')->nullable();
            $table->unsignedBigInteger('campaign_id')->nullable();
            $table->integer('balance_after')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
            $table->foreign('loyalty_program_id')->references('id')->on('loyalty_programs')->nullOnDelete();
            $table->foreign('sale_id')->references('id')->on('sales')->nullOnDelete();
            $table->foreign('campaign_id')->references('id')->on('campaigns')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_points');
        Schema::dropIfExists('loyalty_programs');
        Schema::dropIfExists('marketing_message_logs');
        Schema::dropIfExists('marketing_messages');
        Schema::dropIfExists('customer_segment_members');
        Schema::dropIfExists('customer_segments');
        Schema::dropIfExists('campaign_usages');
        Schema::dropIfExists('campaigns');
        Schema::dropIfExists('quote_items');
        Schema::dropIfExists('quotes');
    }
};
