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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->foreignId('categoria_id')->constrained('categorias')->restrictOnDelete();
            $table->foreignId('unidad_medida_id')->constrained('unidades_medida')->restrictOnDelete();
            $table->decimal('precio_unitario', 12, 2)->default(0);
            $table->integer('stock_minimo')->default(0);
            $table->integer('stock_maximo')->default(0);
            $table->integer('stock_actual')->default(0);
            $table->string('estado', 20)->default('activo');
            $table->text('notas')->nullable();
            $table->timestamps();
            
            $table->index('codigo');
            $table->index('categoria_id');
            $table->index('estado');
            $table->index('stock_actual');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
