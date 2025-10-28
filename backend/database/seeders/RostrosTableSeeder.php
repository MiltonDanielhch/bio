<?php

namespace Database\Seeders;

use App\Models\Rostro;
use App\Models\Empleado;
use Illuminate\Database\Seeder;

class RostrosTableSeeder extends Seeder
{
    public function run(): void
    {
        Rostro::query()->delete();

        /* 1 template facial por empleado activo */
        foreach (Empleado::where('estado', 'activo')->cursor() as $emp) {
            /* Tomamos el zk_user_id más bajo que ya tiene asignado */
            $zkUserId = \DB::table('dispositivo_empleado')
                ->where('empleado_id', $emp->id)
                ->min('zk_user_id') ?? $emp->id;

            Rostro::create([
                'empleado_id'      => $emp->id,
                'zk_user_id'       => $zkUserId,
                'template_rostro'  => \Str::random(1024), // dummy
                'foto_rostro'      => null,
                'calidad'          => 'media',
                'estado'           => 'activo',
            ]);
        }

        $this->command->info('Templates faciales creados: ' . Empleado::where('estado', 'activo')->count());
    }
}
