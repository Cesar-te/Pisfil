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
        Schema::table('transacciones_financieras', function (Blueprint $table) {
            // Permitir null si hay transacciones antiguas o si no siempre se usa
            $table->foreignId('cuenta_contable_id')->nullable()->constrained('cuentas_contables')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transacciones_financieras', function (Blueprint $table) {
            $table->dropForeign(['cuenta_contable_id']);
            $table->dropColumn('cuenta_contable_id');
        });
    }
};
