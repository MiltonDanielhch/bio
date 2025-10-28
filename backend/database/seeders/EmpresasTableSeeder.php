<?php

namespace Database\Seeders;

use App\Models\Empresa;
use Illuminate\Database\Seeder;

class EmpresasTableSeeder extends Seeder
{
    public function run(): void
    {
        Empresa::firstOrCreate(
            ['ruc' => '10203010900015'], // RUC oficial de la Gobernación del Beni
            [
                'nombre_empresa' => 'Gobernación del Beni',
                'direccion'      => 'Av. 6 de Agosto s/n, Edif. Gobernación, Trinidad',
                'telefono'       => '3-4622222',
                'email'          => 'contacto@gobernacionbeni.gob.bo',
                'logo'           => 'logos/gobernacion-beni.png', // súbelo a storage/app/public/logos
                'color_primario' => '#004d40', // verde institucional
                'estado'         => 'activo',
                'creado_por'     => 1,
            ]
        );
    }
}
