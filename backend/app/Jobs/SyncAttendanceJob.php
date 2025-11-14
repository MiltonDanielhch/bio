<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Carbon;
use App\Services\ZkService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Dispositivo;

class SyncAttendanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $deviceIp;
    protected bool $clearAfterSync;
    protected Dispositivo $dispositivo;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Dispositivo $dispositivo, bool $clearAfterSync = false)
    {
        $this->dispositivo = $dispositivo;
        $this->clearAfterSync = $clearAfterSync;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @param ZkService $zkService
     */
    public function handle(ZkService $zkService)
    {
        Log::info("Iniciando SyncAttendanceJob para el dispositivo: {$this->dispositivo->direccion_ip}");

        try {
            // 1. Obtener datos crudos desde el microservicio
            $rawRecords = $zkService->getAttendance(
                $this->dispositivo->direccion_ip,
                $this->dispositivo->puerto,
                $this->dispositivo->password
            );

            if (empty($rawRecords)) {
                Log::info("No hay registros de asistencia nuevos para el dispositivo {$this->dispositivo->direccion_ip}.");
                return;
            }

            // 2. Procesar y enriquecer los registros
            foreach ($rawRecords as $rawRecord) {
                // 3. Enriquecer: Traducir zk_user_id a empleado_id
                $empleadoId = DB::table('dispositivo_empleado')
                    ->where('dispositivo_id', $this->dispositivo->id)
                    ->where('zk_user_id', $rawRecord['user_id'])
                    ->value('empleado_id');

                if (!$empleadoId) {
                    Log::warning("No se encontró mapeo para zk_user_id {$rawRecord['user_id']} en el dispositivo {$this->dispositivo->direccion_ip}.");
                    continue; // Saltar si no hay mapeo
                }

                // 4. Mapear valores y preparar para la inserción
                $processedRecord = [
                    'empleado_id' => $empleadoId,
                    'dispositivo_id' => $this->dispositivo->id,
                    'fecha_hora' => Carbon::parse($rawRecord['timestamp']), // Asegúrate de que sea un objeto Carbon
                    'fecha_local' => Carbon::parse($rawRecord['timestamp'])->toDateString(),
                    'hora_local' => Carbon::parse($rawRecord['timestamp'])->toTimeString(),
                    'tipo_marcaje' => $this->mapPunch($rawRecord['punch']),
                    'tipo_verificacion' => $this->mapStatus($rawRecord['status']), // Asumiendo que 'status' se mapea a tipo_verificacion
                    // ... otros campos que necesites de la tabla registros_asistencia
                ];

                // 5. Inserción segura (usando insertOrIgnore para evitar duplicados)
                DB::table('registros_asistencia')->insertOrIgnore($processedRecord);
            }

            // 6. Limpieza (Opcional)
            if ($this->clearAfterSync) {
                $zkService->clearAttendance($this->dispositivo->direccion_ip, $this->dispositivo->puerto, $this->dispositivo->password);
                Log::info("Registros de asistencia borrados del dispositivo {$this->dispositivo->direccion_ip}.");
            }

            Log::info("SyncAttendanceJob completado para el dispositivo: {$this->dispositivo->direccion_ip}. Se procesaron " . count($rawRecords) . " registros.");

        } catch (\Exception $e) {
            Log::error("Error durante la sincronización de asistencia para {$this->dispositivo->direccion_ip}: {$e->getMessage()}");
            $this->fail($e); // Marca el job como fallido
            return;
        }
    }

    private function mapPunch(int $punch): string
    {
        switch ($punch) {
            case 0: return 'entrada';
            case 1: return 'salida';
            case 2: return 'entrada_almuerzo'; // O el que corresponda
            case 3: return 'salida_almuerzo';  // O el que corresponda
            default: return 'general';
        }
    }

    private function mapStatus(int $status): string
    {
        // Estos valores pueden variar según el modelo ZKTeco o la librería pyzk.
        // Deberías consultar la documentación de pyzk o realizar pruebas.
        switch ($status) {
            case 1: return 'huella';
            case 2: return 'tarjeta';
            case 3: return 'password';
            case 4: return 'rostro';
            default: return 'desconocido';
        }
    }
}
