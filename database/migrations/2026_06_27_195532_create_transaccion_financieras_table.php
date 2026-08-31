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
        Schema::create('transacciones_financieras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cuenta_financiera_id')->constrained('cuentas_financieras')->onDelete('restrict');
            $table->enum('tipo', ['ingreso', 'egreso', 'transferencia']);
            $table->decimal('monto', 12, 2);
            $table->string('motivo', 255);
            $table->string('referencia', 100)->nullable(); // Nro voucher, ticket
            $table->date('fecha_transaccion');
            $table->foreignId('usuario_registra_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('cuenta_destino_id')->nullable()->constrained('cuentas_financieras')->onDelete('restrict'); // Para transferencias
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transacciones_financieras');
    }
};
