<?php

namespace Database\Seeders;

use App\Models\Person;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PeopleTableSeeder extends Seeder
{
    public function run(): void
    {
        // Admin que registra
        $admin = User::find(1);

        $ciudades = ['Trinidad', 'Riberalta', 'Guayaramerín', 'Rurrenabaque', 'San Borja', 'La Paz', 'Santa Cruz'];
        $nombres  = [
            ['n' => 'Juan Carlos',   'pa' => 'Limachi',     'ma' => 'Laura',     'gen' => 'Masculino'],
            ['n' => 'María Elena',   'pa' => 'Suárez',      'ma' => 'Vargas',    'gen' => 'Femenino'],
            ['n' => 'Luis Fernando', 'pa' => 'Aguilera',    'ma' => 'Prado',     'gen' => 'Masculino'],
            ['n' => 'Sandra Patricia','pa' => 'Melgar',     'ma' => 'Ríos',      'gen' => 'Femenino'],
            ['n' => 'Carlos Eduardo','pa' => 'Roca',        'ma' => 'Pereira',   'gen' => 'Masculino'],
            ['n' => 'Lucía Alejandra','pa' => 'Torrico',    'ma' => 'Flores',    'gen' => 'Femenino'],
            ['n' => 'Roberto Carlos','pa' => 'Paredes',     'ma' => 'Justiniano','gen' => 'Masculino'],
            ['n' => 'Ana Paola',     'pa' => 'Vaca',        'ma' => 'Guzmán',    'gen' => 'Femenino'],
        ];

        foreach ($nombres as $i => $p) {
            Person::firstOrCreate(
                ['ci' => (10234567 + $i)], // CI único
                [
                    'first_name'        => $p['n'],
                    'middle_name'       => null,
                    'paternal_surname'  => $p['pa'],
                    'maternal_surname'  => $p['ma'],
                    'birth_date'        => now()->subYears(rand(25, 45)),
                    'email'             => Str::lower(Str::slug($p['n'] . ' ' . $p['pa'], '.')) . '@gob.bo',
                    'phone'             => '3-' . rand(4600000, 4699999),
                    'address'           => 'Ciudad de ' . $ciudades[$i % count($ciudades)],
                    'gender'            => $p['gen'],
                    'image'             => null,
                    'status'            => 1,
                    'registerUser_id'   => $admin->id,
                    'registerRole'      => 'admin',
                ]
            );
        }

        $this->command->info('People creados: ' . count($nombres));
    }
}
