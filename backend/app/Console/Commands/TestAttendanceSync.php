<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ZkService;
use App\Models\Dispositivo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class TestAttendanceSync extends Command
{
    protected $signature = 'test:attendance-sync {deviceId}';
    protected $description = 'Test attendance synchronization logic manually';

    public function handle(ZkService $zkService)
    {
        $deviceId = $this->argument('deviceId');
        $dispositivo = Dispositivo::find($deviceId);

        if (!$dispositivo) {
            $this->error("Dispositivo no encontrado.");
            return;
        }

        $this->info("Probando sincronización para dispositivo: {$dispositivo->nombre_dispositivo} ({$dispositivo->direccion_ip})");

        try {
            // 1. Obtener datos
            $this->info("Solicitando registros al microservicio...");
            $rawRecords = $zkService->getAttendance(
                $dispositivo->direccion_ip,
                $dispositivo->puerto,
                $dispositivo->password
            );

            if (empty($rawRecords)) {
                $this->warn("No se recibieron registros.");
                return;
            }

            $this->info("Recibidos " . count($rawRecords) . " registros.");

            // 2. Procesar
            foreach ($rawRecords as $index => $rawRecord) {
                if ($index >= 5) break; // Solo mostrar los primeros 5 para no saturar

                $this->line("------------------------------------------------");
                $this->info("Procesando registro #{$index}: UID={$rawRecord['uid']}, UserID={$rawRecord['user_id']}");

                // Buscar mapeo
                $mapping = DB::table('dispositivo_empleado')
                    ->where('dispositivo_id', $dispositivo->id)
                    ->where('zk_user_id', $rawRecord['uid']) // Probando con UID
                    ->first();

                if ($mapping) {
                    $this->info("✅ Mapeo encontrado: Empleado ID {$mapping->empleado_id}");
                } else {
                    $this->error("❌ Mapeo NO encontrado para zk_user_id (uid) = {$rawRecord['uid']}");
                    
                    // Debug: ver qué hay en la tabla para este dispositivo
                    $existing = DB::table('dispositivo_empleado')
                        ->where('dispositivo_id', $dispositivo->id)
                        ->get();
                    $this->line("  Datos en DB para este dispositivo: " . $existing->pluck('zk_user_id')->implode(', '));
                }
            }

        } catch (\Exception $e) {
            $this->error("Excepción: " . $e->getMessage());
        }
    }
}
