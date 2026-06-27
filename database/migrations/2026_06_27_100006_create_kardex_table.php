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
        Schema::create('kardex', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->restrictOnDelete();
            $table->string('tipo_movimiento', 20); // entrada, salida, ajuste, devolucion
            $table->decimal('cantidad', 12, 2);
            $table->decimal('precio_unitario', 12, 2);
            $table->decimal('saldo_anterior', 15, 2);
            $table->decimal('saldo_actual', 15, 2);
            $table->string('referencia_tipo', 50)->nullable();
            $table->unsignedBigInteger('referencia_id')->nullable();
            $table->foreignId('usuario_id')->constrained('users')->restrictOnDelete();
            $table->text('observaciones')->nullable();
            $table->dateTime('fecha_movimiento');
            $table->timestamps();
            
            $table->index('producto_id');
            $table->index('tipo_movimiento');
            $table->index('fecha_movimiento');
            $table->index(['referencia_tipo', 'referencia_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kardex');
    }
};
