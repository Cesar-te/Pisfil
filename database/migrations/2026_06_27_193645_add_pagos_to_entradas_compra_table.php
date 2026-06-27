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
        Schema::table('entradas_compra', function (Blueprint $table) {
            $table->enum('estado_pago', ['pendiente', 'parcial', 'pagado'])->default('pendiente')->after('estado');
            $table->decimal('monto_pagado', 10, 2)->default(0)->after('estado_pago');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entradas_compra', function (Blueprint $table) {
            $table->dropColumn(['estado_pago', 'monto_pagado']);
        });
    }
};
