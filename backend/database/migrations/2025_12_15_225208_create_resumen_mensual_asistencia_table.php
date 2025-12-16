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
        Schema::create('resumen_mensual_asistencia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periodo_id')->constrained('periodos_planilla')->onDelete('cascade');
            $table->foreignId('empleado_id')->constrained('empleados')->onDelete('cascade');
            
            // Contadores de asistencia
            $table->integer('total_dias_laborables')->default(0); // Días que debía trabajar según horario
            $table->integer('total_dias_trabajados')->default(0); // Días con al menos una marcación
            $table->integer('total_dias_falta')->default(0); // Días sin marcación y sin incidencia justificada
            $table->integer('total_dias_incidencia')->default(0); // Días con incidencias aprobadas
            
            // Tardanzas
            $table->integer('total_tardanzas')->default(0); // Número de veces que llegó tarde
            $table->integer('total_minutos_tardanza')->default(0); // Suma total de minutos de retraso
            
            // Horas trabajadas
            $table->decimal('total_horas_trabajadas', 10, 2)->default(0); // Horas totales trabajadas
            $table->decimal('total_horas_programadas', 10, 2)->default(0); // Horas que debía trabajar
            $table->decimal('total_horas_diferencia', 10, 2)->default(0); // Diferencia (+ o -)
            
            // Horas extra (calculadas según normativa laboral)
            $table->decimal('total_horas_extra_25', 10, 2)->default(0); // HE al 25%
            $table->decimal('total_horas_extra_50', 10, 2)->default(0); // HE al 50%
            $table->decimal('total_horas_extra_100', 10, 2)->default(0); // HE al 100%
            
            // Incidencias por tipo (JSON para flexibilidad)
            $table->json('detalle_incidencias')->nullable(); // {tipo_id: cantidad}
            $table->json('detalle_tardanzas_por_dia')->nullable(); // [{fecha: '2025-08-01', minutos: 15}, ...]
            $table->json('detalle_faltas')->nullable(); // ['2025-08-05', '2025-08-12']
            
            // Fechas
            $table->date('primer_marcacion')->nullable();
            $table->date('ultima_marcacion')->nullable();
            
            // Estado y observaciones
            $table->enum('estado', ['pendiente', 'validado', 'observado', 'aprobado'])->default('pendiente');
            $table->text('observaciones')->nullable();
            $table->json('metadatos')->nullable(); // Para datos adicionales específicos de cada empresa
            
            $table->timestamps();
            
            // Índices
            $table->unique(['periodo_id', 'empleado_id']); // Un solo resumen por empleado por periodo
            $table->index('estado');
            $table->index('empleado_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resumen_mensual_asistencia');
    }
};
