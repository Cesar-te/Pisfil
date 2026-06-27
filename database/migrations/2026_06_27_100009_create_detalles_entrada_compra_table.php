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
        Schema::create('detalles_entrada_compra', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entrada_compra_id')->constrained('entradas_compra')->restrictOnDelete();
            $table->foreignId('producto_id')->constrained('productos')->restrictOnDelete();
            $table->decimal('cantidad_solicitada', 12, 2);
            $table->decimal('cantidad_recibida', 12, 2)->nullable();
            $table->decimal('precio_unitario', 12, 2);
            $table->decimal('costo_total', 15, 2);
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            $table->index('entrada_compra_id');
            $table->index('producto_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalles_entrada_compra');
    }
};
