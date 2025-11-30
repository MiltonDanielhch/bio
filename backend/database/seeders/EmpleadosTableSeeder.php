<?php

namespace Database\Seeders;

use App\Models\Empleado;
use App\Models\Departamento;
use Illuminate\Database\Seeder;

class EmpleadosTableSeeder extends Seeder
{
    public function run(): void
    {
        $deptos = Departamento::pluck('id')->toArray();
        if (empty($deptos)) {
            $this->command->warn('⚠️ No hay departamentos. Cancelando EmpleadosTableSeeder.');
            return;
        }

        /* 8 empleados repartidos en las 5 oficinas de la Gobernación */
        $empleados = [
            // Trinidad – RRHH
            ['nombres' => 'Carlos Eduardo',  'apellidos' => 'Roca Pereira',     'dni' => '10234567', 'codigo' => 'BEN-001', 'depto' => 0],
            ['nombres' => 'María Fernanda',  'apellidos' => 'Suárez Vargas',    'dni' => '10234568', 'codigo' => 'BEN-002', 'depto' => 0],
            // Trinidad – Planificación
            ['nombres' => 'Jorge Luis',      'apellidos' => 'Ribera Aguilera',  'dni' => '10234569', 'codigo' => 'BEN-003', 'depto' => 1],
            // Riberalta
            ['nombres' => 'Lucía Alejandra', 'apellidos' => 'Torrico Flores',   'dni' => '10234570', 'codigo' => 'RIB-001', 'depto' => 2],
            // Guayaramerín
            ['nombres' => 'Ana Paola',       'apellidos' => 'Vaca Guzmán',      'dni' => '10234572', 'codigo' => 'GUA-001', 'depto' => 3],
            // Rurrenabaque
            ['nombres' => 'Miguel Ángel',    'apellidos' => 'Choque López',     'dni' => '10234573', 'codigo' => 'RUR-001', 'depto' => 4],
            // San Borja
            ['nombres' => 'Sandra Patricia', 'apellidos' => 'Melgar Ríos',      'dni' => '10234574', 'codigo' => 'SBJ-001', 'depto' => 5],
            ['nombres' => 'Luis Fernando',   'apellidos' => 'Aguilera Prado',   'dni' => '10234575', 'codigo' => 'SBJ-002', 'depto' => 5],
        ];

        foreach ($empleados as $e) {
            Empleado::create([
                'empresa_id'        => 1,                    // Gobernación del Beni
                'departamento_id'   => $deptos[$e['depto']],
                'codigo_empleado'   => $e['codigo'],
                'dni'               => $e['dni'],
                'nombres'           => $e['nombres'],
                'apellidos'         => $e['apellidos'],
                'fecha_nacimiento'  => now()->subYears(rand(25, 45)),
                'genero'            => collect(['M', 'F'])->random(),
                'email'             => strtolower(str_replace(' ', '.', $e['nombres'] . '.' . $e['apellidos'])) . '@gobiernobeni.gob.bo',
                'telefono'          => '3-' . rand(4600000, 4699999),
                'direccion'         => 'Ciudad de ' . explode('-', $e['codigo'])[0],
                'fecha_contratacion'=> now()->subMonths(rand(1, 60)),
                'tipo_contrato'     => collect(['indefinido', 'plazo_fijo'])->random(),
                'estado'            => 'activo',
                'foto_perfil'       => null,
                'user_id'           => null,
                'creado_por'        => 1,
            ]);
        }
    }
}
