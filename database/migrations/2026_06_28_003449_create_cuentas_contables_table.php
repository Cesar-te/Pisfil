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
        Schema::create('cuentas_contables', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('descripcion');
            $table->string('elemento', 5); // 1, 2, 3... 0
            $table->integer('nivel'); // 2, 3, 4, 5
            $table->string('tipo', 50)->nullable(); // Activo, Pasivo, etc.
            $table->foreignId('padre_id')->nullable()->constrained('cuentas_contables')->nullOnDelete();
            $table->boolean('estado')->default(true);
            $table->timestamps();

            $table->index('codigo');
            $table->index('elemento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuentas_contables');
    }
};
