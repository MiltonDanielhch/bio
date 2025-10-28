<?php

namespace Database\Seeders;

use App\Models\Sucursal;
use App\Models\Empresa;
use Illuminate\Database\Seeder;

class SucursalesTableSeeder extends Seeder
{
    public function run(): void
    {
        // Obtenemos el ID de la Gobernación del Beni (única empresa)
        $gob = Empresa::where('ruc', '10203010900015')->firstOrFail();

        $oficinas = [
            [
                'nombre_sucursal' => 'Gobernación – Casa Matriz Trinidad',
                'direccion'       => 'Av. 6 de Agosto s/n, Edif. Gobernación, Trinidad',
                'ciudad'          => 'Trinidad',
                'latitud'         => -14.8333,
                'longitud'        => -64.9000,
            ],
            [
                'nombre_sucursal' => 'Gobernación – Oficina Riberalta',
                'direccion'       => 'Calle Cobija Nº 245, Riberalta',
                'ciudad'          => 'Riberalta',
                'latitud'         => -11.0000,
                'longitud'        => -66.0667,
            ],
            [
                'nombre_sucursal' => 'Gobernación – Oficina Guayaramerín',
                'direccion'       => 'Av. Mamoré Nº 789, Guayaramerín',
                'ciudad'          => 'Guayaramerín',
                'latitud'         => -10.8333,
                'longitud'        => -65.3667,
            ],
            [
                'nombre_sucursal' => 'Gobernación – Oficina Rurrenabaque',
                'direccion'       => 'Calle Comercio Nº 123, Rurrenabaque',
                'ciudad'          => 'Rurrenabaque',
                'latitud'         => -14.4333,
                'longitud'        => -67.5333,
            ],
            [
                'nombre_sucursal' => 'Gobernación – Oficina San Borja',
                'direccion'       => 'Calle Sucre Nº 456, San Borja',
                'ciudad'          => 'San Borja',
                'latitud'         => -14.8333,
                'longitud'        => -66.7500,
            ],
        ];

        foreach ($oficinas as $o) {
            Sucursal::firstOrCreate(
                [
                    'empresa_id'   => $gob->id,
                    'ciudad'       => $o['ciudad'],
                ],
                array_merge($o, [
                    'empresa_id'   => $gob->id,
                    'pais'         => 'Bolivia',
                    'zona_horaria' => 'America/La_Paz',
                    'estado'       => 'activo',
                    'creado_por'   => 1,
                ])
            );
        }
    }
}
