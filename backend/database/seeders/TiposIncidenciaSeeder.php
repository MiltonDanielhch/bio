<?php

namespace Database\Seeders;

use App\Models\TipoIncidencia;
use Illuminate\Database\Seeder;

class TiposIncidenciaSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['nombre' => 'Tardanza',                    'descripcion' => 'Llegada después del horario establecido (Art. 58 Reglamento RRHH)'],
            ['nombre' => 'Falta injustificada',         'descripcion' => 'Inasistencia sin causa legal ni justificación (Art. 60 RRHH)'],
            ['nombre' => 'Falta justificada',           'descripcion' => 'Inasistencia autorizada conforme Decreto 2026 y normas vigentes'],
            ['nombre' => 'Permiso con goce',            'descripcion' => 'Licencia o comisión con goce de haber (Art. 61 RRHH)'],
            ['nombre' => 'Permiso sin goce',            'descripcion' => 'Licencia sin goce de haber (Art. 62 RRHH)'],
            ['nombre' => 'Vacaciones',                  'descripcion' => 'Periodo de descanso anual escalafonado (30 días hábiles)'],
            ['nombre' => 'Descanso médico',             'descripcion' => 'Incapacidad temporal certificada por CNS o médico forense'],
            ['nombre' => 'Licencia maternidad',         'descripcion' => '90 días calendario post-parto (Art. 184 Código Trabajo)'],
            ['nombre' => 'Licencia paternidad',         'descripcion' => '3 días hábiles por nacimiento de hijo (Art. 185 Código Trabajo)'],
            ['nombre' => 'Comisión oficial',            'descripcion' => 'Tareas fuera de la oficina por encargo institucional con goce'],
            ['nombre' => 'Suspensión disciplinaria',    'descripcion' => 'Sanción sin goce de haber conforme Art. 64 RRHH'],
        ];

        foreach ($items as $item) {
            TipoIncidencia::firstOrCreate(
                ['nombre' => $item['nombre']],
                $item
            );
        }
    }
}
