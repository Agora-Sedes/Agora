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
        Schema::create('stream_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendant_id')->constrained('attendants')->cascadeOnDelete();
            $table->foreignId('conference_id')->constrained('conferences')->cascadeOnDelete();
            // Identifica al dispositivo/navegador dueño de la sesión activa.
            $table->string('session_token', 64);
            $table->timestamps();

            // Una única sesión activa por asistente.
            $table->unique('attendant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stream_sessions');
    }
};
