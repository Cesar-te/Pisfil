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
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('restrict');
            $table->string('tipo_comprobante', 20)->default('Factura');
            $table->string('serie_comprobante', 10)->nullable();
            $table->string('numero_comprobante', 20)->nullable();
            $table->date('fecha_venta');
            $table->string('moneda', 10)->default('PEN');
            $table->decimal('total', 12, 2)->default(0);
            $table->enum('estado', ['borrador', 'pagada', 'anulada'])->default('borrador');
            $table->foreignId('cuenta_financiera_id')->nullable()->constrained('cuentas_financieras')->onDelete('restrict');
            $table->foreignId('usuario_registra_id')->constrained('users')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
