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
            $table->string('userAgent');
            $table->string('language');
            $table->string('platform');
            $table->string('screenResolution');
            $table->integer('colorDepth');
            $table->string('timezone');
            $table->json('plugins');
            $table->boolean('cookiesEnabled');
            $table->integer('hardwareConcurrency');
            $table->boolean('onlineStatus');
            $table->string('viewportSize');
            $table->text('canvasFingerprint');
            $table->json('installedFonts');
            $table->string('browserName');
            $table->string('browserVersion');
            $table->json('windowSize');
            $table->boolean('localStorageAvailable');
            $table->boolean('sessionStorageAvailable');
            $table->json('cssProperties');
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
