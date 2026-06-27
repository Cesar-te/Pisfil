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
        Schema::create('reportes_tareas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tarea_produccion_id')->constrained('tareas_produccion')->restrictOnDelete();
            $table->foreignId('usuario_id')->constrained('users')->restrictOnDelete();
            $table->dateTime('fecha_reporte');
            $table->integer('porcentaje_avance');
            $table->decimal('horas_trabajadas', 8, 2);
            $table->text('descripcion_trabajo_realizado');
            $table->text('observaciones')->nullable();
            $table->json('detalles_adicionales')->nullable();
            $table->timestamps();
            
            $table->index('tarea_produccion_id');
            $table->index('usuario_id');
            $table->index('fecha_reporte');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reportes_tareas');
    }
};
