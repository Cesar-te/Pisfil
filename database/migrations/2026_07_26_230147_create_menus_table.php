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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('url')->nullable();
            $table->string('icono', 50)->nullable();
            $table->foreignId('padre_id')->nullable()->constrained('menus')->onDelete('cascade');
            $table->integer('orden')->default(0);
            $table->foreignId('permiso_id')->nullable()->constrained('permisos')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
