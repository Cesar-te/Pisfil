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
        Schema::create('detalle_asientos_contables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asiento_contable_id')->constrained('asientos_contables')->cascadeOnDelete();
            $table->foreignId('cuenta_contable_id')->constrained('cuentas_contables')->restrictOnDelete();
            $table->enum('tipo_movimiento', ['debe', 'haber']);
            $table->decimal('monto', 15, 2);
            $table->string('glosa', 255)->nullable();
            $table->timestamps();

            $table->index('tipo_movimiento');
            $table->index('cuenta_contable_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_asientos_contables');
    }
};
