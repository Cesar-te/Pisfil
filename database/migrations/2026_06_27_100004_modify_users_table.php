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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('rol_id')->nullable()->after('password')->constrained('roles')->nullOnDelete();
            $table->string('documento_identidad', 20)->nullable()->after('email');
            $table->string('telefono', 20)->nullable()->after('documento_identidad');
            $table->boolean('estado')->default(true)->after('telefono');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('rol_id');
            $table->dropColumn('documento_identidad');
            $table->dropColumn('telefono');
            $table->dropColumn('estado');
        });
    }
};
