<?php

namespace App\Jobs;

use App\Models\ReporteAsistencia;
use App\Models\RegistroAsistencia;
use App\Models\Empleado;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class GenerarReporteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $reporte;

    public function __construct(ReporteAsistencia $reporte)
    {
        $this->reporte = $reporte;
    }

    public function handle()
    {
        try {
            $this->reporte->update(['estado' => 'procesando']);

            $registros = RegistroAsistencia::with(['empleado', 'dispositivo'])
                ->whereHas('empleado', function ($q) {
                    $q->where('empresa_id', $this->reporte->empresa_id);
                })
                ->whereBetween('fecha_local', [$this->reporte->fecha_inicio, $this->reporte->fecha_fin])
                ->orderBy('empleado_id')
                ->orderBy('fecha_hora')
                ->get()
                ->groupBy('empleado_id');

            $data = $this->procesarAsistencia($registros);

            $pdf = Pdf::loadView('admin.reportes.pdf_template', [
                'reporte' => $this->reporte,
                'data' => $data
            ]);

            $fileName = 'reportes/' . \Str::slug($this->reporte->nombre_reporte) . '.pdf';
            Storage::put($fileName, $pdf->output());

            $this->reporte->update([
                'estado' => 'completado',
                'archivo_path' => $fileName,
                'fecha_generacion' => now(),
            ]);

        } catch (\Exception $e) {
            Log::error("Error generando reporte {$this->reporte->id}: " . $e->getMessage());
            $this->reporte->update(['estado' => 'error']);
            $this->fail($e);
        }
    }

    private function procesarAsistencia($registrosAgrupados)
    {
        $resultado = [];
        $periodo = \Carbon\CarbonPeriod::create($this->reporte->fecha_inicio, $this->reporte->fecha_fin);

        // Obtener todos los empleados del reporte (incluso los que no marcaron)
        $empleadosIds = \App\Models\Empleado::where('empresa_id', $this->reporte->empresa_id)
            ->where('estado', 'activo')
            ->pluck('id');

        foreach ($empleadosIds as $empleadoId) {
            $empleado = \App\Models\Empleado::find($empleadoId);
            $registrosEmpleado = $registrosAgrupados->get($empleadoId, collect());
            
            $resumenEmpleado = [
                'empleado' => $empleado,
                'dias' => []
            ];

            foreach ($periodo as $date) {
                $fechaStr = $date->format('Y-m-d');
                $nombreDia = $this->traducirDia($date->format('l')); // Monday -> Lunes

                // 1. Buscar Horario Asignado para esta fecha
                $asignacion = \App\Models\AsignacionHorario::where('empleado_id', $empleadoId)
                    ->enFecha($fechaStr)
                    ->with('horario')
                    ->first();

                $horario = $asignacion ? $asignacion->horario : null;
                $esDiaLaboral = $horario ? $horario->esDiaLaboral($nombreDia) : false;

                // 2. Obtener marcas del día
                $marcas = $registrosEmpleado->where('fecha_local', $fechaStr);
                $entrada = $marcas->first();
                $salida = $marcas->count() > 1 ? $marcas->last() : null;

                // 3. Calcular Estado
                $estado = 'N/A';
                $detalles = '';

                if ($esDiaLaboral) {
                    if ($marcas->isEmpty()) {
                        $estado = 'Falta';
                    } else {
                        // Verificar Atraso
                        if ($entrada && $horario) {
                            $horaEntradaReal = Carbon::parse($entrada->hora_local);
                            // Hora entrada esperada
                            $horaEntradaProg = Carbon::parse($horario->hora_entrada->format('H:i:s'));
                            // Límite con tolerancia
                            $limiteEntrada = $horaEntradaProg->copy()->addMinutes($horario->tolerancia_entrada ?? 0);

                            if ($horaEntradaReal->gt($limiteEntrada)) {
                                $estado = 'Atraso';
                                $minutosAtraso = $horaEntradaReal->diffInMinutes($horaEntradaProg);
                                $detalles = "({$minutosAtraso} min)";
                            } else {
                                $estado = 'Puntual';
                            }
                        } else {
                            $estado = 'Presente'; // Si hay marca pero no horario configurado
                        }
                    }
                } else {
                    $estado = 'Libre'; // Día no laboral o sin horario
                }

                // 4. Calcular Horas Trabajadas
                $horasTrabajadas = 0;
                if ($entrada && $salida) {
                    $horasTrabajadas = Carbon::parse($salida->hora_local)->diffInHours(Carbon::parse($entrada->hora_local));
                }

                $resumenEmpleado['dias'][$fechaStr] = [
                    'dia_semana' => $nombreDia,
                    'entrada' => $entrada ? $entrada->hora_local : '--:--',
                    'salida' => $salida ? $salida->hora_local : '--:--',
                    'horas' => $horasTrabajadas > 0 ? number_format($horasTrabajadas, 2) : '-',
                    'estado' => $estado,
                    'detalles' => $detalles
                ];
            }
            $resultado[] = $resumenEmpleado;
        }

        return $resultado;
    }

    private function traducirDia($diaIngles)
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
}
