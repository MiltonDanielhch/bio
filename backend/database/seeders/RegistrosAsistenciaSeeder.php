<?php

namespace Database\Seeders;

use App\Models\RegistroAsistencia;
use App\Models\Empleado;
use App\Models\Dispositivo;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class RegistrosAsistenciaSeeder extends Seeder
{
    public function run(): void
    {
        $emp = Empleado::with('departamento.sucursal')->where('estado', 'activo')->get();
        $dev = Dispositivo::with('sucursal')->get()->keyBy('sucursal_id');

        $hoy = Carbon::now()->startOfDay();

        foreach ($emp as $e) {
            $suc = $e->departamento->sucursal;
            if (! $suc) {
                $suc = \App\Models\Sucursal::where('ciudad', 'Trinidad')->firstOrFail();
            }
            $disp  = $dev[$suc->id];
            $coords = [$suc->latitud, $suc->longitud];


            // 7 días hacia atrás
            foreach (range(0, 6) as $diasAtras) {
                $fecha = $hoy->copy()->subDays($diasAtras);

                /* ---------- JORNADA COMPLETA 4 MARCAS ---------- */
                $marcas = [
                    ['tipo' => 'entrada',           'hora' => '07:' . rand(40, 55), 'geo' => true],
                    ['tipo' => 'salida_almuerzo',   'hora' => '12:00',              'geo' => false],
                    ['tipo' => 'entrada_almuerzo',  'hora' => '13:0' . rand(0, 9),  'geo' => false],
                    ['tipo' => 'salida',            'hora' => '17:' . rand(25, 40), 'geo' => true],
                ];

                foreach ($marcas as $m) {
                    [$h, $min] = explode(':', $m['hora']);
                    $dt = $fecha->copy()->setTime($h, $min);

                    RegistroAsistencia::create([
                        'empleado_id'            => $e->id,
                        'dispositivo_id'         => $disp->id,
                        'tipo_marcaje'           => $m['tipo'],
                        'fecha_local'            => $dt->toDateString(),
                        'hora_local'             => $dt->format('H:i'),
                        'fecha_hora'             => $dt,
                        'tipo_verificacion'      => collect(['huella', 'rostro'])->random(),
                        'latitud'                => $m['geo'] ? $coords[0] : null,
                        'longitud'               => $m['geo'] ? $coords[1] : null,
                        'precision_ubicacion'    => $m['geo'] ? rand(3, 6) + rand(0, 99) / 100 : null,
                        'confianza_verificacion' => rand(93, 98) + rand(0, 99) / 100,
                        'procesado'              => false,
                        'incidencia_id'          => null,
                        'estado_validacion'      => 'pendiente',
                        'observaciones'          => null,
                    ]);
                }
            }
        }

        $this->command->info('Registros de asistencia creados: ' . ($emp->count() * 7 * 4));
    }
}
