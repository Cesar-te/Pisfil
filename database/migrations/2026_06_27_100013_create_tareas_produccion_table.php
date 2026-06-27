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
        Schema::create('tareas_produccion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_produccion_id')->constrained('ordenes_produccion')->restrictOnDelete();
            $table->string('numero_tarea', 50);
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->foreignId('proceso_produccion_id')->nullable()->constrained('procesos_produccion')->nullOnDelete();
            $table->string('estado', 30)->default('pendiente'); // pendiente, en_progreso, completada, reproceso
            $table->dateTime('fecha_inicio_planificada');
            $table->dateTime('fecha_fin_planificada');
            $table->dateTime('fecha_inicio_real')->nullable();
            $table->dateTime('fecha_fin_real')->nullable();
            $table->foreignId('usuario_responsable_id')->constrained('users')->restrictOnDelete();
            $table->integer('porcentaje_avance')->default(0);
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            $table->index('orden_produccion_id');
            $table->index('estado');
            $table->index('usuario_responsable_id');
            $table->unique(['orden_produccion_id', 'numero_tarea']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tareas_produccion');
    }
};
