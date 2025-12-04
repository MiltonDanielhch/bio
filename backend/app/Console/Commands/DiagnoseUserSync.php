<?php

namespace App\Console\Commands;

use App\Models\Dispositivo;
use Illuminate\Console\Command;

class DiagnoseUserSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'diagnose:user-sync {dispositivo_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Diagnostica la configuración de sincronización de usuarios para un dispositivo';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dispositivoId = $this->argument('dispositivo_id');
        
        if ($dispositivoId) {
            $dispositivo = Dispositivo::find($dispositivoId);
            if (!$dispositivo) {
                $this->error("No se encontró el dispositivo con ID: {$dispositivoId}");
                return 1;
            }
            $this->diagnoseDevice($dispositivo);
        } else {
            // Diagnosticar todos los dispositivos
            $dispositivos = Dispositivo::all();
            foreach ($dispositivos as $dispositivo) {
                $this->diagnoseDevice($dispositivo);
                $this->newLine();
            }
        }

        return 0;
    }

    private function diagnoseDevice(Dispositivo $dispositivo)
    {
        $this->info("=== Dispositivo: {$dispositivo->nombre_dispositivo} ({$dispositivo->direccion_ip}) ===");
        
        $empleados = $dispositivo->empleados;
        
        if ($empleados->isEmpty()) {
            $this->warn("⚠ No hay empleados asignados a este dispositivo");
            return;
        }

        $this->info("Total de empleados asignados: {$empleados->count()}");
        $this->newLine();

        $hasIssues = false;
        $table = [];

        foreach ($empleados as $empleado) {
            $zkUserId = $empleado->pivot->zk_user_id;
            $privilegio = $empleado->pivot->privilegio ?? 'N/A';
            $estado = $empleado->pivot->estado_sincronizacion;
            
            $issues = [];
            
            // Verificar si zk_user_id es null o 0
            if (is_null($zkUserId) || $zkUserId === 0) {
                $issues[] = 'zk_user_id inválido';
                $hasIssues = true;
            }

            $table[] = [
                'ID Empleado' => $empleado->id,
                'Nombre' => $empleado->nombres . ' ' . $empleado->apellidos,
                'zk_user_id' => $zkUserId ?? 'NULL',
                'Privilegio' => $privilegio,
                'Estado Sincro' => $estado,
                'Problemas' => implode(', ', $issues) ?: '✓ OK'
            ];
        }

        $this->table(
            ['ID Empleado', 'Nombre', 'zk_user_id', 'Privilegio', 'Estado Sincro', 'Problemas'],
            $table
        );

        if ($hasIssues) {
            $this->error("❌ Se encontraron problemas que impedirán la sincronización");
            $this->warn("💡 Solución: Asignar zk_user_id válidos a los empleados en la tabla pivot dispositivo_empleado");
        } else {
            $this->info("✅ Todos los datos están correctamente configurados");
        }

        // Mostrar muestra del payload que se enviaría
        $this->newLine();
        $this->info("📦 Muestra de payload que se enviaría al microservicio:");
        $samplePayload = $empleados->take(2)->map(function ($empleado) {
            return [
                'uid' => $empleado->pivot->zk_user_id,
                'user_id' => (string) $empleado->id,
                'name' => $empleado->nombres . ' ' . $empleado->apellidos,
                'privilege' => $empleado->pivot->privilegio ?? 'User',
                'password' => '',
            ];
        });

        $this->line(json_encode($samplePayload->toArray(), JSON_PRETTY_PRINT));
    }
}
