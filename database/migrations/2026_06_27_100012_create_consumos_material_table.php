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
        Schema::create('consumos_material', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_produccion_id')->constrained('ordenes_produccion')->restrictOnDelete();
            $table->foreignId('producto_id')->constrained('productos')->restrictOnDelete();
            $table->decimal('cantidad_planificada', 12, 2);
            $table->decimal('cantidad_consumida', 12, 2)->nullable();
            $table->foreignId('unidad_medida_id')->constrained('unidades_medida')->restrictOnDelete();
            $table->decimal('precio_unitario', 12, 2);
            $table->decimal('costo_total', 15, 2);
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            $table->index('orden_produccion_id');
            $table->index('producto_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consumos_material');
    }
};
