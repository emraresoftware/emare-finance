<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // branches tablosuna tenant_id ve settings ekle
        Schema::table('branches', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->json('settings')->nullable()->after('is_active');
        });

        // users tablosuna tenant_id, branch_id, role_id ekle
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->after('tenant_id')->constrained()->nullOnDelete();
            $table->foreignId('role_id')->nullable()->after('branch_id')->constrained('roles')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
            $table->dropConstrainedForeignId('branch_id');
            $table->dropConstrainedForeignId('tenant_id');
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn('settings');
            $table->dropConstrainedForeignId('tenant_id');
        });
    }
};
