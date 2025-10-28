<?php

namespace Database\Seeders;

use App\Models\Incidencia;
use App\Models\Empleado;
use App\Models\TipoIncidencia;
use Illuminate\Database\Seeder;

class IncidenciasTableSeeder extends Seeder
{
    public function run(): void
    {
        $empleados = Empleado::with('departamento.sucursal')->where('estado', 'activo')->get();
        $tipos   = TipoIncidencia::pluck('id');

        $ciudades = $empleados->map(fn ($e) => $e->departamento->sucursal->ciudad ?? 'Trinidad')->toArray();

        $casos = [
            // Pendientes
            ['estado' => 'pendiente',   'dias' => 2, 'motivo' => 'Tardanza – lluvia intensa en %s'],
            ['estado' => 'pendiente',   'dias' => 3, 'motivo' => 'Falta – malestar estomacal (%s)'],
            ['estado' => 'pendiente',   'dias' => 1, 'motivo' => 'Salida anticipada – cita médica (%s)'],
            // Aprobadas
            ['estado' => 'aprobado',    'dias' => 5, 'motivo' => 'Permiso médico COVID-19 (%s)'],
            ['estado' => 'aprobado',    'dias' => 4, 'motivo' => 'Tardanza justificada – paro de transporte (%s)'],
            ['estado' => 'aprobado',    'dias' => 6, 'motivo' => 'Comisión oficial – capacitación (%s)'],
            // Rechazadas
            ['estado' => 'rechazado',   'dias' => 7, 'motivo' => 'Falta sin justificación (%s)'],
            ['estado' => 'rechazado',   'dias' => 8, 'motivo' => 'Tardanza – motivo no verificado (%s)'],
        ];

        foreach ($empleados as $idx => $emp) {
            $caso   = $casos[$idx];
            $ciudad = $ciudades[$idx];
            $motivo = sprintf($caso['motivo'], $ciudad);

            Incidencia::create([
                'empleado_id'        => $emp->id,
                'tipo_incidencia_id' => $tipos->random(),
                'fecha_incidencia'   => now()->subDays($caso['dias']),
                'hora_incidencia'    => $caso['estado'] === 'pendiente' ? '08:45' : null,
                'motivo'             => $motivo,
                'observaciones'      => 'Generado por seeder – Gobernación del Beni',
                'evidencia'          => in_array($caso['estado'], ['aprobado', 'rechazado'])
                    ? 'evidencia-' . $emp->id . '.pdf'
                    : null,
                'estado'             => $caso['estado'],
                'aprobado_por'       => in_array($caso['estado'], ['aprobado', 'rechazado']) ? 1 : null,
                'aprobado_en'        => in_array($caso['estado'], ['aprobado', 'rechazado'])
                    ? now()->subDays($caso['dias'] - 1)
                    : null,
                'creado_por'         => 1,
            ]);
        }

        $this->command->info('Incidencias creadas: ' . $empleados->count());
    }
}
