<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('page_url')->nullable();
            $table->string('category')->default('bug'); // bug, suggestion, question, other
            $table->string('priority')->default('normal'); // low, normal, high, critical
            $table->text('message');
            $table->text('admin_reply')->nullable();
            $table->foreignId('replied_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('replied_at')->nullable();
            $table->string('status')->default('open'); // open, in_progress, resolved, closed
            $table->string('screenshot_path')->nullable();
            $table->json('meta')->nullable(); // browser info, screen size etc.
            $table->timestamps();

            $table->index('status');
            $table->index('category');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_messages');
    }
};
