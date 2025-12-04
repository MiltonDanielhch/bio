<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Empleado;
use App\Models\Dispositivo;
use Illuminate\Support\Facades\DB;

class AssignMilton extends Command
{
    protected $signature = 'assign:milton';
    protected $description = 'Asigna a Milton al dispositivo 7 para pruebas';

    public function handle()
    {
        $dni = '10824260';
        $deviceId = 7;

        $empleado = Empleado::where('dni', $dni)->first();
        if (!$empleado) {
            $this->error("Empleado con DNI $dni no encontrado.");
            return;
        }

        $dispositivo = Dispositivo::find($deviceId);
        if (!$dispositivo) {
            $this->error("Dispositivo $deviceId no encontrado.");
            return;
        }

        // Verificar si ya existe en la tabla pivot
        $exists = DB::table('dispositivo_empleado')
            ->where('empleado_id', $empleado->id)
            ->where('dispositivo_id', $dispositivo->id)
            ->exists();

        if ($exists) {
            $this->info("Milton ya está asignado al dispositivo.");
        } else {
            // Asignar
            DB::table('dispositivo_empleado')->insert([
                'empleado_id' => $empleado->id,
                'dispositivo_id' => $dispositivo->id,
                'zk_user_id' => 73, // El UID que vimos en el log del dispositivo
                'privilegio' => 'usuario',
                'estado' => 'activo',
                'estado_sincronizacion' => 'sincronizado',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->info("✅ Milton asignado correctamente al dispositivo con UID 73.");
        }
    }
}
