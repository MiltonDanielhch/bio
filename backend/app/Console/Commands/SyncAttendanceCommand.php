<?php

namespace App\Console\Commands;

use App\Jobs\SyncAttendanceJob;
use Illuminate\Console\Command;

class SyncAttendanceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zk:sync-attendance {ip : The IP address of the ZKTeco device}
                                               {--clear : Clear attendance log on device after syncing}
                                               {--password= : The communication key/password for the device}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync attendance records from a ZKTeco device and save them to the database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $ip = $this->argument('ip');
        $clear = $this->option('clear');
        // Usar la contraseña de la opción, o si no, la configurada por defecto.
        $password = $this->option('password') ?? config('services.zkservice.password');

        $this->info("Dispatching job to sync attendance from device: {$ip}");
        if ($clear) {
            $this->warn('The --clear flag is set. Records will be deleted from the device after sync.');
        }

        SyncAttendanceJob::dispatch($ip, $clear, $password);

        $this->info('Job dispatched successfully!');
        return Command::SUCCESS;
    }
}
