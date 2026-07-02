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
        Schema::table('attendants', function (Blueprint $table) {
            // Modalidad elegida por el asistente: presencial ('irl') o virtual ('online').
            $table->enum('mode', ['irl', 'online'])->default('irl')->after('conference_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendants', function (Blueprint $table) {
            $table->dropColumn('mode');
        });
    }
};
