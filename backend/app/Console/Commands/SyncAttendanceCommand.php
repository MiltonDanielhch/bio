<?php

namespace App\Console\Commands;

use App\Jobs\SyncAttendanceJob;
use App\Models\Dispositivo; // Importa el modelo Dispositivo
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncAttendanceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zk:sync-attendance {ip?} {--clear}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza los registros de asistencia desde los dispositivos ZKTeco.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $ip = $this->argument('ip');
        $clear = $this->option('clear');

        if ($ip) {
            // Si se proporciona una IP, sincroniza solo ese dispositivo
            $dispositivo = Dispositivo::where('direccion_ip', $ip)->first();

            if ($dispositivo) {
                SyncAttendanceJob::dispatch($dispositivo, $clear);
                $this->info("Sincronización de asistencia iniciada para el dispositivo con IP: {$ip}.");
            } else {
                $this->error("No se encontró ningún dispositivo con la IP: {$ip}.");
            }
        } else {
            // Si no se proporciona una IP, sincroniza todos los dispositivos activos
            $dispositivos = Dispositivo::where('estado', 'activo')->get(); // Usar el scope 'activos' o el campo 'estado'

            if ($dispositivos->count() > 0) {
                foreach ($dispositivos as $dispositivo) {
                    SyncAttendanceJob::dispatch($dispositivo, $clear);
                }
                $this->info("Sincronización de asistencia iniciada para todos los dispositivos activos.");
            } else {
                $this->warn("No se encontraron dispositivos activos para sincronizar.");
            }
        }

        return Command::SUCCESS;
    }
}
