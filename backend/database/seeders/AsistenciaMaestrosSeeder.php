<?php

namespace Database\Seeders;

use App\Models\DispositivoEmpleado;
use App\Models\Empleado;
use Illuminate\Database\Seeder;

class AsistenciaMaestrosSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // 🔹 Maestros principales (orden lógico de dependencias)
            EmpresasTableSeeder::class,
            SucursalesTableSeeder::class,
            DepartamentosTableSeeder::class,
            EmpleadosTableSeeder::class,
            DispositivosTableSeeder::class,
            HorariosTableSeeder::class,
            TiposIncidenciaSeeder::class,

            // 🔹 Relaciones y operación
            AsignacionHorariosSeeder::class,
            DispositivoEmpleadoSeeder::class,
            HuellasTableSeeder::class,
            RostrosTableSeeder::class,

            // 🔹 Registros de prueba (opcional en desarrollo)
            RegistrosAsistenciaSeeder::class,
            IncidenciasTableSeeder::class,
            ReportesAsistenciaSeeder::class,
            // LogsSistemaSeeder::class,
            AsistenciaMenuAppendSeeder::class,
        ]);
    }
}
