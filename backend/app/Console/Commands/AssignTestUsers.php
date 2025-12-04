<?php

namespace App\Console\Commands;

use App\Models\Dispositivo;
use App\Models\Empleado;
use Illuminate\Console\Command;

class AssignTestUsers extends Command
{
    protected $signature = 'assign:test-users {device_id}';
    protected $description = 'Asigna usuarios de prueba a un dispositivo';

    public function handle()
    {
        $deviceId = $this->argument('device_id');
        $dispositivo = Dispositivo::find($deviceId);

        if (!$dispositivo) {
            $this->error("Dispositivo {$deviceId} no encontrado");
            return 1;
        }

        $this->info("Asignando usuarios al dispositivo: {$dispositivo->nombre_dispositivo}");

        // Buscar empleados existentes o usar IDs fijos si sabemos que existen
        $empleados = Empleado::limit(2)->get();
        
        if ($empleados->isEmpty()) {
            $this->error("No hay empleados en la base de datos");
            return 1;
        }

        $syncData = [];
        $i = 101;
        foreach ($empleados as $empleado) {
            $syncData[$empleado->id] = [
                'zk_user_id' => $i++,
                'privilegio' => 'User',
                'estado_sincronizacion' => 'pendiente'
            ];
        }

        $dispositivo->empleados()->syncWithoutDetaching($syncData);

        $this->info("✅ Se asignaron {$empleados->count()} empleados correctamente.");
        return 0;
    }
}
