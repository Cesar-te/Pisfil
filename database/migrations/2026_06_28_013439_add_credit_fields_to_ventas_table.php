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
        Schema::table('ventas', function (Blueprint $table) {
            $table->string('condicion_pago', 20)->default('contado')->after('total');
            $table->string('estado_pago', 20)->default('pagado')->after('estado');
            $table->decimal('monto_cobrado', 12, 2)->default(0)->after('estado_pago');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn(['condicion_pago', 'estado_pago', 'monto_cobrado']);
        });
    }
};
