<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SMS Sağlayıcı Ayarları (NetGSM, İleti Merkezi, Twilio vb.)
        Schema::create('sms_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('provider')->default('netgsm'); // netgsm, iletimerkezi, twilio, mutlucell
            $table->string('api_key')->nullable();
            $table->string('api_secret')->nullable();
            $table->string('sender_id')->nullable(); // Başlık / Alfanumerik gönderici
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('api_url')->nullable();
            $table->decimal('balance', 12, 2)->default(0); // Kalan bakiye / kredi
            $table->boolean('is_active')->default(false);
            $table->json('extra_config')->nullable(); // Ek sağlayıcı yapılandırması
            $table->timestamps();
        });

        // SMS Şablonları
        Schema::create('sms_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name'); // Şablon adı
            $table->string('code')->index(); // welcome, order_confirm, payment_reminder vb.
            $table->text('content'); // SMS içeriği — {musteri_adi}, {firma_adi}, {tutar} vb. değişkenler
            $table->string('category')->default('general'); // general, sales, payment, loyalty, marketing
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // SMS Senaryoları (Otomatik Gönderim Kuralları)
        Schema::create('sms_scenarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name'); // Senaryo adı
            $table->string('trigger_event'); // sale_completed, payment_received, customer_birthday, loyalty_earned, campaign_start, manual, scheduled
            $table->foreignId('template_id')->nullable()->constrained('sms_templates')->nullOnDelete();
            $table->string('target_type')->default('all'); // all, customer, customer_type, segment, manual
            $table->string('customer_type_filter')->nullable(); // bireysel, kurumsal, vip vb.
            $table->foreignId('segment_id')->nullable()->constrained('customer_segments')->nullOnDelete();
            $table->json('conditions')->nullable(); // Ek koşullar: min_tutar, son_x_gun vb.
            $table->string('schedule_type')->default('immediate'); // immediate, delayed, scheduled, recurring
            $table->integer('delay_minutes')->nullable(); // Gecikme (dakika)
            $table->string('cron_expression')->nullable(); // Tekrarlayan: "0 9 * * 1" = Her Pzt 09:00
            $table->time('send_time')->nullable(); // Gönderim saati
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0); // Sıralama önceliği
            $table->timestamps();
        });

        // SMS Gönderim Logları
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('scenario_id')->nullable()->constrained('sms_scenarios')->nullOnDelete();
            $table->foreignId('template_id')->nullable()->constrained('sms_templates')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('phone', 20);
            $table->text('content'); // Gerçek gönderilen içerik
            $table->string('status')->default('pending'); // pending, sent, delivered, failed, rejected
            $table->string('provider_message_id')->nullable(); // Sağlayıcının mesaj ID'si
            $table->string('error_message')->nullable();
            $table->decimal('cost', 8, 4)->default(0); // SMS maliyeti
            $table->string('trigger_event')->nullable(); // Tetikleyen olay
            $table->json('meta')->nullable(); // Ek bilgi (sale_id, campaign_id vb.)
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['customer_id', 'created_at']);
        });

        // SMS Kara Liste (Gönderim yapılmayacak numaralar)
        Schema::create('sms_blacklist', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('phone', 20)->index();
            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_blacklist');
        Schema::dropIfExists('sms_logs');
        Schema::dropIfExists('sms_scenarios');
        Schema::dropIfExists('sms_templates');
        Schema::dropIfExists('sms_settings');
    }
};
