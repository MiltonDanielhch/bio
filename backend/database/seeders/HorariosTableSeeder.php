<?php

namespace Database\Seeders;

use App\Models\Horario;
use Illuminate\Database\Seeder;

class HorariosTableSeeder extends Seeder
{
    public function run(): void
    {
        /* 1. Administrativo estándar (Trinidad y sedes grandes) */
        Horario::firstOrCreate(
            ['empresa_id' => 1, 'nombre_horario' => 'Administrativo Gobernación'],
            [
                'hora_entrada'           => '08:00:00',
                'hora_salida'            => '16:30:00',
                'hora_entrada_almuerzo'  => '12:00:00',
                'hora_salida_almuerzo'   => '13:00:00',
                'tolerancia_entrada'     => 10,
                'tolerancia_salida'      => 5,
                'dias_laborales'         => ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'],
                'flexible'               => false,
                'nocturno'               => false,
                'creado_por'             => 1,
            ]
        );

        /* 2. Horario reducido (oficinas provinciales pequeñas) */
        Horario::firstOrCreate(
            ['empresa_id' => 1, 'nombre_horario' => 'Horario Reducido Provincia'],
            [
                'hora_entrada'           => '08:00:00',
                'hora_salida'            => '14:00:00',
                'hora_entrada_almuerzo'  => null,
                'hora_salida_almuerzo'   => null,
                'tolerancia_entrada'     => 10,
                'tolerancia_salida'      => 5,
                'dias_laborales'         => ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'],
                'flexible'               => false,
                'nocturno'               => false,
                'creado_por'             => 1,
            ]
        );
    }
}
