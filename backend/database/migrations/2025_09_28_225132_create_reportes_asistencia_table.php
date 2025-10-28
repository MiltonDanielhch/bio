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
        Schema::create('reportes_asistencia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->string('nombre_reporte');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->enum('tipo', ['diario', 'semanal', 'mensual', 'custom']);
            $table->json('filtros')->nullable();
            $table->foreignId('generado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->string('archivo_path')->nullable();
            $table->enum('estado', ['procesando', 'completado', 'error'])->default('procesando');
            $table->text('error')->nullable();
            $table->timestamps();

            $table->index(['empresa_id', 'fecha_inicio']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reportes_asistencia');
    }
};
