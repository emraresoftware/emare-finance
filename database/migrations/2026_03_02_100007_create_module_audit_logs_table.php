<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('module_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->enum('action', ['enable', 'disable', 'config_update']);
            $table->json('before')->nullable();
            $table->json('after')->nullable();
            $table->string('ip')->nullable();
            $table->datetime('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('module_audit_logs');
    }
};
