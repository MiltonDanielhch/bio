<?php

namespace Database\Seeders;

use App\Models\DispositivoEmpleado;
use App\Models\Empleado;
use App\Models\Dispositivo;
use Illuminate\Database\Seeder;

class DispositivoEmpleadoSeeder extends Seeder
{
    public function run(): void
    {
        /* ----------------------------------------------------------
         * 1 dispositivo por oficina (IDs 1-5) y los empleados
         * que ya están creados en esas 5 sucursales.
         * ---------------------------------------------------------- */

        // Trinidad (2 dispositivos como ejemplo)
        $this->asignar(1, [1, 2]);   // zk_user_id 1,2
        $this->asignar(2, [3, 4]);   // zk_user_id 3,4

        // Riberalta
        $this->asignar(3, [5]);

        // Guayaramerín
        $this->asignar(4, [6]);

        // Rurrenabaque
        $this->asignar(5, [7]);

        // San Borja
        $this->asignar(5, [8]);
    }

    /* ------------------------------------------------------------------
     * Helper: crea el registro y asigna zk_user_id secuencial
     * ------------------------------------------------------------------ */
    private function asignar(int $dispositivoId, array $empleadosIds): void
    {
        static $zkUserId = 1;

        foreach ($empleadosIds as $empId) {
            DispositivoEmpleado::firstOrCreate(
                [
                    'empleado_id'    => $empId,
                    'dispositivo_id' => $dispositivoId,
                ],
                [
                    'zk_user_id'            => $zkUserId++,
                    'privilegio'            => 'usuario',
                    'tarjeta_id'            => null,
                    'estado'                => 'activo',
                    'estado_sincronizacion' => 'pendiente',
                    'ultima_sincronizacion' => null,
                ]
            );
        }
    }
}
