<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auditorias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('accion', 80);
            $table->string('entidad_tipo', 120)->nullable();
            $table->unsignedBigInteger('entidad_id')->nullable();
            $table->json('valores_anteriores')->nullable();
            $table->json('valores_nuevos')->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamps();

            $table->index(['entidad_tipo', 'entidad_id']);
            $table->index('accion');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditorias');
    }
};
