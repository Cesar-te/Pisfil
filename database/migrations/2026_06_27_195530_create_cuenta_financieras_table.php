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
        Schema::create('cuentas_financieras', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100); // Ej: Caja Chica, BCP Soles
            $table->enum('tipo', ['caja', 'banco'])->default('banco');
            $table->string('banco', 50)->nullable(); // Solo para tipo banco
            $table->string('numero_cuenta', 50)->nullable();
            $table->string('moneda', 10)->default('PEN'); // PEN, USD
            $table->decimal('saldo_actual', 12, 2)->default(0);
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuentas_financieras');
    }
};
