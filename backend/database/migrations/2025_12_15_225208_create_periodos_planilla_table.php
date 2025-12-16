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
        Schema::create('periodos_planilla', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->string('nombre'); // Ej: "Planilla Agosto 2025"
            $table->string('codigo')->unique(); // Ej: "2025-08"
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->enum('estado', ['abierto', 'procesando', 'cerrado', 'error'])->default('abierto');
            $table->text('descripcion')->nullable();
            
            // Auditoría
            $table->foreignId('cerrado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('fecha_cierre')->nullable();
            $table->integer('total_empleados_procesados')->default(0);
            $table->text('observaciones')->nullable();
            $table->json('configuracion')->nullable(); // Para guardar parámetros específicos del proceso
            
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index(['empresa_id', 'estado']);
            $table->index(['fecha_inicio', 'fecha_fin']);
            $table->index('codigo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periodos_planilla');
    }
};
