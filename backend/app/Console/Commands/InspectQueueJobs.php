<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class InspectQueueJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:inspect';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Muestra los trabajos pendientes en la cola y los últimos trabajos completados';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Trabajos pendientes en la tabla jobs
        $this->info('=== TRABAJOS PENDIENTES EN LA COLA ===');
        $pendingJobs = DB::table('jobs')->get();
        
        if ($pendingJobs->isEmpty()) {
            $this->warn('⚠ No hay trabajos en la cola');
        } else {
            $table = [];
            foreach ($pendingJobs as $job) {
                $payload = json_decode($job->payload, true);
                $table[] = [
                    'ID' => $job->id,
                    'Cola' => $job->queue,
                    'Job' => $payload['displayName'] ?? 'N/A',
                    'Intentos' => $job->attempts,
                    'Disponible en' => date('Y-m-d H:i:s', $job->available_at),
                ];
            }
            
            $this->table(['ID', 'Cola', 'Job', 'Intentos', 'Disponible en'], $table);
        }

        $this->newLine();

        // Trabajos fallidos
        $this->info('=== TRABAJOS FALLIDOS ===');
        $failedJobs = DB::table('failed_jobs')->orderBy('failed_at', 'desc')->limit(5)->get();
        
        if ($failedJobs->isEmpty()) {
            $this->info('✅ No hay trabajos fallidos');
        } else {
            $table = [];
            foreach ($failedJobs as $job) {
                $payload = json_decode($job->payload, true);
                $table[] = [
                    'ID' => $job->id,
                    'Cola' => $job->queue ?? 'N/A',
                    'Job' => $payload['displayName'] ?? 'N/A',
                    'Falló en' => $job->failed_at,
                    'Excepción (primeros 100 chars)' => substr($job->exception, 0, 100) . '...',
                ];
            }
            
            $this->table(['ID', 'Cola', 'Job', 'Falló en', 'Excepción'], $table);
        }

        return 0;
    }
}
