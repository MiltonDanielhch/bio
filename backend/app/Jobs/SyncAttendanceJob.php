<?php

namespace App\Jobs;

use App\Services\ZkService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncAttendanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $deviceIp;
    protected bool $clearAfterSync;
    protected ?string $password;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $deviceIp, bool $clearAfterSync = false, ?string $password = null)
    {
        $this->deviceIp = $deviceIp;
        $this->clearAfterSync = $clearAfterSync;
        $this->password = $password;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ZkService $zkService)
    {
        Log::info("Starting attendance sync for device: {$this->deviceIp}");

        $records = $zkService->getAttendance($this->deviceIp, $this->password);

        if (is_null($records)) {
            Log::error("Could not retrieve attendance records for {$this->deviceIp}. Aborting sync.");
            return;
        }

        if (empty($records)) {
            Log::info("No new attendance records to sync for {$this->deviceIp}.");
            return;
        }

        // Asumiendo una tabla 'attendance_records'
        DB::table('attendance_records')->insert($records);

        Log::info(count($records) . " records synced for device {$this->deviceIp}.");

        if ($this->clearAfterSync) {
            $zkService->clearAttendance($this->deviceIp, $this->password);
            Log::info("Attendance records cleared from device {$this->deviceIp}.");
        }
    }
}
