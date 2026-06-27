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
        Schema::create('incidencias_reproceso', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tarea_produccion_id')->constrained('tareas_produccion')->restrictOnDelete();
            $table->string('tipo_incidencia', 50); // defecto_calidad, falta_material, equipo_averiado, otro
            $table->text('descripcion');
            $table->text('causa_raiz')->nullable();
            $table->foreignId('usuario_reporta_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('usuario_asigna_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('fecha_incidencia');
            $table->dateTime('fecha_resolucion')->nullable();
            $table->string('estado', 30)->default('abierta'); // abierta, en_revision, resuelta
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            $table->index('tarea_produccion_id');
            $table->index('estado');
            $table->index('fecha_incidencia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidencias_reproceso');
    }
};
