<?php

namespace Database\Seeders;

use App\Models\ReporteAsistencia;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ReportesAsistenciaSeeder extends Seeder
{
    public function run(): void
    {
        /* 1. Reporte COMPLETADO (descargable) */
        $path = 'reportes/asistencia/gobernacion_beni_semana_38_2024.xlsx';
        Storage::disk('public')->put($path, ''); // archivo vacío de prueba

        ReporteAsistencia::create([
            'empresa_id'     => 1,
            'nombre_reporte' => 'Semana 38 – Gobernación del Beni',
            'fecha_inicio'   => now()->startOfWeek()->subWeek(),
            'fecha_fin'      => now()->startOfWeek()->subWeek()->endOfWeek(),
            'tipo'           => 'semanal',
            'filtros'        => ['sucursales' => [1, 2, 3, 4, 5], 'departamentos' => [1, 2, 3, 4, 5, 6]],
            'generado_por'   => 1,
            'archivo_path'   => $path,
            'estado'         => 'completado',
        ]);

        /* 2. Reporte en PROCESO */
        ReporteAsistencia::create([
            'empresa_id'     => 1,
            'nombre_reporte' => 'Octubre 2025 – Gobernación del Beni (procesando)',
            'fecha_inicio'   => now()->startOfMonth(),
            'fecha_fin'      => now()->endOfMonth(),
            'tipo'           => 'mensual',
            'filtros'        => null,
            'generado_por'   => 1,
            'archivo_path'   => null,
            'estado'         => 'procesando',
        ]);

        /* 3. Reporte con ERROR */
        ReporteAsistencia::create([
            'empresa_id'     => 1,
            'nombre_reporte' => 'Rango personalizado – Gobernación del Beni (error)',
            'fecha_inicio'   => now()->subDays(15),
            'fecha_fin'      => now()->subDays(5),
            'tipo'           => 'custom',
            'filtros'        => ['empleados' => [1, 2, 3]],
            'generado_por'   => 1,
            'archivo_path'   => null,
            'estado'         => 'error',
            'error'          => 'Excepción: archivo temporal no generado – falta espacio en disco.',
        ]);
    }
}
