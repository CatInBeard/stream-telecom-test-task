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
        Schema::create('visit_user_data', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('short_link_id')->constrained()->onDelete('cascade'); // Внешний ключ на модель ShortLink
            $table->string('user_agent');
            $table->string('ip_address');
            $table->uuid('session_uuid');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visit_user_data');
    }
};
