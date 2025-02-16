<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('additional_visit_infos', function (Blueprint $table) {
            $table->id();
            $table->string('userAgent')->nullable();
            $table->string('language')->nullable();
            $table->string('platform')->nullable();
            $table->string('screenResolution')->nullable();
            $table->integer('colorDepth')->nullable();
            $table->string('timezone')->nullable();
            $table->json('plugins')->nullable();
            $table->boolean('cookiesEnabled')->nullable();
            $table->integer('hardwareConcurrency')->nullable();
            $table->boolean('onlineStatus')->nullable();
            $table->string('viewportSize')->nullable();
            $table->text('canvasFingerprint')->nullable();
            $table->json('installedFonts')->nullable();
            $table->string('browserName')->nullable();
            $table->string('browserVersion')->nullable();
            $table->json('windowSize')->nullable();
            $table->boolean('localStorageAvailable')->nullable();
            $table->boolean('sessionStorageAvailable')->nullable();
            $table->json('cssProperties')->nullable();
            $table->uuid('visit_user_data_id')->constrained('visit_user_data')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('additional_visit_infos');
    }
};
