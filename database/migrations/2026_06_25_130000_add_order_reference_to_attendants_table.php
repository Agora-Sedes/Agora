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
            // Agrupa a todos los inscriptos cargados en un mismo registro/compra.
            // Se manda a Mercado Pago como external_reference para poder ubicar a
            // todo el grupo desde el webhook (un pagador puede pagar varias entradas).
            $table->string('order_reference')->nullable()->index()->after('conference_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendants', function (Blueprint $table) {
            $table->dropColumn('order_reference');
        });
    }
};
