<?php

namespace Database\Seeders;

use App\Models\Departamento;
use App\Models\Sucursal;
use Illuminate\Database\Seeder;

class DepartamentosTableSeeder extends Seeder
{
    public function run(): void
    {
        // Mapeo ciudad → sucursal ya creada
        $sucursales = Sucursal::whereHas('empresa', fn ($q) => $q->where('ruc', '10203010900015'))
            ->pluck('id', 'ciudad');

        $departamentos = [
            /* TRINIDAD (casa matriz) */
            [
                'ciudad'                => 'Trinidad',
                'nombre_departamento'   => 'Recursos Humanos',
                'descripcion'           => 'Gestión de personal, nómina y asistencia.',
            ],
            [
                'ciudad'                => 'Trinidad',
                'nombre_departamento'   => 'Planificación y Desarrollo',
                'descripcion'           => 'Planificación estratégica, proyectos y estadística.',
            ],
            [
                'ciudad'                => 'Trinidad',
                'nombre_departamento'   => 'Tesorería General',
                'descripcion'           => 'Control de ingresos, egresos y finanzas públicas.',
            ],

            /* RIBERALTA */
            [
                'ciudad'                => 'Riberalta',
                'nombre_departamento'   => 'Oficina de Apoyo Provincial Vaca Díez',
                'descripcion'           => 'Coordinación de programas sociales y obras en la provincia.',
            ],

            /* GUAYARAMERÍN */
            [
                'ciudad'                => 'Guayaramerín',
                'nombre_departamento'   => 'Oficina de Apoyo Provincial Mamoré',
                'descripcion'           => 'Gestión de infraestructura y servicios públicos.',
            ],

            /* RURRENABAQUE */
            [
                'ciudad'                => 'Rurrenabaque',
                'nombre_departamento'   => 'Oficina de Apoyo Provincial General José Ballivián',
                'descripcion'           => 'Promoción turística y mantenimiento vial.',
            ],

            /* SAN BORJA */
            [
                'ciudad'                => 'San Borja',
                'nombre_departamento'   => 'Oficina de Apoyo Provincial Marbán',
                'descripcion'           => 'Apoyo agropecuario y medio ambiente.',
            ],
        ];

        foreach ($departamentos as $d) {
            Departamento::firstOrCreate(
                [
                    'sucursal_id'         => $sucursales[$d['ciudad']],
                    'nombre_departamento' => $d['nombre_departamento'],
                ],
                [
                    'descripcion'      => $d['descripcion'],
                    'jefe_empleado_id' => null, // se asignará después
                    'creado_por'       => 1,
                ]
            );
        }
    }
}
