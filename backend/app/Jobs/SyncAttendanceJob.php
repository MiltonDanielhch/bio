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
        $startTime = now();
        $recordsProcessed = 0;
        $recordsIgnored = 0;
        $mappingFailures = [];

        try {
            // 1. Obtener datos crudos desde el microservicio
            $rawRecords = $zkService->getAttendance(
                $this->dispositivo->direccion_ip,
                $this->dispositivo->puerto,
                $this->dispositivo->password
            );

            // Actualizar estado de conexión exitosa
            $this->dispositivo->update([
                'ultima_conexion' => now(),
                'estado' => 'activo' // Asegurar que esté activo si respondió
            ]);

            if (empty($rawRecords)) {
                Log::info("No hay registros de asistencia nuevos para el dispositivo {$this->dispositivo->direccion_ip}.");
                return;
            }

            // 2. Procesar y enriquecer los registros
            foreach ($rawRecords as $rawRecord) {
                // 3. Enriquecer: Traducir uid del dispositivo a empleado_id
                $empleadoId = DB::table('dispositivo_empleado')
                    ->where('dispositivo_id', $this->dispositivo->id)
                    ->where('zk_user_id', $rawRecord['uid'])
                    ->value('empleado_id');

                // FALLBACK: Si no se encuentra por UID, intentar buscar por user_id
                if (!$empleadoId) {
                    $empleado = DB::table('empleados')
                        ->join('dispositivo_empleado', 'empleados.id', '=', 'dispositivo_empleado.empleado_id')
                        ->where('dispositivo_empleado.dispositivo_id', $this->dispositivo->id)
                        ->where(function($query) use ($rawRecord) {
                            $query->where('empleados.codigo_empleado', $rawRecord['user_id'])
                                  ->orWhere('empleados.dni', $rawRecord['user_id']);
                        })
                        ->select('empleados.id', 'dispositivo_empleado.id as pivot_id')
                        ->first();

                    if ($empleado) {
                        $empleadoId = $empleado->id;
                        DB::table('dispositivo_empleado')
                            ->where('id', $empleado->pivot_id)
                            ->update(['zk_user_id' => $rawRecord['uid']]);
                    }
                }

                if (!$empleadoId) {
                    $mappingFailures[] = $rawRecord['user_id'];
                    continue;
                }

                // 4. Mapear valores y preparar para la inserción
                $processedRecord = [
                    'empleado_id' => $empleadoId,
                    'dispositivo_id' => $this->dispositivo->id,
                    'fecha_hora' => Carbon::parse($rawRecord['timestamp']),
                    'fecha_local' => Carbon::parse($rawRecord['timestamp'])->toDateString(),
                    'hora_local' => Carbon::parse($rawRecord['timestamp'])->toTimeString(),
                    'tipo_marcaje' => $this->mapPunch($rawRecord['punch']),
                    'tipo_verificacion' => $this->mapStatus($rawRecord['status']),
                    'estado_validacion' => 'pendiente',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // 5. Inserción segura
                $inserted = DB::table('registros_asistencia')->insertOrIgnore($processedRecord);
                
                if ($inserted) {
                    $recordsProcessed++;
                } else {
                    $recordsIgnored++;
                }
            }

            // 6. Limpieza (Opcional)
            if ($this->clearAfterSync && $recordsProcessed > 0) {
                $zkService->clearAttendance($this->dispositivo->direccion_ip, $this->dispositivo->puerto, $this->dispositivo->password);
            }

            // Registro en Log del Sistema
            \App\Models\LogSistema::registrar(
                'sincronizacion_asistencia',
                "Sincronización completada para {$this->dispositivo->nombre_dispositivo}. Procesados: {$recordsProcessed}, Duplicados: {$recordsIgnored}, Fallos Mapeo: " . count($mappingFailures),
                null,
                [
                    'dispositivo_id' => $this->dispositivo->id,
                    'ip' => $this->dispositivo->direccion_ip,
                    'procesados' => $recordsProcessed,
                    'ignorados' => $recordsIgnored,
                    'fallos_mapeo' => $mappingFailures,
                    'duracion' => $startTime->diffInSeconds(now()) . 's'
                ],
                'registros_asistencia'
            );

        } catch (\Exception $e) {
            // Actualizar estado de error en el dispositivo si es posible
            Log::error("Error en SyncAttendanceJob para {$this->dispositivo->direccion_ip}: {$e->getMessage()}");
            
            \App\Models\LogSistema::registrar(
                'error_sincronizacion',
                "Fallo al sincronizar {$this->dispositivo->nombre_dispositivo}: {$e->getMessage()}",
                null,
                ['error' => $e->getMessage(), 'dispositivo_id' => $this->dispositivo->id],
                'dispositivos'
            );

            $this->fail($e);
        }
    }

    private function mapPunch(int $punch): string
    {
        return match ($punch) {
            0 => 'entrada',
            1 => 'salida',
            2 => 'entrada_almuerzo',
            3 => 'salida_almuerzo',
            default => 'general',
        };
    }

    private function mapStatus(int $status): string
    {
        return match ($status) {
            1 => 'huella',
            2 => 'tarjeta',
            3 => 'clave',
            4 => 'rostro',
            default => 'manual',
        };
    }
}
