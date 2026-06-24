<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendants', function (Blueprint $table) {
            $table->string('payment_id')->nullable()->after('conference_id');
            $table->string('registration_batch_id')->nullable()->after('payment_id');
        });
    }

    public function down(): void
    {
        Schema::table('attendants', function (Blueprint $table) {
            $table->dropColumn(['payment_id', 'registration_batch_id']);
        });
    }
};
