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
        Schema::create('procesos_produccion', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->integer('duracion_estimada')->default(0);
            $table->string('duracion_estimada_unidad', 20)->default('minutos'); // minutos, horas, dias
            $table->integer('orden_secuencia')->default(0);
            $table->boolean('estado')->default(true);
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            $table->index('codigo');
            $table->index('orden_secuencia');
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procesos_produccion');
    }
};
