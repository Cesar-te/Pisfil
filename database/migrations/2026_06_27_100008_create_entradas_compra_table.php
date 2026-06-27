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
        Schema::create('entradas_compra', function (Blueprint $table) {
            $table->id();
            $table->string('numero_documento', 50)->unique();
            $table->foreignId('proveedor_id')->constrained('proveedores')->restrictOnDelete();
            $table->dateTime('fecha_emision');
            $table->dateTime('fecha_recepcion')->nullable();
            $table->string('estado', 30)->default('pendiente'); // pendiente, recibida, validada, rechazada
            $table->text('observaciones')->nullable();
            $table->foreignId('usuario_id')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            
            $table->index('numero_documento');
            $table->index('proveedor_id');
            $table->index('estado');
            $table->index('fecha_emision');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entradas_compra');
    }
};
