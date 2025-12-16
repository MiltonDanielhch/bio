<?php

namespace App\Jobs;

use App\Models\PeriodoPlanilla;
use App\Services\PlanillaService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcesarPlanillaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $periodo;

    /**
     * Número de veces que el job puede intentar ejecutarse
     */
    public $tries = 3;

    /**
     * Timeout del job en segundos
     */
    public $timeout = 1800; // 30 minutos

    /**
     * Create a new job instance.
     */
    public function __construct(PeriodoPlanilla $periodo)
    {
        $this->periodo = $periodo;
    }

    /**
     * Execute the job.
     */
    public function handle(PlanillaService $planillaService): void
    {
        try {
            Log::info("Iniciando procesamiento del periodo: {$this->periodo->id}");

            // Actualizar estado a procesando
            $this->periodo->update(['estado' => 'procesando']);

            // Procesar el periodo usando el servicio
            $planillaService->procesarPeriodo($this->periodo);

            // Actualizar estado a cerrado
            $this->periodo->update([
                'estado' => 'cerrado',
                'fecha_cierre' => now(),
                'cerrado_por' => auth()->id() ?? 1, // Usuario que lanzó el proceso
            ]);

            Log::info("Periodo {$this->periodo->id} procesado exitosamente. Total empleados: {$this->periodo->total_empleados_procesados}");

        } catch (\Exception $e) {
            Log::error("Error procesando periodo {$this->periodo->id}: " . $e->getMessage());
            Log::error($e->getTraceAsString());

            // Actualizar estado a error
            $this->periodo->update([
                'estado' => 'error',
                'observaciones' => "Error: " . $e->getMessage(),
            ]);

            // Re-lanzar la excepción para que Laravel maneje los reintentos
            $this->fail($e);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Job ProcesarPlanillaJob falló definitivamente para periodo {$this->periodo->id}");
        
        $this->periodo->update([
            'estado' => 'error',
            'observaciones' => "Error crítico después de {$this->tries} intentos: " . $exception->getMessage(),
        ]);
    }
}
