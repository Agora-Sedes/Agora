<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conference_id')->constrained('conferences')->cascadeOnDelete();
            $table->string('body', 280);
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->index(['conference_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
