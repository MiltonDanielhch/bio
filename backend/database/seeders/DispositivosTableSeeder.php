<?php

namespace Database\Seeders;

use App\Models\Dispositivo;
use App\Models\Sucursal;
use Illuminate\Database\Seeder;

class DispositivosTableSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener las 5 oficinas de la Gobernación del Beni
        $sucursales = Sucursal::whereHas('empresa', fn ($q) => $q->where('ruc', '10203010900015'))
            ->orderBy('id')
            ->get();

        $dispositivos = [
            /* 1 Trinidad – Casa Matriz */
            [
                'sucursal_id'        => $sucursales->firstWhere('ciudad', 'Trinidad')->id,
                'nombre_dispositivo' => 'ZK-Entrada-Principal-Trinidad',
                'tipo'               => 'huella_facial',
                'numero_serie'       => 'ZK-GOB-BEN-2024-001',
                'direccion_ip'       => '10.10.1.201',
                'ubicacion'          => 'Portón principal – Casa Matriz Trinidad',
            ],
            /* 2 Riberalta */
            [
                'sucursal_id'        => $sucursales->firstWhere('ciudad', 'Riberalta')->id,
                'nombre_dispositivo' => 'ZK-Entrada-Riberalta',
                'tipo'               => 'huella_facial',
                'numero_serie'       => 'ZK-GOB-BEN-2024-002',
                'direccion_ip'       => '10.10.2.201',
                'ubicacion'          => 'Acceso oficina provincial – Riberalta',
            ],
            /* 3 Guayaramerín */
            [
                'sucursal_id'        => $sucursales->firstWhere('ciudad', 'Guayaramerín')->id,
                'nombre_dispositivo' => 'ZK-Entrada-Guayaramerín',
                'tipo'               => 'huella_facial',
                'numero_serie'       => 'ZK-GOB-BEN-2024-003',
                'direccion_ip'       => '10.10.3.201',
                'ubicacion'          => 'Acceso oficina provincial – Guayaramerín',
            ],
            /* 4 Rurrenabaque */
            [
                'sucursal_id'        => $sucursales->firstWhere('ciudad', 'Rurrenabaque')->id,
                'nombre_dispositivo' => 'ZK-Entrada-Rurrenabaque',
                'tipo'               => 'huella_facial',
                'numero_serie'       => 'ZK-GOB-BEN-2024-004',
                'direccion_ip'       => '10.10.4.201',
                'ubicacion'          => 'Acceso oficina provincial – Rurrenabaque',
            ],
            /* 5 San Borja */
            [
                'sucursal_id'        => $sucursales->firstWhere('ciudad', 'San Borja')->id,
                'nombre_dispositivo' => 'ZK-Entrada-San-Borja',
                'tipo'               => 'huella_facial',
                'numero_serie'       => 'ZK-GOB-BEN-2024-005',
                'direccion_ip'       => '10.10.5.201',
                'ubicacion'          => 'Acceso oficina provincial – San Borja',
            ],
        ];

        foreach ($dispositivos as $d) {
            Dispositivo::firstOrCreate(
                ['numero_serie' => $d['numero_serie']],
                [
                    'sucursal_id'        => $d['sucursal_id'],
                    'nombre_dispositivo' => $d['nombre_dispositivo'],
                    'tipo'               => $d['tipo'],
                    'direccion_ip'       => $d['direccion_ip'],
                    'puerto'             => 4370,
                    'password'           => 0,
                    'ubicacion'          => $d['ubicacion'],
                    'estado'             => 'activo',
                    'ultima_conexion'    => now(),
                    'version_firmware'   => 'Ver 6.60 Apr 28 2023',
                    'creado_por'         => 1,
                    'ultimo_user_id'     => 0,
                ]
            );
        }
    }
}
