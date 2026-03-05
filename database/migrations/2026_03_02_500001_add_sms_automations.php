<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Müşterilere doğum tarihi ekle
        if (!Schema::hasColumn('customers', 'birth_date')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->date('birth_date')->nullable()->after('email');
            });
        }

        // SMS Otomasyon Kuyruğu — zamanlanmış gönderimler için
        Schema::create('sms_automation_queue', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('scenario_id')->nullable()->constrained('sms_scenarios')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('template_id')->nullable()->constrained('sms_templates')->nullOnDelete();
            $table->string('phone', 20);
            $table->text('content')->nullable(); // render edilmiş mesaj
            $table->string('trigger_event');
            $table->json('variables')->nullable(); // şablon değişkenleri
            $table->timestamp('scheduled_at'); // ne zaman gönderilecek
            $table->timestamp('sent_at')->nullable();
            $table->string('status')->default('pending'); // pending, sent, failed, cancelled
            $table->string('error_message')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['status', 'scheduled_at']);
            $table->index(['customer_id', 'trigger_event']);
        });

        // SMS Otomasyon Ayarları — her otomasyon tipi için ayrı konfigürasyon
        Schema::create('sms_automation_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('automation_type')->index(); // birthday, cargo, welcome, inactivity, payment_reminder, appointment, new_year, special_day, after_sale, loyalty_milestone
            $table->string('name'); // Görüntüleme adı
            $table->foreignId('template_id')->nullable()->constrained('sms_templates')->nullOnDelete();
            $table->boolean('is_active')->default(false);
            $table->time('send_time')->default('10:00'); // Gönderim saati
            $table->integer('days_before')->default(0); // X gün önce (doğum günü için)
            $table->integer('days_after')->default(0); // X gün sonra (satış sonrası için)
            $table->integer('inactive_days')->default(30); // İnaktiflik süresi
            $table->json('conditions')->nullable(); // Ek koşullar
            $table->text('description')->nullable();
            $table->integer('sent_count')->default(0);
            $table->timestamp('last_run_at')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'automation_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_automation_configs');
        Schema::dropIfExists('sms_automation_queue');

        if (Schema::hasColumn('customers', 'birth_date')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropColumn('birth_date');
            });
        }
    }
};
