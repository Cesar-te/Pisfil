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
        Schema::create('asientos_contables', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 40)->unique();
            $table->date('fecha');
            $table->string('descripcion', 255);
            $table->string('origen_tipo', 80)->nullable();
            $table->unsignedBigInteger('origen_id')->nullable();
            $table->string('moneda', 10)->default('PEN');
            $table->decimal('total_debe', 15, 2)->default(0);
            $table->decimal('total_haber', 15, 2)->default(0);
            $table->enum('estado', ['borrador', 'confirmado', 'anulado'])->default('confirmado');
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('fecha');
            $table->index(['origen_tipo', 'origen_id']);
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asientos_contables');
    }
};
