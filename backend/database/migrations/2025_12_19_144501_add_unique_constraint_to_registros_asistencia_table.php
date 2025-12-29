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
        Schema::table('registros_asistencia', function (Blueprint $table) {
            // Evitar duplicados basados en el empleado, el dispositivo y el momento exacto
            $table->unique(['empleado_id', 'dispositivo_id', 'fecha_hora'], 'unique_asistencia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registros_asistencia', function (Blueprint $table) {
            $table->dropUnique('unique_asistencia');
        });
    }
};
