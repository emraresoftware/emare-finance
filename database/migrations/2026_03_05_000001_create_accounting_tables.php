<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Tekdüzen Hesap Planı ──────────────────────────────────────
        Schema::create('account_plan', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();           // 100, 120, 153.01
            $table->string('name');                          // Kasa, Alıcılar...
            $table->enum('type', [
                'asset',            // Aktif / Varlık
                'liability',        // Pasif / Yükümlülük
                'equity',           // Öz Kaynak
                'revenue',          // Gelir
                'cost',             // Satış Maliyeti
                'expense',          // Gider
            ]);
            $table->enum('normal_balance', ['debit', 'credit']); // Doğal bakiye
            $table->tinyInteger('level')->default(1);        // 1=ana 2=yardımcı 3=alt
            $table->string('parent_code', 20)->nullable();   // Üst hesap kodu
            $table->boolean('is_active')->default(true);
            $table->boolean('is_system')->default(false);    // Sistem hesabı = silinemez
            $table->timestamps();
        });

        // ── 2. Yevmiye Fişleri ──────────────────────────────────────────
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->string('entry_no', 30)->unique();        // YV-2026-001
            $table->date('date');
            $table->string('description');
            $table->enum('type', [
                'opening',    // Açılış
                'sale',       // Satış
                'purchase',   // Alış
                'expense',    // Gider
                'income',     // Tahsilat / Gelir
                'payroll',    // Bordro
                'adjustment', // Düzeltme
                'closing',    // Kapanış
                'manual',     // Manuel
            ])->default('manual');
            $table->string('reference_type')->nullable(); // App\Models\Sale vb.
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->boolean('is_posted')->default(false); // Kesinleşti mi
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['date', 'type']);
            $table->index(['reference_type', 'reference_id']);
        });

        // ── 3. Yevmiye Satırları ────────────────────────────────────────
        Schema::create('journal_entry_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained()->cascadeOnDelete();
            $table->string('account_code', 20);
            $table->string('description')->nullable();
            $table->decimal('debit', 15, 2)->default(0);    // Borç
            $table->decimal('credit', 15, 2)->default(0);   // Alacak
            $table->unsignedSmallInteger('line_order')->default(0);
            $table->timestamps();

            $table->index('account_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_entry_lines');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('account_plan');
    }
};
