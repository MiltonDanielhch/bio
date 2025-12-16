<?php

namespace App\Services;

use App\Models\PeriodoPlanilla;
use App\Models\ResumenMensualAsistencia;
use App\Models\Empleado;
use App\Models\RegistroAsistencia;
use App\Models\AsignacionHorario;
use App\Models\Incidencia;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PlanillaService
{
    /**
     * Procesa un periodo completo de planilla
     */
    public function procesarPeriodo(PeriodoPlanilla $periodo): bool
    {
        try {
            DB::beginTransaction();

            // Obtener todos los empleados activos de la empresa
            $empleados = Empleado::where('empresa_id', $periodo->empresa_id)
                ->where('estado', 'activo')
                ->get();

            $totalProcesados = 0;

            foreach ($empleados as $empleado) {
                $this->procesarEmpleado($periodo, $empleado);
                $totalProcesados++;
            }

            // Actualizar el periodo
            $periodo->update([
                'total_empleados_procesados' => $totalProcesados,
            ]);

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error procesando periodo {$periodo->id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Procesa un empleado específico para un periodo
     */
    public function procesarEmpleado(PeriodoPlanilla $periodo, Empleado $empleado): ResumenMensualAsistencia
    {
        // Verificar si ya existe un resumen (para evitar duplicados)
        $resumen = ResumenMensualAsistencia::firstOrNew([
            'periodo_id' => $periodo->id,
            'empleado_id' => $empleado->id,
        ]);

        // Inicializar contadores
        $datos = [
            'total_dias_laborables' => 0,
            'total_dias_trabajados' => 0,
            'total_dias_falta' => 0,
            'total_dias_incidencia' => 0,
            'total_tardanzas' => 0,
            'total_minutos_tardanza' => 0,
            'total_horas_trabajadas' => 0,
            'total_horas_programadas' => 0,
            'total_horas_extra_25' => 0,
            'total_horas_extra_50' => 0,
            'total_horas_extra_100' => 0,
            'detalle_tardanzas_por_dia' => [],
            'detalle_faltas' => [],
            'detalle_incidencias' => [],
            'primer_marcacion' => null,
            'ultima_marcacion' => null,
        ];

        // Obtener todos los registros del periodo
        $registros = RegistroAsistencia::where('empleado_id', $empleado->id)
            ->whereBetween('fecha_local', [$periodo->fecha_inicio, $periodo->fecha_fin])
            ->orderBy('fecha_hora')
            ->get()
            ->groupBy('fecha_local');

        // Obtener incidencias del periodo
        $incidencias = Incidencia::where('empleado_id', $empleado->id)
            ->whereBetween('fecha_incidencia', [$periodo->fecha_inicio, $periodo->fecha_fin])
            ->where('estado', 'aprobado')
            ->get()
            ->groupBy('fecha_incidencia');

        // Iterar por cada día del periodo
        $periodo_dias = CarbonPeriod::create($periodo->fecha_inicio, $periodo->fecha_fin);

        foreach ($periodo_dias as $fecha) {
            $fechaStr = $fecha->format('Y-m-d');
            $nombreDia = $this->traducirDia($fecha->format('l'));

            // 1. Obtener horario asignado
            $asignacion = AsignacionHorario::where('empleado_id', $empleado->id)
                ->enFecha($fechaStr)
                ->with('horario')
                ->first();

            $horario = $asignacion ? $asignacion->horario : null;
            $esDiaLaboral = $horario ? $horario->esDiaLaboral($nombreDia) : false;

            if (!$esDiaLaboral) {
                continue; // Saltar días no laborables
            }

            $datos['total_dias_laborables']++;

            // 2. Verificar si hay incidencia aprobada
            $tieneIncidencia = isset($incidencias[$fechaStr]);
            if ($tieneIncidencia) {
                $datos['total_dias_incidencia']++;
                $incidenciaDia = $incidencias[$fechaStr]->first();
                $tipoId = $incidenciaDia->tipo_incidencia_id;
                
                if (!isset($datos['detalle_incidencias'][$tipoId])) {
                    $datos['detalle_incidencias'][$tipoId] = 0;
                }
                $datos['detalle_incidencias'][$tipoId]++;
                continue; // No contar como falta ni tardanza
            }

            // 3. Obtener marcaciones del día
            $marcasDia = $registros->get($fechaStr, collect());

            if ($marcasDia->isEmpty()) {
                // FALTA
                $datos['total_dias_falta']++;
                $datos['detalle_faltas'][] = $fechaStr;
                continue;
            }

            // 4. Tiene marcaciones - calcular asistencia
            $datos['total_dias_trabajados']++;

            $entrada = $marcasDia->first();
            $salida = $marcasDia->count() > 1 ? $marcasDia->last() : null;

            // Actualizar fechas de primera y última marcación
            if (is_null($datos['primer_marcacion']) || $fechaStr < $datos['primer_marcacion']) {
                $datos['primer_marcacion'] = $fechaStr;
            }
            if (is_null($datos['ultima_marcacion']) || $fechaStr > $datos['ultima_marcacion']) {
                $datos['ultima_marcacion'] = $fechaStr;
            }

            // 5. Calcular TARDANZA
            if ($horario && $entrada) {
                $resultado = $this->calcularTardanza($entrada, $horario);
                if ($resultado['es_tardanza']) {
                    $datos['total_tardanzas']++;
                    $datos['total_minutos_tardanza'] += $resultado['minutos'];
                    $datos['detalle_tardanzas_por_dia'][] = [
                        'fecha' => $fechaStr,
                        'minutos' => $resultado['minutos'],
                        'hora_entrada' => $entrada->hora_local,
                    ];
                }
            }

            // 6. Calcular HORAS TRABAJADAS y EXTRAS
            if ($horario && $entrada && $salida) {
                $horasInfo = $this->calcularHorasTrabajadas($entrada, $salida, $horario, $nombreDia);
                $datos['total_horas_trabajadas'] += $horasInfo['horas_trabajadas'];
                $datos['total_horas_programadas'] += $horasInfo['horas_programadas'];
                $datos['total_horas_extra_25'] += $horasInfo['horas_extra_25'];
                $datos['total_horas_extra_50'] += $horasInfo['horas_extra_50'];
                $datos['total_horas_extra_100'] += $horasInfo['horas_extra_100'];
            }
        }

        // Calcular diferencia de horas
        $datos['total_horas_diferencia'] = $datos['total_horas_trabajadas'] - $datos['total_horas_programadas'];

        // Guardar resumen
        $resumen->fill($datos);
        $resumen->save();

        return $resumen;
    }

    /**
     * Calcula si hay tardanza y cuántos minutos
     */
    private function calcularTardanza($entrada, $horario): array
    {
        $horaEntradaReal = Carbon::parse($entrada->hora_local);
        $horaEntradaProgramada = Carbon::parse($horario->hora_entrada->format('H:i:s'));
        $tolerancia = $horario->tolerancia_entrada ?? 0;
        $limiteEntrada = $horaEntradaProgramada->copy()->addMinutes($tolerancia);

        $esTardanza = $horaEntradaReal->gt($limiteEntrada);
        $minutos = $esTardanza ? $horaEntradaReal->diffInMinutes($horaEntradaProgramada) : 0;

        return [
            'es_tardanza' => $esTardanza,
            'minutos' => $minutos,
        ];
    }

    /**
     * Calcula horas trabajadas y horas extras
     */
    private function calcularHorasTrabajadas($entrada, $salida, $horario, $nombreDia): array
    {
        $horaEntrada = Carbon::parse($entrada->hora_local);
        $horaSalida = Carbon::parse($salida->hora_local);
        
        // Horas reales trabajadas
        $horasTrabajadas = $horaSalida->diffInMinutes($horaEntrada) / 60;

        // Horas programadas según horario
        $horaProgramadaEntrada = Carbon::parse($horario->hora_entrada->format('H:i:s'));
        $horaProgramadaSalida = Carbon::parse($horario->hora_salida->format('H:i:s'));
        $horasProgramadas = $horaProgramadaSalida->diffInMinutes($horaProgramadaEntrada) / 60;

        // Calcular horas extra
        $horasExtra = max(0, $horasTrabajadas - $horasProgramadas);
        
        // Clasificar horas extra según normativa
        // Este cálculo puede variar según la legislación de cada país
        $horasExtra25 = 0;
        $horasExtra50 = 0;
        $horasExtra100 = 0;

        if ($horasExtra > 0) {
            // Ejemplo de clasificación (ajustar según normativa):
            // - Primeras 2 horas extra al día: 25%
            // - Siguientes horas: 50%
            // - Domingos/feriados: 100%
            
            if (in_array($nombreDia, ['Sábado', 'Domingo'])) {
                $horasExtra100 = $horasExtra;
            } elseif ($horasExtra <= 2) {
                $horasExtra25 = $horasExtra;
            } else {
                $horasExtra25 = 2;
                $horasExtra50 = $horasExtra - 2;
            }
        }

        return [
            'horas_trabajadas' => round($horasTrabajadas, 2),
            'horas_programadas' => round($horasProgramadas, 2),
            'horas_extra_25' => round($horasExtra25, 2),
            'horas_extra_50' => round($horasExtra50, 2),
            'horas_extra_100' => round($horasExtra100, 2),
        ];
    }

    /**
     * Traduce el nombre del día al español
     */
    private function traducirDia(string $diaIngles): string
    {
        $dias = [
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes',
            'Saturday' => 'Sábado',
            'Sunday' => 'Domingo',
        ];
        return $dias[$diaIngles] ?? $diaIngles;
    }

    /**
     * Exporta un periodo a Excel
     */
    public function exportarPeriodoExcel(PeriodoPlanilla $periodo)
    {
        $resumenes = $periodo->resumenes()->with('empleado.departamento')->get();
        
        $data = [];
        foreach ($resumenes as $resumen) {
            $data[] = $resumen->toExportArray();
        }

        return $data;
    }
}
