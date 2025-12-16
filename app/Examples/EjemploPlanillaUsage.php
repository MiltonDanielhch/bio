<?php
/**
 * EJEMPLO PRÁCTICO DE USO
 * Módulo de Procesamiento y Cierre de Planillas
 * 
 * Este archivo contiene ejemplos de cómo usar el módulo desde código
 */

namespace App\Examples;

use App\Models\PeriodoPlanilla;
use App\Models\ResumenMensualAsistencia;
use App\Models\Empleado;
use App\Services\PlanillaService;
use App\Jobs\ProcesarPlanillaJob;
use Carbon\Carbon;

class EjemploPlanillaUsage
{
    /**
     * EJEMPLO 1: Crear un nuevo período
     */
    public function crearPeriodo()
    {
        $periodo = PeriodoPlanilla::create([
            'empresa_id' => 1,
            'nombre' => 'Planilla Diciembre 2025',
            // El código se genera automáticamente: "2025-12"
            'fecha_inicio' => '2025-12-01',
            'fecha_fin' => '2025-12-31',
            'descripcion' => 'Cierre mensual de diciembre para cálculo de aguinaldo',
        ]);

        return $periodo;
    }

    /**
     * EJEMPLO 2: Cerrar un período (procesar asíncronamente)
     */
    public function cerrarPeriodoAsync()
    {
        $periodo = PeriodoPlanilla::find(1);

        // Verificar que se puede cerrar
        if (!$periodo->puedeCerrarse()) {
            throw new \Exception("El período no puede cerrarse en su estado actual: {$periodo->estado}");
        }

        // Lanzar el job para procesamiento en background
        ProcesarPlanillaJob::dispatch($periodo);

        // El período ahora está en estado "procesando"
        // Cuando termine, estará en "cerrado" o "error"
    }

    /**
     * EJEMPLO 3: Procesar un período de forma síncrona (para testing)
     */
    public function cerrarPeriodoSync()
    {
        $periodo = PeriodoPlanilla::find(1);
        $planillaService = app(PlanillaService::class);

        // Actualizar estado
        $periodo->update(['estado' => 'procesando']);

        try {
            // Procesar
            $planillaService->procesarPeriodo($periodo);

            // Cerrar
            $periodo->update([
                'estado' => 'cerrado',
                'fecha_cierre' => now(),
                'cerrado_por' => auth()->id(),
            ]);

            echo "Período procesado exitosamente. Total empleados: {$periodo->total_empleados_procesados}\n";

        } catch (\Exception $e) {
            $periodo->update([
                'estado' => 'error',
                'observaciones' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * EJEMPLO 4: Consultar resúmenes de un período
     */
    public function obtenerResumenes()
    {
        $periodo = PeriodoPlanilla::find(1);

        // Obtener todos los resúmenes
        $resumenes = $periodo->resumenes()->with('empleado')->get();

        foreach ($resumenes as $resumen) {
            echo "Empleado: {$resumen->empleado->nombre_completo}\n";
            echo "  - Días trabajados: {$resumen->total_dias_trabajados} / {$resumen->total_dias_laborables}\n";
            echo "  - Faltas: {$resumen->total_dias_falta}\n";
            echo "  - Tardanzas: {$resumen->total_tardanzas} ({$resumen->total_minutos_tardanza} min)\n";
            echo "  - Horas trabajadas: {$resumen->total_horas_trabajadas} hrs\n";
            echo "  - Horas extra: {$resumen->total_horas_extra} hrs\n";
            echo "  - % Asistencia: {$resumen->porcentaje_asistencia}%\n";
            echo "  - Estado: " . ($resumen->esPerfecto() ? '✅ Perfecto' : ($resumen->tieneProblemas() ? '⚠️ Con observaciones' : '✔️ Normal')) . "\n\n";
        }
    }

    /**
     * EJEMPLO 5: Filtrar empleados con problemas
     */
    public function empleadosConProblemas()
    {
        $periodo = PeriodoPlanilla::find(1);

        $problemáticos = $periodo->resumenes()
            ->where(function ($query) {
                $query->where('total_dias_falta', '>', 0)
                      ->orWhere('total_tardanzas', '>', 3);
            })
            ->with('empleado')
            ->get();

        echo "Empleados con problemas de asistencia:\n";
        foreach ($problemáticos as $resumen) {
            echo "- {$resumen->empleado->nombre_completo}: ";
            echo "{$resumen->total_dias_falta} faltas, ";
            echo "{$resumen->total_tardanzas} tardanzas\n";
        }
    }

    /**
     * EJEMPLO 6: Exportar datos para Excel
     */
    public function exportarDatos()
    {
        $periodo = PeriodoPlanilla::find(1);
        
        if ($periodo->estado !== 'cerrado') {
            throw new \Exception("Solo se pueden exportar períodos cerrados");
        }

        $data = [];
        
        foreach ($periodo->resumenes as $resumen) {
            $data[] = $resumen->toExportArray();
        }

        // Convertir a CSV
        $csv = $this->arrayToCsv($data);
        
        // Guardar o descargar
        file_put_contents(storage_path("planilla_{$periodo->codigo}.csv"), $csv);
        
        echo "Archivo exportado: planilla_{$periodo->codigo}.csv\n";
    }

    /**
     * EJEMPLO 7: Ver detalle de tardanzas de un empleado
     */
    public function detalleTardanzas()
    {
        $resumen = ResumenMensualAsistencia::where('periodo_id', 1)
            ->where('empleado_id', 5)
            ->first();

        if (!$resumen) {
            echo "No se encontró resumen para este empleado\n";
            return;
        }

        echo "Detalle de tardanzas de {$resumen->empleado->nombre_completo}:\n\n";

        foreach ($resumen->detalle_tardanzas_por_dia as $tardanza) {
            $fecha = Carbon::parse($tardanza['fecha'])->format('d/m/Y');
            echo "- {$fecha}: Llegó a las {$tardanza['hora_entrada']} ({$tardanza['minutos']} minutos tarde)\n";
        }

        echo "\nTotal: {$resumen->total_tardanzas} tardanzas, {$resumen->total_minutos_tardanza} minutos acumulados\n";
        echo "Promedio: {$resumen->promedio_tardanza} minutos por tardanza\n";
    }

    /**
     * EJEMPLO 8: Ver detalle de faltas
     */
    public function detalleFaltas()
    {
        $resumen = ResumenMensualAsistencia::where('periodo_id', 1)
            ->where('empleado_id', 5)
            ->first();

        echo "Detalle de faltas de {$resumen->empleado->nombre_completo}:\n\n";

        foreach ($resumen->detalle_faltas as $fecha) {
            $fechaFormat = Carbon::parse($fecha)->format('d/m/Y (l)');
            echo "- {$fechaFormat}\n";
        }

        echo "\nTotal: {$resumen->total_dias_falta} faltas\n";
    }

    /**
     * EJEMPLO 9: Comparar múltiples períodos
     */
    public function compararPeriodos()
    {
        $empleado = Empleado::find(5);
        
        $resumenes = ResumenMensualAsistencia::where('empleado_id', $empleado->id)
            ->with('periodo')
            ->orderBy('periodo_id', 'desc')
            ->limit(6)
            ->get();

        echo "Historial de asistencia de {$empleado->nombre_completo}:\n\n";
        echo str_pad("Período", 20) . str_pad("Asist.", 10) . str_pad("Faltas", 10) . str_pad("Tardanzas", 12) . "\n";
        echo str_repeat("-", 52) . "\n";

        foreach ($resumenes as $resumen) {
            echo str_pad($resumen->periodo->nombre, 20);
            echo str_pad("{$resumen->porcentaje_asistencia}%", 10);
            echo str_pad($resumen->total_dias_falta, 10);
            echo str_pad($resumen->total_tardanzas, 12);
            echo "\n";
        }
    }

    /**
     * EJEMPLO 10: Buscar empleados perfectos
     */
    public function empleadosPerfectos()
    {
        $periodo = PeriodoPlanilla::find(1);

        $perfectos = $periodo->resumenes()
            ->where('total_dias_falta', 0)
            ->where('total_tardanzas', 0)
            ->whereColumn('total_dias_trabajados', 'total_dias_laborables')
            ->with('empleado')
            ->get();

        echo "🏆 EMPLEADOS CON ASISTENCIA PERFECTA:\n\n";
        foreach ($perfectos as $resumen) {
            echo "✅ {$resumen->empleado->nombre_completo}\n";
            echo "   {$resumen->total_dias_trabajados} días trabajados, ";
            echo "{$resumen->total_horas_trabajadas} horas, ";
            echo "0 faltas, 0 tardanzas\n\n";
        }
    }

    /**
     * EJEMPLO 11: Estadísticas generales del período
     */
    public function estadisticasGenerales()
    {
        $periodo = PeriodoPlanilla::find(1);

        $stats = [
            'total_empleados' => $periodo->resumenes()->count(),
            'total_faltas' => $periodo->resumenes()->sum('total_dias_falta'),
            'total_tardanzas' => $periodo->resumenes()->sum('total_tardanzas'),
            'total_horas_trabajadas' => $periodo->resumenes()->sum('total_horas_trabajadas'),
            'total_horas_extra' => $periodo->resumenes()->sum('total_horas_extra_25') 
                                 + $periodo->resumenes()->sum('total_horas_extra_50')
                                 + $periodo->resumenes()->sum('total_horas_extra_100'),
            'empleados_perfectos' => $periodo->resumenes()
                ->where('total_dias_falta', 0)
                ->where('total_tardanzas', 0)
                ->count(),
            'promedio_asistencia' => $periodo->resumenes()->avg('porcentaje_asistencia'),
        ];

        echo "📊 ESTADÍSTICAS DEL PERÍODO: {$periodo->nombre}\n\n";
        echo "Total empleados: {$stats['total_empleados']}\n";
        echo "Total faltas: {$stats['total_faltas']}\n";
        echo "Total tardanzas: {$stats['total_tardanzas']}\n";
        echo "Total horas trabajadas: " . number_format($stats['total_horas_trabajadas'], 2) . " hrs\n";
        echo "Total horas extra: " . number_format($stats['total_horas_extra'], 2) . " hrs\n";
        echo "Empleados perfectos: {$stats['empleados_perfectos']}\n";
        echo "Promedio de asistencia: " . number_format($stats['promedio_asistencia'], 2) . "%\n";
    }

    /**
     * EJEMPLO 12: Reabrir un período
     */
    public function reabrirPeriodo()
    {
        $periodo = PeriodoPlanilla::find(1);

        if ($periodo->estado !== 'cerrado') {
            throw new \Exception("Solo se pueden reabrir períodos cerrados");
        }

        // Eliminar todos los resúmenes
        $periodo->resumenes()->delete();

        // Reabrir
        $periodo->update([
            'estado' => 'abierto',
            'fecha_cierre' => null,
            'cerrado_por' => null,
            'total_empleados_procesados' => 0,
        ]);

        echo "Período reabierto. Todos los resúmenes fueron eliminados.\n";
    }

    /**
     * Método auxiliar para convertir array a CSV
     */
    private function arrayToCsv($data)
    {
        if (empty($data)) {
            return '';
        }

        $output = fopen('php://temp', 'r+');
        
        // Headers
        fputcsv($output, array_keys($data[0]));
        
        // Data
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        
        return $csv;
    }
}

/**
 * CÓMO EJECUTAR ESTOS EJEMPLOS:
 * 
 * 1. Desde Tinker:
 * 
 * php artisan tinker
 * $ejemplo = new App\Examples\EjemploPlanillaUsage();
 * $ejemplo->crearPeriodo();
 * $ejemplo->obtenerResumenes();
 * 
 * 2. Desde un comando personalizado:
 * 
 * php artisan make:command PlanillaExampleCommand
 * // Luego ejecutar: php artisan planilla:example
 * 
 * 3. Desde un controlador de pruebas:
 * 
 * Route::get('/test-planilla', function() {
 *     $ejemplo = new App\Examples\EjemploPlanillaUsage();
 *     $ejemplo->estadisticasGenerales();
 * });
 */
