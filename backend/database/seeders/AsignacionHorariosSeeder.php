<?php

namespace Database\Seeders;

use App\Models\AsignacionHorario;
use App\Models\Empleado;
use App\Models\Horario;
use Illuminate\Database\Seeder;

class AsignacionHorariosSeeder extends Seeder
{
    public function run(): void
    {
        $adm   = Horario::where('empresa_id', 1)
                        ->where('nombre_horario', 'Administrativo Gobernación')
                        ->firstOrFail();

        $reduc = Horario::where('empresa_id', 1)
                        ->where('nombre_horario', 'Horario Reducido Provincia')
                        ->firstOrFail();

        Empleado::with('departamento.sucursal')
            ->where('estado', 'activo')
            ->chunk(50, function ($emps) use ($adm, $reduc) {
                foreach ($emps as $emp) {
                    $ciudad = $emp->departamento->sucursal->ciudad ?? 'Trinidad';
                    $horario = $ciudad === 'Trinidad' ? $adm : $reduc;

                    AsignacionHorario::firstOrCreate(
                        ['empleado_id' => $emp->id],
                        [
                            'horario_id'   => $horario->id,
                            'fecha_inicio' => now()->subDays(7),
                            'fecha_fin'    => null,
                            'activo'       => true,
                        ]
                    );
                }
            });
    }
}
