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
            ['nombre' => 'Administrativo Gobernación'],
            [
                'hora_entrada'           => '08:00:00',
                'hora_salida'            => '16:30:00',
                'tolerancia_minutos'     => 10,
                'dias_laborales'         => ['lunes', 'martes', 'miercoles', 'jueves', 'viernes'],
                'creado_por'             => 1,
            ]
        );

        /* 2. Horario reducido (oficinas provinciales pequeñas) */
        Horario::firstOrCreate(
            ['nombre' => 'Horario Continuo Provincia'],
            [
                'hora_entrada'           => '08:00:00',
                'hora_salida'            => '14:00:00',
                'tolerancia_minutos'     => 10,
                'dias_laborales'         => ['lunes', 'martes', 'miercoles', 'jueves', 'viernes'],
                'creado_por'             => 1,
            ]
        );
    }
}
