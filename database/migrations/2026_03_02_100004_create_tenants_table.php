<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique()->nullable();
            $table->enum('status', ['active', 'suspended', 'cancelled'])->default('active');
            $table->foreignId('plan_id')->nullable()->constrained()->nullOnDelete();
            $table->datetime('trial_ends_at')->nullable();
            $table->string('billing_email')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
