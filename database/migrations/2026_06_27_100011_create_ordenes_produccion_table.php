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
        Schema::create('ordenes_produccion', function (Blueprint $table) {
            $table->id();
            $table->string('numero_orden', 50)->unique();
            $table->string('cliente', 150);
            $table->text('descripcion_trabajo');
            $table->string('estado', 30)->default('planificada'); // planificada, en_proceso, pausada, completada, cancelada
            $table->dateTime('fecha_inicio_planificada');
            $table->dateTime('fecha_fin_planificada');
            $table->dateTime('fecha_inicio_real')->nullable();
            $table->dateTime('fecha_fin_real')->nullable();
            $table->text('observaciones')->nullable();
            $table->foreignId('usuario_creador_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('usuario_asignado_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index('numero_orden');
            $table->index('estado');
            $table->index('fecha_inicio_planificada');
            $table->index('usuario_asignado_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordenes_produccion');
    }
};
