<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── Dijital Ekran İçerikleri ───
        Schema::create('signage_contents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['image', 'video', 'template', 'widget', 'url'])->default('image');
            $table->string('file_path')->nullable();
            $table->string('file_url')->nullable();
            $table->string('url')->nullable();
            $table->string('resolution')->nullable();
            $table->string('file_size')->nullable();
            $table->unsignedInteger('duration')->default(10); // saniye
            $table->json('tags')->nullable();
            $table->json('meta')->nullable();
            $table->enum('status', ['active', 'draft', 'scheduled', 'archived'])->default('draft');
            $table->timestamps();
        });

        // ─── Dijital Ekran Cihazları ───
        Schema::create('signage_devices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location')->nullable();
            $table->string('resolution')->default('1920x1080');
            $table->enum('orientation', ['landscape', 'portrait'])->default('landscape');
            $table->string('template')->default('menu-board');
            $table->string('device_type')->nullable();
            $table->string('model')->nullable();
            $table->string('os')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('mac_address')->nullable();
            $table->unsignedTinyInteger('brightness')->default(80);
            $table->unsignedTinyInteger('volume')->default(0);
            $table->boolean('auto_power')->default(false);
            $table->string('power_on')->nullable();
            $table->string('power_off')->nullable();
            $table->string('api_token', 64)->nullable()->unique();
            $table->enum('status', ['online', 'offline', 'maintenance'])->default('offline');
            $table->timestamp('last_ping_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        // ─── Playlistler ───
        Schema::create('signage_playlists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('loop')->default(true);
            $table->string('schedule_text')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        // ─── Playlist Öğeleri ───
        Schema::create('signage_playlist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('playlist_id')->constrained('signage_playlists')->cascadeOnDelete();
            $table->foreignId('content_id')->constrained('signage_contents')->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedInteger('duration_override')->nullable();
            $table->timestamps();
        });

        // ─── Cihaz-Playlist Bağlantısı ───
        Schema::create('signage_device_playlist', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('signage_devices')->cascadeOnDelete();
            $table->foreignId('playlist_id')->constrained('signage_playlists')->cascadeOnDelete();
            $table->unsignedTinyInteger('priority')->default(1);
            $table->timestamps();
        });

        // ─── Zamanlamalar ───
        Schema::create('signage_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('playlist_id')->constrained('signage_playlists')->cascadeOnDelete();
            $table->time('time_start');
            $table->time('time_end');
            $table->json('days'); // ["Pzt","Sal","Çar","Per","Cum","Cmt","Paz"]
            $table->unsignedTinyInteger('priority')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signage_schedules');
        Schema::dropIfExists('signage_device_playlist');
        Schema::dropIfExists('signage_playlist_items');
        Schema::dropIfExists('signage_playlists');
        Schema::dropIfExists('signage_devices');
        Schema::dropIfExists('signage_contents');
    }
};
