<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('costos_produccion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_produccion_id')->constrained('ordenes_produccion')->cascadeOnDelete();
            $table->enum('tipo', ['mano_obra', 'gasto_indirecto', 'servicio']);
            $table->string('descripcion', 255);
            $table->decimal('monto', 15, 2);
            $table->date('fecha');
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['orden_produccion_id', 'tipo']);
            $table->index('fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('costos_produccion');
    }
};
