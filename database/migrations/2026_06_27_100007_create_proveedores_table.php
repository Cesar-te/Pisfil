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
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('nombre_empresa', 150);
            $table->string('nombre_contacto', 100)->nullable();
            $table->string('documento_identidad', 30)->nullable();
            $table->string('ruc', 15)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('celular', 20)->nullable();
            $table->text('direccion')->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->string('pais', 100)->default('Perú');
            $table->string('condicion_pago', 50)->nullable();
            $table->integer('plazo_entrega')->nullable(); // en días
            $table->boolean('estado')->default(true);
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            $table->index('codigo');
            $table->index('ruc');
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proveedores');
    }
};
